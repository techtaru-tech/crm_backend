<?php

namespace App\Http\Controllers;

use App\Events\FormSubmitted;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Models\FormSubmissionValue;
use App\Models\Lead;
use App\Models\Tenant;
use App\Services\LeadDuplicateDetector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicFormController extends Controller
{
    public function widget(int $formId): View|Response
    {
        // Public (guest) request: the BelongsToTenant global scope fails
        // closed when no tenant is resolved, so a plain Form::findOrFail
        // would 404 for every visitor (the embedded-form iframe never
        // loaded). Bypass the scope to locate the form, then bind its
        // tenant into context so all downstream lookups resolve correctly.
        $form = Form::withoutGlobalScope('tenant')->with('fields', 'tenant')->findOrFail($formId);
        $tenantRecord = $form->tenant;
        if (! $form->active || ! $tenantRecord || ! $tenantRecord->active) {
            abort(404);
        }
        app()->instance('current_tenant', $tenantRecord);
        $steps = $form->multi_step
            ? $form->fields->groupBy('step_number')
            : collect([1 => $form->fields]);

        return view('forms.show', compact('form', 'tenantRecord', 'steps'));
    }

    public function show(string $tenant, string $slug): View|Response
    {
        $tenantRecord = Tenant::where('slug', $tenant)->where('active', true)->firstOrFail();
        // Resolve the tenant context for this public (guest) request so the
        // fail-closed BelongsToTenant scope does not blank the Form lookup.
        app()->instance('current_tenant', $tenantRecord);
        $form = Form::where('tenant_id', $tenantRecord->id)
            ->where('slug', $slug)
            ->where('active', true)
            ->with('fields')
            ->firstOrFail();

        $steps = $form->multi_step
            ? $form->fields->groupBy('step_number')
            : collect([1 => $form->fields]);

        return view('forms.show', compact('form', 'tenantRecord', 'steps'));
    }

    public function submit(Request $request, string $tenant, string $slug): JsonResponse|RedirectResponse
    {
        $tenantRecord = Tenant::where('slug', $tenant)->where('active', true)->firstOrFail();
        // Resolve the tenant context up-front for this public (guest) submit
        // so the fail-closed BelongsToTenant scope does not break the Form
        // lookup, the duplicate-lead check, the submission->lead linkage, or
        // the lead/observer side effects further down.
        app()->instance('current_tenant', $tenantRecord);
        $form = Form::where('tenant_id', $tenantRecord->id)
            ->where('slug', $slug)
            ->where('active', true)
            ->with('fields')
            ->firstOrFail();

        if ($request->filled('__hp_email')) {
            return response()->json(['success' => true, 'spam' => true]);
        }

        if ($form->recaptcha_enabled && $form->recaptcha_secret_key) {
            $token = $request->input('g-recaptcha-response');
            if (! $token) {
                return response()->json(['success' => false, 'message' => __('messages.recaptcha_token_missing')], 422);
            }
            $recaptchaResponse = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $form->recaptcha_secret_key,
                'response' => $token,
                'remoteip' => $request->ip(),
            ])->json();

            if (! ($recaptchaResponse['success'] ?? false) || ($recaptchaResponse['score'] ?? 1) < 0.5) {
                return response()->json(['success' => false, 'message' => __('messages.recaptcha_spam_check_failed')], 422);
            }
        }

        $rules = [];
        foreach ($form->fields as $field) {
            if (in_array($field->type, ['divider'])) {
                continue;
            }
            $key = 'field_' . $field->id;
            $fieldRules = [];
            if ($field->required || $field->type === 'gdpr') {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            if ($field->type === 'email') {
                $fieldRules[] = 'email';
            } elseif ($field->type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($field->type === 'date') {
                $fieldRules[] = 'date';
            } elseif ($field->type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:10240';
                // Allowlist by client extension AND server-resolved MIME
                // so a hostile uploader cannot smuggle a `.php` payload
                // by either lying about the extension OR by sending a
                // matching mimetype with a script extension.  Without
                // this, a tenant who adds a file field exposes the
                // install to RCE if the local disk is ever moved to
                // public, aliased by the web server, or migrated to S3
                // with a CloudFront alias.
                $fieldRules[] = 'extensions:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip';
                $fieldRules[] = 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip';
            }
            $rules[$key] = $fieldRules;
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $consentedAt = null;
        $consentText = null;
        $gdprField   = $form->fields->firstWhere('type', 'gdpr');
        if ($gdprField && $request->input('field_' . $gdprField->id)) {
            $consentedAt = now();
            $consentText = $gdprField->label;
        }

        $submission = FormSubmission::create([
            'form_id'        => $form->id,
            'tenant_id'      => $tenantRecord->id,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'is_spam'        => false,
            'consented_at'   => $consentedAt,
            'consent_text'   => $consentText,
            'completed_step' => $request->integer('completed_step', $form->multi_step ? $form->fields->max('step_number') : 1),
        ]);

        $emailField     = $form->fields->firstWhere('field_key', 'email') ?? $form->fields->firstWhere('type', 'email');
        $phoneField     = $form->fields->firstWhere('field_key', 'phone') ?? $form->fields->firstWhere('type', 'phone');
        $firstNameField = $form->fields->firstWhere('field_key', 'first_name') ?? $form->fields->firstWhere('label', 'First Name');
        $lastNameField  = $form->fields->firstWhere('field_key', 'last_name') ?? $form->fields->firstWhere('label', 'Last Name');

        $email     = $emailField     ? $request->input('field_' . $emailField->id)     : null;
        $phone     = $phoneField     ? $request->input('field_' . $phoneField->id)     : null;
        $firstName = $firstNameField ? $request->input('field_' . $firstNameField->id) : null;
        $lastName  = $lastNameField  ? $request->input('field_' . $lastNameField->id)  : null;

        // UTM / referrer attribution (from POST body or query string)
        $utm = [
            'utm_source'   => $request->input('utm_source',   $request->query('utm_source')),
            'utm_medium'   => $request->input('utm_medium',   $request->query('utm_medium')),
            'utm_campaign' => $request->input('utm_campaign', $request->query('utm_campaign')),
            'utm_content'  => $request->input('utm_content',  $request->query('utm_content')),
            'utm_term'     => $request->input('utm_term',     $request->query('utm_term')),
            'landing_page' => $request->input('landing_page', null),
            'referrer_url' => $request->input('referrer_url', $request->header('Referer')),
        ];

        $existingLead = app(LeadDuplicateDetector::class)->findExisting(
            $tenantRecord->id,
            $email,
            $phone,
        );

        $lead = null;
        if (! $existingLead) {
            $lead = Lead::create(array_merge([
                'tenant_id'          => $tenantRecord->id,
                'source'             => 'web_form',
                'source_id'          => (string) $form->id,
                'form_id'            => $form->id,
                'first_name'         => $firstName,
                'last_name'          => $lastName,
                'email'              => $email,
                'phone'              => $phone,
                'status'             => 'new',
                'consented_at'       => $consentedAt,
                'consent_text'       => $consentText,
                'pipeline_id'        => $form->pipeline_id,
                'pipeline_stage_id'  => $form->pipeline_stage_id,
                'raw_data'           => $request->only(array_keys($rules)),
            ], array_filter($utm, fn($v) => $v !== null && $v !== '')));
            $submission->update(['lead_id' => $lead->id]);
        } else {
            $submission->update(['lead_id' => $existingLead->id]);
            if ($consentedAt) {
                $existingLead->update([
                    'consented_at' => $consentedAt,
                    'consent_text' => $consentText,
                ]);
            }
            $lead = $existingLead;
        }

        foreach ($form->fields as $field) {
            if (in_array($field->type, ['divider'])) {
                continue;
            }

            $value = null;
            if ($field->type === 'file') {
                $uploadedFile = $request->file('field_' . $field->id);
                if ($uploadedFile && $uploadedFile->isValid()) {
                    // Tenant-scoped upload path so two tenants whose
                    // form_id auto-increments collide (or whose names
                    // happen to hash identically) cannot read each
                    // other's uploads, and so the GDPR Article 17 purge
                    // sweep (which walks `tenants/{id}/...` and now
                    // `form-uploads/{tenant_id}/...`) finds them.
                    $storedPath = $uploadedFile->store(
                        'form-uploads/' . $form->tenant_id . '/' . $form->id,
                        'local'
                    );
                    $value = $storedPath;
                }
            } else {
                $value = $request->input('field_' . $field->id);
            }

            if ($value !== null) {
                FormSubmissionValue::create([
                    'form_submission_id' => $submission->id,
                    'form_field_id'      => $field->id,
                    'value'              => is_array($value) ? implode(', ', $value) : (string) $value,
                ]);
            }
        }

        event(new FormSubmitted($form, $submission, $lead));

        // API consumers (POST /api/forms/...) always get JSON, even when no
        // Accept header is present — they're machine clients, not browsers.
        // Web form posts honour the existing wantsJson() branch (XHR/fetch)
        // and otherwise redirect like before.
        if ($request->is('api/*') || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $form->thank_you_message ?? __('public_form.thank_you_short')]);
        }

        if ($form->redirect_url && $this->isSafeRedirect($form->redirect_url, $request)) {
            return redirect($form->redirect_url);
        }

        return back()->with('success', $form->thank_you_message ?? __('public_form.thank_you_full'));
    }

    /**
     * Open-redirect guard for `forms.redirect_url`.  A tenant (or anyone
     * who compromises a tenant account / mass-assigns the column) can
     * otherwise use the application's trusted form URL as a phishing
     * hop to redirect submitters to attacker-controlled domains.
     *
     * Fix note: delegate to {@see \App\Support\UrlSafety::isSafeRedirect}
     * — the prior inline implementation had a `javascript:` bypass
     * (`parse_url('javascript:x')['host']` is null, which the old code
     * treated as "relative URL, always safe").  The centralised helper
     * checks the scheme BEFORE the host and rejects anything outside
     * http/https.
     */
    private function isSafeRedirect(string $target, Request $request): bool
    {
        return \App\Support\UrlSafety::isSafeRedirect($target, $request);
    }
}
