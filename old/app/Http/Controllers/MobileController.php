<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\LeadDuplicateDetector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Mobile-first PWA entry points.
 *
 * These pages are deliberately stripped down — single column, no
 * Filament chrome — so they fit the PWA install experience and the
 * service worker can cache them as a usable offline shell.
 *
 * Capture:  manual quick-form for field reps between appointments.
 * Scan:     camera → OpenAI Vision → parsed lead (name/email/phone/company).
 * Offline:  shown when the device has no connectivity and the SW
 *           couldn't serve the requested /mobile/* shell.
 */
class MobileController extends Controller
{
    public function capture(Request $request)
    {
        $this->requireAuth($request);
        return view('mobile.capture', [
            'users' => User::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function scan(Request $request)
    {
        $this->requireAuth($request);
        return view('mobile.scan');
    }

    public function offline()
    {
        return view('mobile.offline');
    }

    /**
     * Accept a manual-capture form submission.
     *
     * The public form is behind auth; we still support a queued
     * offline replay (the SW sends `X-LeadHub-Queued: 1`) which
     * uses the same endpoint.  Duplicates are folded into the
     * existing lead.
     */
    public function storeCapture(Request $request): JsonResponse
    {
        $this->requireAuth($request);

        // Tenant-bind assigned_user_id — without the Rule::exists scope a
        // mobile client could pass a user id from another tenant in the
        // JSON body and bind that user as the lead's assignee.  Once
        // bound, the cross-tenant assignee shows up in /admin filters and
        // notifications fire to the wrong person's inbox.
        $tenantId = (int) (auth()->user()->tenant_id ?? 0);

        $data = $request->validate([
            'first_name' => 'nullable|string|max:120',
            'last_name'  => 'nullable|string|max:120',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:40',
            'company'    => 'nullable|string|max:150',
            'notes'      => 'nullable|string|max:2000',
            'source'     => 'nullable|string|max:40',
            'assigned_user_id' => [
                'nullable',
                'integer',
                \Illuminate\Validation\Rule::exists('users', 'id')
                    ->where('tenant_id', $tenantId),
            ],
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json(['ok' => false, 'error' => __('mobile.capture_error_email_or_phone_required')], 422);
        }

        $user = auth()->user();

        $existing = app(LeadDuplicateDetector::class)
            ->findExisting($user->tenant_id, $data['email'] ?? null, $data['phone'] ?? null);

        if ($existing) {
            return response()->json([
                'ok'       => true,
                'dedup'    => true,
                'lead_id'  => $existing->id,
                'message'  => __('mobile.capture_dedup_existing_lead'),
            ]);
        }

        $lead = Lead::create(array_filter([
            'tenant_id'        => $user->tenant_id,
            'source'           => $data['source'] ?? 'mobile_capture',
            'first_name'       => $data['first_name'] ?? null,
            'last_name'        => $data['last_name']  ?? null,
            'email'            => $data['email']      ?? null,
            'phone'            => $data['phone']      ?? null,
            'company'          => $data['company']    ?? null,
            'notes'            => $data['notes']      ?? null,
            'status'           => 'new',
            'assigned_user_id' => $data['assigned_user_id'] ?? $user->id,
        ], fn ($v) => $v !== null && $v !== ''));

        return response()->json([
            'ok'      => true,
            'lead_id' => $lead->id,
            'message' => __('mobile.capture_success_lead_captured'),
        ]);
    }

    /**
     * Business-card OCR via OpenAI Vision.
     *
     * Accepts a single uploaded image (<= 8 MB), sends it to the
     * configured OpenAI model as a base64 data URL, asks for a
     * strict JSON shape, and returns the parsed fields.  Falls back
     * gracefully if the API key isn't configured or the model
     * returns malformed JSON.
     */
    public function ocrBusinessCard(Request $request): JsonResponse
    {
        $this->requireAuth($request);

        $request->validate([
            'image' => ['required', 'file', 'image', 'max:8192'], // 8 MB
        ]);

        $key = config('services.openai.api_key');
        if (! $key) {
            return response()->json([
                'ok'    => false,
                'error' => __('mobile.ocr_error_openai_key_missing'),
            ], 503);
        }

        $file = $request->file('image');
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $b64  = base64_encode((string) file_get_contents($file->getRealPath()));

        // i18n: OCR prompt routed through translator so the AI
        // returns extracted free-text fields (job title, company name)
        // tagged in the buyer's locale.  Without this :locale instruction
        // GPT-4o-mini defaulted to English regardless of the workspace
        // locale, leaking English-named industries into translated
        // workspaces.
        $locale     = (string) app()->getLocale();
        $promptText = (string) __('mobile.ocr_business_card_prompt', ['locale' => $locale]);

        try {
            $resp = Http::withToken($key)
                ->timeout(40)
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => config('services.openai.vision_model', 'gpt-4o-mini'),
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages'    => [[
                        'role'    => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $promptText,
                            ],
                            [
                                'type'      => 'image_url',
                                'image_url' => ['url' => 'data:' . $mime . ';base64,' . $b64],
                            ],
                        ],
                    ]],
                ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => __('mobile.ocr_error_request_failed')], 502);
        }

        if (! $resp->successful()) {
            return response()->json(['ok' => false, 'error' => __('mobile.ocr_error_provider_status', ['status' => $resp->status()])], 502);
        }

        $content = $resp->json('choices.0.message.content');
        $parsed  = is_string($content) ? json_decode($content, true) : null;

        if (! is_array($parsed)) {
            return response()->json(['ok' => false, 'error' => __('mobile.ocr_error_parse_failed')], 422);
        }

        return response()->json([
            'ok'     => true,
            'fields' => [
                'first_name' => $parsed['first_name'] ?? null,
                'last_name'  => $parsed['last_name']  ?? null,
                'email'      => $parsed['email']      ?? null,
                'phone'      => $parsed['phone']      ?? null,
                'company'    => $parsed['company']    ?? null,
                'title'      => $parsed['title']      ?? null,
                'website'    => $parsed['website']    ?? null,
            ],
        ]);
    }

    protected function requireAuth(Request $request): void
    {
        if (! auth()->check()) {
            abort(redirect()->guest('/admin/login'));
        }
    }
}
