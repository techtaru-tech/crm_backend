<?php

declare(strict_types=1);

namespace App\Support;

/**
 * SSRF guard for outbound URLs supplied by tenants.
 *
 * Threat: tenants set `url=http://169.254.169.254/...` on an outbound
 * webhook (or a Slack action, or a Salesforce instance_url) and the
 * worker fetches it from inside the host's network — leaking IMDS
 * credentials, Redis state, internal admin endpoints, etc.
 *
 * Defence: before any HTTP call to a tenant-supplied URL, run it
 * through {@see assertSafe()}.  Rejects:
 *   - Schemes other than http/https
 *   - Hosts that resolve (or themselves are) RFC1918, loopback, link-
 *     local, or AWS/GCP/Azure metadata addresses
 *   - Hosts that fail DNS resolution entirely (defence-in-depth so
 *     downstream Http facades don't return surprising errors)
 *
 * Use this from background jobs and synchronous test-send actions.
 * Skip it for OAuth-redirect URLs that the user opens in a browser
 * (those reach the gateway from the user's network, not ours).
 */
final class UrlSafetyGuard
{
    /**
     * Block-list of CIDR ranges that should never be reachable by an
     * outbound HTTP call from the application server.  Each entry is
     * `[start, end]` as IP strings; comparisons run via ip2long().
     *
     * @var array<int, array{0:string, 1:string}>
     */
    private const BLOCKED_CIDRS = [
        // Loopback
        ['127.0.0.0',     '127.255.255.255'],
        // RFC1918 private networks
        ['10.0.0.0',      '10.255.255.255'],
        ['172.16.0.0',    '172.31.255.255'],
        ['192.168.0.0',   '192.168.255.255'],
        // Link-local + AWS/GCP/Azure metadata
        ['169.254.0.0',   '169.254.255.255'],
        // Carrier-grade NAT
        ['100.64.0.0',    '100.127.255.255'],
        // IETF protocol assignments
        ['192.0.0.0',     '192.0.0.255'],
        // TEST-NET
        ['192.0.2.0',     '192.0.2.255'],
        ['198.51.100.0',  '198.51.100.255'],
        ['203.0.113.0',   '203.0.113.255'],
        // Multicast + reserved
        ['224.0.0.0',     '255.255.255.255'],
        // 0.0.0.0
        ['0.0.0.0',       '0.255.255.255'],
    ];

    /**
     * Validate that the URL is safe for an outbound HTTP call.  Throws
     * UnsafeUrlException on any rejection so the caller can let the
     * exception bubble up to the queue's failed-jobs handler — same
     * shape Laravel uses for HTTP client errors.
     *
     * @throws \App\Support\UnsafeUrlException
     */
    public static function assertSafe(string $url): void
    {
        $url = trim($url);
        if ($url === '') {
            throw new UnsafeUrlException((string) __('support/url_safety.url_empty'));
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            throw new UnsafeUrlException((string) __('support/url_safety.url_missing_scheme_host'));
        }

        $scheme = strtolower($parts['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new UnsafeUrlException((string) __('support/url_safety.scheme_not_allowed'));
        }

        $host = strtolower($parts['host']);

        // If the host is itself an IP literal, validate it directly.
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            self::assertIpAllowed($host);
            return;
        }

        // Reject obvious local hostnames before we even resolve DNS.
        $localHosts = ['localhost', 'localhost.localdomain', 'ip6-localhost', 'metadata', 'metadata.google.internal'];
        if (in_array($host, $localHosts, true)) {
            throw new UnsafeUrlException((string) __('support/url_safety.host_not_routable', ['host' => $host]));
        }

        // Resolve and reject any A-record that lands in a blocked range.
        // Some hosts have multiple A records; if ANY record is in a
        // blocked range we treat the whole hostname as unsafe (DNS
        // rebinding mitigation — without this, an attacker can make
        // foo.example.com return 169.254.169.254 only on the second
        // resolution, after our preflight passed on the first).
        $records = @gethostbynamel($host);
        if ($records === false || $records === []) {
            throw new UnsafeUrlException((string) __('support/url_safety.host_unresolvable', ['host' => $host]));
        }

        foreach ($records as $ip) {
            self::assertIpAllowed($ip);
        }
    }

    /**
     * Convenience helper: returns true/false instead of throwing.  Use
     * when you want to silently skip a delivery rather than crash.
     */
    public static function isSafe(string $url): bool
    {
        try {
            self::assertSafe($url);
            return true;
        } catch (UnsafeUrlException) {
            return false;
        }
    }

    private static function assertIpAllowed(string $ip): void
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // IPv6 — for now we conservatively reject all non-IPv4
            // outbound; flipping this to a future allowlist is a
            // 1.0.x change once the buyer matrix demands it.
            throw new UnsafeUrlException((string) __('support/url_safety.ipv6_not_allowed'));
        }

        $ipLong = ip2long($ip);
        if ($ipLong === false) {
            throw new UnsafeUrlException((string) __('support/url_safety.ip_unparseable', ['ip' => $ip]));
        }

        foreach (self::BLOCKED_CIDRS as [$start, $end]) {
            if ($ipLong >= ip2long($start) && $ipLong <= ip2long($end)) {
                throw new UnsafeUrlException(
                    (string) __('support/url_safety.ip_in_blocked_range', ['ip' => $ip]),
                );
            }
        }
    }
}
