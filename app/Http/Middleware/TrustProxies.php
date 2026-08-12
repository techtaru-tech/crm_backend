<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * TrustProxies — controls which upstream load balancers /
 * reverse-proxies are allowed to set X-Forwarded-* headers
 * that Laravel reads to populate $request->ip(), ->getHost(),
 * ->isSecure(), etc.
 *
 * Default behaviour: trust NOTHING.  An empty TRUSTED_PROXIES
 * env value (the default for fresh CodeCanyon installs) means
 * Laravel ignores X-Forwarded-* entirely and uses the TCP peer
 * address — safe for direct-to-PHP installs and for buyers
 * who haven't read the deployment notes yet.
 *
 * Buyers behind Cloudflare / Nginx / a load balancer MUST opt
 * in by setting TRUSTED_PROXIES to the upstream proxy IPs (or
 * CIDR ranges).  The wildcard "*" trusts all proxies and should
 * only be used when the buyer is *certain* their PHP-FPM
 * socket is unreachable except via that proxy — otherwise an
 * attacker can spoof X-Forwarded-For and forge $request->ip(),
 * which the brute-force lockout, audit log, and SA IP allowlist
 * all rely on.
 *
 * Documented in .env.example under "Reverse Proxies".
 */
class TrustProxies extends Middleware
{
    /**
     * @var array<int,string>|string|null
     */
    protected $proxies;

    /**
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Laravel 13's parent TrustProxies has NO constructor — it relies
     * on class-property defaults plus static ::at()/::withHeaders()
     * configuration.  Calling parent::__construct() fatals with
     * "Cannot call constructor".  We therefore parse TRUSTED_PROXIES
     * env directly here and assign $this->proxies — which the parent
     * reads via its protected proxies() helper.
     */
    public function __construct()
    {
        $this->proxies = $this->parseTrustedProxiesEnv();
    }

    /**
     * @return array<int,string>|string
     */
    protected function parseTrustedProxiesEnv(): array|string
    {
        $raw = (string) ($_ENV['TRUSTED_PROXIES'] ?? env('TRUSTED_PROXIES', ''));

        if ($raw === '') {
            return [];
        }

        if (trim($raw) === '*') {
            // Symfony's TrustedProxies treats '*' specially as "trust
            // every proxy in the chain" — preserve that semantic.
            return '*';
        }

        $parts = preg_split('/[\s,]+/', $raw) ?: [];
        return array_values(array_filter(array_map('trim', $parts)));
    }
}
