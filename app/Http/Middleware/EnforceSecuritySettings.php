<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantSecurityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces per-tenant security settings at request time:
 * - IP whitelist check (blocks requests from non-whitelisted IPs — 403)
 * - Session lifetime enforcement via config override
 *
 * Per-tenant values are pulled via TenantSecurityService, which falls
 * back to app-global defaults when the tenant hasn't overridden a value.
 * Rate limiting / lockout thresholds are enforced in the login throttle
 * middleware; 2FA enforcement is in Enforce2Fa.
 */
class EnforceSecuritySettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant();
        if ($tenant === null) {
            return $next($request);
        }

        $security = app(TenantSecurityService::class)->all($tenant);

        $this->enforceIpWhitelist($request, $security['ip_whitelist'] ?? []);
        $this->applySessionLifetime((int) ($security['session_lifetime'] ?? 0));

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

    private function enforceIpWhitelist(Request $request, array $whitelist): void
    {
        if (empty($whitelist)) {
            return;
        }

        $clientIp = $request->ip();
        foreach ($whitelist as $allowed) {
            if ($allowed === $clientIp || $this->ipInRange($clientIp, $allowed)) {
                return;
            }
        }

        abort(403, __('messages.access_denied_ip_not_whitelisted'));
    }

    private function applySessionLifetime(int $lifetime): void
    {
        if ($lifetime > 0) {
            config(['session.lifetime' => $lifetime]);
        }
    }

    private function ipInRange(string $ip, string $range): bool
    {
        if (! str_contains($range, '/')) {
            return false;
        }

        [$subnet, $bits] = explode('/', $range, 2);
        $bits    = (int) $bits;
        $ipLong  = ip2long($ip);
        $subLong = ip2long($subnet);

        if ($ipLong === false || $subLong === false) {
            return false;
        }

        $mask = $bits > 0 ? ~((1 << (32 - $bits)) - 1) : 0;
        return ($ipLong & $mask) === ($subLong & $mask);
    }
}
