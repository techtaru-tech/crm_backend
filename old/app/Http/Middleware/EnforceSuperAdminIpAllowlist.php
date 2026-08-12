<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts the Super Admin panel to a configured list of IPs / CIDR
 * ranges.  Off by default — empty allowlist means no restriction so
 * fresh CodeCanyon installs aren't accidentally bricked.  When the
 * buyer adds even one entry to SA_IP_ALLOWLIST the panel becomes
 * private to that IP set.
 *
 * Configuration:
 *   .env  SA_IP_ALLOWLIST="203.0.113.0/24,198.51.100.7"
 *   .env  SA_IP_ALLOWLIST_STRICT=false   (default — see below)
 *   config/auth.php
 *     'super_admin_ip_allowlist'        => env('SA_IP_ALLOWLIST', '')
 *     'super_admin_ip_allowlist_strict' => env('SA_IP_ALLOWLIST_STRICT', false)
 *
 * STRICT MODE OFF (DEFAULT, Cloudflare-friendly):
 *   Only the resolved client IP ($request->ip()) is checked against
 *   the allowlist.  Behind Cloudflare or any reverse proxy,
 *   TrustProxies is responsible for giving the middleware the real
 *   buyer IP (not the proxy IP).  The buyer puts their office IP in
 *   SA_IP_ALLOWLIST and Cloudflare's published ranges in
 *   TRUSTED_PROXIES.  No need to allowlist the entire proxy network.
 *
 * STRICT MODE ON (DIRECT-TO-PHP ONLY):
 *   Both $request->ip() AND the raw TCP peer ($_SERVER['REMOTE_ADDR'])
 *   must match the allowlist.  Use this only when there is NO proxy
 *   in front of the application — otherwise REMOTE_ADDR is the
 *   proxy's IP and the dual-check will fail every time.  Good for
 *   bare PHP-FPM behind nginx-on-the-same-host, fail2ban-protected
 *   installs, or HTTPS straight to the app server.
 *
 * The middleware uses Symfony's IpUtils to handle both single IPs
 * (IPv4 + IPv6) and CIDR ranges in a single check.
 *
 * 403s are intentionally minimal — we don't reveal whether the panel
 * exists at all, so an attacker scanning the install gets the same
 * response shape they'd get from any forbidden URL.
 */
class EnforceSuperAdminIpAllowlist
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowlist = $this->parseAllowlist();
        if (empty($allowlist)) {
            return $next($request);
        }

        $forwardedIp = $request->ip();
        $forwardedOk = $forwardedIp && IpUtils::checkIp($forwardedIp, $allowlist);

        if (! $this->strictMode()) {
            // Default: trust the TrustProxies-resolved client IP.
            // Buyers behind Cloudflare/Nginx MUST configure
            // TRUSTED_PROXIES so $request->ip() returns the real
            // client IP and not the proxy IP — see .env.example.
            if ($forwardedOk) {
                return $next($request);
            }

            abort(403, __('messages.forbidden_generic'));
        }

        // Strict mode: BOTH the resolved client IP AND the raw TCP
        // peer ($_SERVER['REMOTE_ADDR']) must match.  This is only
        // safe for direct-to-PHP installs — any proxy in front will
        // make REMOTE_ADDR the proxy's IP and the check will fail.
        $peerIp = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : null;
        $peerOk = $peerIp && IpUtils::checkIp($peerIp, $allowlist);

        if ($forwardedOk && $peerOk) {
            return $next($request);
        }

        abort(403, __('messages.forbidden_generic'));
    }

    /**
     * Strict mode flag — when true the legacy dual-check applies.
     * Defaults to false so the protection works behind Cloudflare /
     * Nginx / any reverse proxy out of the box.
     */
    protected function strictMode(): bool
    {
        return (bool) config('auth.super_admin_ip_allowlist_strict', false);
    }

    /**
     * @return string[] List of single IPs and/or CIDR ranges.
     */
    protected function parseAllowlist(): array
    {
        $raw = (string) config('auth.super_admin_ip_allowlist', '');
        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', preg_split('/[\s,]+/', $raw) ?: []),
            fn ($entry) => $entry !== '',
        ));
    }
}
