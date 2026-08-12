<?php

namespace App\Http\Controllers;

use App\Mail\TenantWelcomeMail;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Public self-service tenant registration.
 *
 * When enabled via config('leadhub.registration.enabled'), visitors can
 * create a workspace and a tenant admin account in a single form,
 * receiving a free trial automatically.
 *
 * When disabled, all routes 404 so self-hosted installs can keep the
 * SA-only provisioning flow.
 */
class RegistrationController extends Controller
{
    public function show(Request $request)
    {
        abort_unless(config('leadhub.registration.enabled', true), 404);

        if ($this->isTenantHost($request)) {
            return redirect('/admin');
        }

        if (Auth::check()) {
            return redirect('/admin');
        }

        // Capture referral code from URL and persist in session for the POST.
        if ($ref = $request->query('ref')) {
            $request->session()->put('referral_code', strtoupper(substr((string) $ref, 0, 32)));
        }

        // Resolve the two operator-configurable legal URLs from
        // LandingContent, falling back to the /pages/terms and
        // /pages/privacy static pages the StaticPagesSeeder creates.
        $landing = app(\App\Settings\LandingContent::class);
        $termsUrl   = trim((string) ($landing->register_terms_url   ?? '')) ?: '/pages/terms';
        $privacyUrl = trim((string) ($landing->register_privacy_url ?? '')) ?: '/pages/privacy';

        $recaptcha = app(\App\Services\RecaptchaService::class);

        return view('marketing.register', [
            'appName'         => config('leadhub.branding.app_name', 'LeadHub'),
            'trialDays'       => (int) config('plans.trial_days', 14),
            'selectedPlan'    => $request->query('plan'),
            'canSignUp'       => true,
            'availablePlans'  => app(\App\Services\PlanService::class)->getAllPlans(publicOnly: true),
            'referralCode'    => $request->session()->get('referral_code'),
            'termsUrl'        => $termsUrl,
            'privacyUrl'      => $privacyUrl,
            // reCAPTCHA site script + the form hidden input the
            // script populates.  The register blade includes the
            // script tag via {!! $recaptchaScript !!} and a hidden
            // input name="g-recaptcha-response".  If guard is off
            // these are empty strings — zero-cost no-ops.
            'recaptchaScript' => (\App\Settings\RecaptchaSettings::resolveSafe()?->shouldGuardRegister() ?? false)
                ? $recaptcha->scriptFor('register')
                : '',
            'recaptchaActive' => (\App\Settings\RecaptchaSettings::resolveSafe()?->shouldGuardRegister() ?? false),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(config('leadhub.registration.enabled', true), 404);

        if ($this->isTenantHost($request)) {
            return redirect('/admin');
        }

        // Throttle: 5 sign-ups per hour per IP — prevents spam/abuse
        $throttleKey = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => __('messages.registration_throttled', ['seconds' => $seconds])]);
        }
        RateLimiter::hit($throttleKey, 3600);

