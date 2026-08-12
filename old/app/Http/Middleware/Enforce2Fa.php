<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantSecurityService;
use App\Settings\SecuritySettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects authenticated users to the 2FA setup page when:
 *  - the current tenant has `enforce_2fa = true` (tenant /admin panel), OR
 *  - there is no current tenant but the system-wide
 *    `SecuritySettings.enforce_2fa` flag is on (Super Admin /super-admin
 *    panel)
 *
 * AND the user has not yet enabled two-factor authentication.
 *
 * Bypasses itself for 2FA setup/challenge/logout routes on either panel
 * to prevent redirect loops.  The setup page lives under the tenant
 * panel namespace (`filament.admin.pages.two-factor-setup`); SA users
 * have access there because they share the same `web` guard.
 */
class Enforce2Fa
{
    private const BYPASS_ROUTES = [
        // Tenant panel auth + setup
        'filament.admin.auth.two-factor.challenge',
        'filament.admin.auth.two-factor.login',
        'filament.admin.auth.logout',
        'filament.admin.auth.*',
        // The setup page itself — otherwise users loop on redirect.
        'filament.admin.pages.two-factor-setup',
        // Super Admin panel auth — must bypass so the SA login form
        // itself can render before the user has authenticated.  Logout
        // bypass prevents a 2FA-enforced SA from getting trapped if
        // they want to sign out without enrolling first.
        'filament.super-admin.auth.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $tenant     = $this->resolveTenant();
        $enforce2fa = $tenant !== null
            ? (bool) app(TenantSecurityService::class)->get($tenant, 'enforce_2fa', false)
            : $this->globalEnforce2Fa();

        if (! $enforce2fa) {
            return $next($request);
        }

        $user = auth()->user();

        if (method_exists($user, 'hasEnabledTwoFactorAuthentication') && ! $user->hasEnabledTwoFactorAuthentication()) {
            if ($this->isOnBypassRoute($request)) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('messages.two_factor_required'),
                ], 403);
            }

            session()->flash('status', __('middleware.enforce_2fa.required'));

            // Redirect target: the TwoFactorSetup page (renders the QR).
            // Was `filament.admin.pages.profile` — that route never existed
            // (real name is .profile-page) so every enforced user hit a
            // RouteNotFoundException instead of the enrollment screen.
            // The page lives on the tenant panel; SA users can reach it
            // because they share the same `web` guard.
            return redirect()->route('filament.admin.pages.two-factor-setup');
        }

        return $next($request);
    }

    private function resolveTenant(): ?Tenant
    {
        if (! app()->bound('current_tenant')) {
            return null;
        }

        $resolved = app('current_tenant');

        return $resolved instanceof Tenant ? $resolved : null;
    }

    /**
     * Read the system-wide enforce_2fa flag from Spatie SecuritySettings.
     * Wrapped in try/catch so a missing settings table during install
     * never trips the SA login flow.
     */
    private function globalEnforce2Fa(): bool
    {
        try {
            return (bool) app(SecuritySettings::class)->enforce_2fa;
        } catch (\Throwable) {
            return false;
        }
    }

    private function isOnBypassRoute(Request $request): bool
    {
        foreach (self::BYPASS_ROUTES as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }

        $uri = $request->path();
        return str_contains($uri, 'two-factor') || str_contains($uri, 'logout');
    }
}
