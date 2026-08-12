<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\Lead;
use App\Services\LeadDuplicateDetector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PublicLandingPageController extends Controller
{
    /**
     * Render a published landing page at /{workspace}/{slug}.  Tenant is
     * resolved from the workspace path segment — this replaces the prior
     * subdomain-based resolution so every tenant lives at a clean
     * shared-domain URL.
     */
    public function show(string $workspace, string $slug): View|Response
    {
        $tenant = $this->resolveTenantFromWorkspace($workspace);
        $page   = $this->resolvePage($slug, $tenant);

        if (! $page) {
            abort(404);
        }

        // Race-safe view counter (does not trigger updated_at).
        DB::table('landing_pages')->where('id', $page->id)->increment('views_count');

        $sections = $page->sections()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        return view('public.landing.show', [
            'page'     => $page,
            'sections' => $sections,
        ]);
    }

    /**
     * Legacy /p/{slug} entry point.  Looks up the tenant that owns the
     * page (by page slug + staff-preview `?t=` hint for pre-architecture
     * bookmarks) and 301-redirects to the new /{workspace}/{slug} URL.
     * Pure bounce: no content served here so old SEO inbound links
     * consolidate naturally to the new canonical URL.
     */
    public function showLegacy(string $slug, Request $request): RedirectResponse|Response
    {
        $page = $this->resolvePage($slug);

        if (! $page || ! $page->tenant?->slug) {
            abort(404);
        }

        $query = $request->query();
        $target = url('/' . $page->tenant->slug . '/' . $page->slug);
        if (! empty($query)) {
            $target .= '?' . http_build_query($query);
        }

        return redirect($target, 301);
    }

    /**
     * Handle form submission for the lead-capture fallback on a
     * landing page at /{workspace}/{slug}/submit.  Legacy
     * /p/{slug}/submit routes through submitLegacy() below.
     *
     * If the page is linked to a Form resource, we redirect to its
     * submit URL so the existing PublicFormController handles the
     * full pipeline.
     */
    public function submit(string $workspace, string $slug, Request $request): RedirectResponse|Response
    {
        $tenant = $this->resolveTenantFromWorkspace($workspace);
        return $this->runSubmit($slug, $tenant, $request);
    }

    /**
     * Legacy /p/{slug}/submit entry point — same plumbing as
     * submit() but skips the explicit-tenant resolution so
     * resolvePage() falls back to host/auth-based lookup.  Kept so
     * pages embedded on external sites don't 404 after the route
     * rewrite.
     */
    public function submitLegacy(string $slug, Request $request): RedirectResponse|Response
    {
        return $this->runSubmit($slug, null, $request);
    }

    protected function runSubmit(string $slug, ?\App\Models\Tenant $tenant, Request $request): RedirectResponse|Response
    {
        $page = $this->resolvePage($slug, $tenant);

        if (! $page) {
            abort(404);
        }

        // Delegate to existing Form handler when page has a linked form.
        if ($page->form_id && $page->form) {
            return redirect()->route('forms.submit', [
                'tenant' => $page->tenant?->slug ?? $page->tenant_id,
                'slug'   => $page->form->slug,
            ])->withInput();
        }

        // Honeypot (silent).
        if ($request->filled('__hp_email')) {
            return $this->successRedirect($page);
        }

        $validator = Validator::make($request->all(), [
            'name'  => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        $firstName = null;
        $lastName  = null;
        if (! empty($data['name'])) {
            $parts     = preg_split('/\s+/', trim($data['name']), 2);
            $firstName = $parts[0] ?? null;
            $lastName  = $parts[1] ?? null;
        }

        $utm = [
            'utm_source'   => $request->input('utm_source',   $request->query('utm_source')),
            'utm_medium'   => $request->input('utm_medium',   $request->query('utm_medium')),
            'utm_campaign' => $request->input('utm_campaign', $request->query('utm_campaign')),
            'utm_content'  => $request->input('utm_content',  $request->query('utm_content')),
            'utm_term'     => $request->input('utm_term',     $request->query('utm_term')),
            'landing_page' => url()->current(),
            'referrer_url' => $request->header('Referer'),
        ];

        $existing = app(LeadDuplicateDetector::class)->findExisting(
            $page->tenant_id,
            $data['email'] ?? null,
            $data['phone'] ?? null,
        );

        if (! $existing) {
            Lead::withoutGlobalScope('tenant')->create(array_merge([
                'tenant_id'         => $page->tenant_id,
                'source'            => 'landing_page',
                'source_id'         => (string) $page->id,
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $data['email'] ?? null,
                'phone'             => $data['phone'] ?? null,
                'status'            => 'new',
                'pipeline_id'       => $page->pipeline_id,
                'pipeline_stage_id' => $page->pipeline_stage_id,
                'raw_data'          => $request->except(['_token', '__hp_email']),
            ], array_filter($utm, fn($v) => $v !== null && $v !== '')));
        }

        DB::table('landing_pages')->where('id', $page->id)->increment('conversions_count');

        return $this->successRedirect($page);
    }

    /**
     * Resolve a tenant by the workspace slug segment of the new
     * /{workspace}/{slug} URL pattern.  Returns null on miss so the
     * caller can 404.
     */
    protected function resolveTenantFromWorkspace(string $workspace): ?\App\Models\Tenant
    {
        return \App\Models\Tenant::where('slug', $workspace)
            ->where('active', true)
            ->first();
    }

    protected function resolvePage(string $slug, ?\App\Models\Tenant $explicitTenant = null): ?LandingPage
    {
        // Three possible tenant sources, in priority order:
        //  1. Explicit tenant passed in from the new /{workspace}/{slug}
        //     route (the typical hot path).
        //  2. Middleware-bound tenant (subdomain/custom-domain resolution,
        //     kept for any tenant that still has a subdomain record).
        //  3. Authenticated-staff `?t=<tenant>` preview hint, only when
        //     the viewer has access to that tenant.
        $tenant = $explicitTenant
            ?? (app()->bound('current_tenant') ? app('current_tenant') : null);
        $isStaffPreview = false;

        // Preview fallback: a Filament admin clicked "Preview" on a page
        // from the main app domain. Accept `?t=<tenant_id>` only when the
        // viewer is an authenticated user with access to that tenant
        // (super admin, or same-tenant staff). This is the ONLY way ?t=
        // influences tenant resolution — public visitors cannot spoof it.
        if (! $tenant && auth()->check() && request()->filled('t')) {
            $hintedId = (int) request()->input('t');
            $user     = auth()->user();

            $canPreview = $user->is_super_admin
                || (int) $user->tenant_id === $hintedId;

            if ($canPreview) {
                $tenant = \App\Models\Tenant::find($hintedId);
                $isStaffPreview = (bool) $tenant;
            }
        }

        if (! $tenant) {
            return null;
        }

        // Second preview path: the admin is already on their tenant
        // subdomain (ResolveTenant bound the tenant from the host) and
        // clicks Preview on a draft.  Enable staff preview when the
        // authenticated user belongs to (or super-admins) the resolved
        // tenant AND the explicit `?preview=1` query is present — the
        // query flag keeps us from accidentally revealing drafts on
        // every admin page-view.
        if (! $isStaffPreview && auth()->check() && request()->boolean('preview')) {
            $user = auth()->user();
            if ($user->is_super_admin || (int) $user->tenant_id === (int) $tenant->id) {
                $isStaffPreview = true;
            }
        }

        $query = LandingPage::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->with(['sections', 'form', 'tenant']);

        // Public visitors see only published pages; authenticated staff
        // previews can also see drafts + paused so they can iterate
        // before publishing.
        if (! $isStaffPreview) {
            $query->where('status', 'published');
        }

        return $query->first();
    }

    protected function successRedirect(LandingPage $page): RedirectResponse
    {
        // Fix note: $page->redirect_on_submit is tenant-admin authored.
        // Without scheme + host validation this was an OPEN-REDIRECT
        // (phishing — redirect submitters to an attacker domain that
        // mimics the form's brand) AND a `javascript:`-scheme XSS vector
        // (older browsers + embedded webviews execute `Location:
        // javascript:...` headers).  Route through UrlSafety::isSafeRedirect
        // which enforces:
        //   - http/https only (no javascript:, data:, vbscript:, file:)
        //   - host must match APP_URL or current request host
        //   - protocol-relative URLs (`//evil.com/...`) rejected
        if ($page->redirect_on_submit && \App\Support\UrlSafety::isSafeRedirect($page->redirect_on_submit, request())) {
            return redirect($page->redirect_on_submit);
        }
        return back()->with('landing_success', true);
    }
}