        // reCAPTCHA v3 gate — only runs when SA has it enabled AND
        // the register guard is on in RecaptchaSettings.  Verifies
        // the g-recaptcha-response token submitted by the page JS
        // against Google's siteverify endpoint and scores it.
        // Fails closed (rejects) if Google is unreachable.
        $recaptcha = app(\App\Services\RecaptchaService::class);
        if ((\App\Settings\RecaptchaSettings::resolveSafe()?->shouldGuardRegister() ?? false)) {
            $ok = $recaptcha->verify(
                $request->input('g-recaptcha-response'),
                'register',
                $request->ip(),
            );
            if (! $ok) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['email' => __('auth_flow.recaptcha.verification_failed')]);
            }
        }

        $validated = $request->validate([
            'workspace_name' => [
                'required', 'string', 'min:2', 'max:100',
                // Reject workspace names whose generated slug would
                // collide with a reserved path segment (admin,
                // super-admin, pages, docs, etc.) — otherwise the
                // /{workspace}/{slug} landing-page route would
                // shadow or be shadowed by app routes.
                function (string $attribute, $value, \Closure $fail) {
                    $candidate = \Illuminate\Support\Str::slug((string) $value);
                    if (\App\Support\ReservedSlugs::isReserved($candidate)) {
                        $fail(__('controllers/registration.reserved_workspace_url', ['value' => $value]));
                    }
                },
            ],
            'name'           => ['required', 'string', 'min:2', 'max:100'],
            'email'          => ['required', 'email:rfc,dns', 'max:150', Rule::unique('users', 'email')],
            'password'       => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms'          => ['accepted'],
            'plan'           => ['nullable', 'string'],
            'coupon'         => ['nullable', 'string', 'max:64'],
        ], [
            'terms.accepted'  => __('controllers/registration.must_accept_terms'),
            'email.unique'    => __('controllers/registration.account_exists'),
        ]);

        // Pick plan first so trial-days resolution can prefer the
        // per-plan trial_days (set in PlanResource) over the
        // BillingSettings global default.  Without this ordering,
        // self-service registration ignored per-plan trial lengths
        // even though the SA-create-workspace path honoured them.
        $planKey   = $this->pickInitialPlan($validated['plan'] ?? null);
        $trialDays = app(\App\Services\PlanService::class)->resolveTrialDays($planKey);

        // Resolve referral (query > session) — look up referring tenant if any.
        $refCode = strtoupper((string) ($request->query('ref') ?? $request->session()->get('referral_code', '')));
        $referredBy = null;
        if ($refCode !== '') {
            $referredBy = Tenant::where('referral_code', $refCode)->value('id');
        }

        // Pre-resolve coupon code so the transaction closure can
        // redeem trial-extension coupons atomically with tenant/user
        // creation.  Discount coupons stash in session AFTER the
        // transaction commits so a redeem failure can't leave a
        // tenant without their pending coupon.
        $couponCode = trim((string) ($validated['coupon'] ?? ''));
        $deferredCouponCode = null;     // set inside transaction for post-commit session stash
        $deferredCouponWarning = null;  // set inside transaction for post-commit flash

        $tenant = DB::transaction(function () use ($validated, $planKey, $trialDays, $referredBy, $couponCode, &$deferredCouponCode, &$deferredCouponWarning) {
            // Reuse the same base for slug and subdomain so both
            // resolve to identical workspace identifiers — mirrors
            // the Super Admin tenant-create form.
            $slug = $this->uniqueSlug($validated['workspace_name']);
            $tenant = Tenant::create([
                'name'                  => $validated['workspace_name'],
                'slug'                  => $slug,
                'subdomain'             => $this->uniqueSubdomain($slug),
                'plan'                  => $planKey,
                'subscription_status'   => 'trial',
                'trial_ends_at'         => now()->addDays($trialDays),
                'max_seats'             => 3,
                'seat_count'            => 1,
                'active'                => true,
                'referred_by_tenant_id' => $referredBy,
                'settings'              => [
                    'onboarding' => [
                        'pending'    => true,
                        'started_at' => now()->toIso8601String(),
                    ],
                ],
            ]);

            $user = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($validated['password']),
                'tenant_id'         => $tenant->id,
                'email_verified_at' => now(),
                'is_super_admin'    => false,
            ]);

            $user->tenants()->attach($tenant->id, ['role' => 'admin']);
            // Spatie role — without this, /admin/users shows no
            // badge on the owner and permission checks fall
            // through to the default-deny path.
            $user->syncRoles(['admin']);

            $tenant->update(['owner_id' => $user->id]);

            // Trial-extension coupon redemption runs INSIDE the
            // transaction so a redeem failure rolls back the tenant
            // + user creation — otherwise we'd end up with a tenant
            // whose coupon.times_used was never incremented.
            // Discount coupons only stash in session, so they are
            // handled outside this closure (post-commit).
            if ($couponCode !== '') {
                try {
                    $planRow = \App\Models\Plan::query()->where('key', $planKey)->first();
                    $check = app(\App\Services\CouponService::class)->validate(
                        code: $couponCode,
                        tenant: $tenant,
                        planKey: $planKey,
                        basePrice: $planRow ? (float) $planRow->price : 0.0,
                        currency: $planRow?->currency,
                    );

                    if ($check['valid']) {
                        $coupon = $check['coupon'];
                        if ($coupon->discount_type === \App\Models\Coupon::TYPE_TRIAL_EXTENSION) {
                            // Trial-extension coupons apply right now —
                            // no checkout needed, the trial just gets longer.
                            app(\App\Services\CouponService::class)->redeem(
                                coupon: $coupon,
                                tenant: $tenant,
                                planKey: $planKey,
                                basePrice: $planRow ? (float) $planRow->price : 0.0,
                                currency: (string) ($planRow?->currency ?? 'USD'),
                                metadata: ['source' => 'registration.trial_extension'],
                            );
                        } else {
                            // Discount coupons can only be redeemed at
                            // checkout (the gateway needs to know the
                            // discounted price).  Defer the session stash
                            // until after the transaction commits.
                            $deferredCouponCode = $coupon->code;
                        }
                    } else {
                        $deferredCouponWarning = __('auth_flow.coupon.prefix') . ($check['reason_label'] ?? __('auth_flow.coupon.invalid_code'));
                    }
                } catch (\Throwable $e) {
                    // Trial-extension throws abort the transaction so
                    // we don't leave a half-applied coupon row.
                    Log::warning('Registration coupon application failed: ' . $e->getMessage());
                    throw $e;
                }
            }

            return $tenant->fresh('owner');
        });

        // Post-commit: stash discount coupons (session-only, no DB
        // writes) and surface any validation warning.
        if ($deferredCouponCode !== null) {
            $request->session()->put('pending_coupon_code', $deferredCouponCode);
        }
        if ($deferredCouponWarning !== null) {
            $request->session()->flash('coupon_warning', $deferredCouponWarning);
        }

        // Audit trail — outside transaction so failures don't roll it back
        try {
            AuditLog::record(
                'tenant.self_registered',
                $tenant,
                [],
                [
                    'tenant_name' => $tenant->name,
                    'admin_email' => $validated['email'],
                    'plan'        => $planKey,
                    'trial_days'  => $trialDays,
                    'coupon'      => $couponCode !== '' ? $couponCode : null,
                    'ip'          => $request->ip(),
                ],
                'registration'
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to write registration audit log: ' . $e->getMessage());
        }

        // Consume referral code so it isn't carried into another signup.
        $request->session()->forget('referral_code');

        // Log them straight in — no verification wall for trial users
        Auth::login($tenant->owner);
        $request->session()->regenerate();

        // Welcome email (sync — see TenantWelcomeMail class comment
        // for why we don't queue).  User chose their own password
        // during sign-up, so we send a plain "workspace is ready"
        // message with no setup link and no password reference.
        try {
            Mail::to($validated['email'])->send(
                new TenantWelcomeMail($tenant, $validated['email'], null, true)
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to send self-registration welcome email: ' . $e->getMessage());
        }

        // Surface the actual workspace slug — useful when a
        // duplicate-name collision caused the backend to append a
        // "-N" suffix.  Session flash key is read by the onboarding
        // welcome banner to show "Your workspace is live at /acme-2".
        $expectedSlug = Str::slug($validated['workspace_name']);
        $actualSlug   = $tenant->slug;

        $request->session()->flash('workspace_url', url('/' . $actualSlug));
        if ($expectedSlug !== '' && $actualSlug !== $expectedSlug) {
            $request->session()->flash('workspace_slug_adjusted', [
                'requested' => $expectedSlug,
                'assigned'  => $actualSlug,
            ]);
        }

        return redirect('/admin')->with('welcome', true);
    }

    protected function pickInitialPlan(?string $requested): string
    {
        $default = config('plans.default', 'trial');

        if (! $requested) {
            return $default;
        }

        $available = array_keys(config('plans.plans', []));
        return in_array($requested, $available, true) ? $requested : $default;
    }

    /**
     * Resolve the effective trial length.  Checks BillingSettings
     * first (SA-editable), falls back to config('plans.trial_days')
     * so pre-settings-migration installs still work.  Minimum 1.
     */
    protected static function resolveTrialDays(): int
    {
        try {
            $settings = app(\App\Settings\BillingSettings::class);
            $days = (int) ($settings->trial_days ?? 0);
        } catch (\Throwable) {
            $days = 0;
        }
        if ($days < 1) {
            $days = (int) config('plans.trial_days', 14);
        }
        return max(1, $days);
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'workspace';
        $slug = $base;
        $i    = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /**
     * Generate a DNS-safe subdomain, unique across tenants.  Seeded
     * with the already-unique slug so collisions from a shared base
     * (e.g. two "Acme" workspaces registering back-to-back) still
     * resolve to distinct subdomains.
     */
    protected function uniqueSubdomain(string $seed): string
    {
        $base = Str::slug($seed) ?: 'workspace';
        $sub  = $base;
        $i    = 1;
        while (Tenant::where('subdomain', $sub)->exists()) {
            $sub = $base . '-' . $i++;
        }
        return $sub;
    }

    protected function isTenantHost(Request $request): bool
    {
        $host      = $request->getHost();
        $appDomain = config('leadhub.domain', parse_url(config('app.url'), PHP_URL_HOST));

        if (! $appDomain || $host === $appDomain) {
            return false;
        }
        return true;
    }
}
