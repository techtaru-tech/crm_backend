<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects newly-created tenant owners to the onboarding wizard until
 * `tenants.settings.onboarding.completed_at` is set. Exempts super admins,
 * the onboarding page itself, and a short allow-list of routes that
 * always need to keep working (logout, profile, billing, etc.).
 */
class RedirectToOnboarding
{
    /**
     * Paths that must remain reachable even when onboarding is pending.
     * Filament `$request->is()` patterns.
     */
    private const EXEMPT_PATHS = [
        'admin/onboarding',
        'admin/onboarding/*',
        'admin/logout',
        'admin/login',
        'admin/profile',
        'admin/subscription-required',
        'admin/billing',
        'admin/billing/*',
        'admin/livewire/*',
        'livewire/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Don't hijack impersonation sessions — SA is driving.
        if (session()->has('impersonating_from')) {
            return $next($request);
        }

        $tenant = $user->tenant;
        if (! $tenant) {
            return $next($request);
        }

        // Only redirect tenants that explicitly need onboarding (pending flag
        // set on creation).  Legacy tenants without the flag are assumed
        // done — TenantOnboardingService::isPending defaults to false
        // when the bucket is empty.
        $svc = app(\App\Services\TenantOnboardingService::class);
        if (! $svc->isPending($tenant) || $svc->isCompleted($tenant)) {
            return $next($request);
        }

        foreach (self::EXEMPT_PATHS as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // AJAX / Livewire fetches should not receive a redirect HTML body.
        if ($request->expectsJson()) {
            return $next($request);
        }

        return redirect('/admin/onboarding');
    }
}
