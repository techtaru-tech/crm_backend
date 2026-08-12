<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Standard security response headers applied to every web response.
 *
 *   X-Frame-Options: SAMEORIGIN
 *     Blocks click-jacking — the browser refuses to render the
 *     page inside a third-party iframe.  An attacker can't overlay-
 *     trick a logged-in operator on /admin or /super-admin into
 *     clicking destructive actions through an embed.
 *
 *   X-Content-Type-Options: nosniff
 *     Stops MIME-sniffing.  Prevents a `text/plain` upload with
 *     embedded JavaScript from being interpreted as a script when
 *     served from the same origin.
 *
 *   Referrer-Policy: strict-origin-when-cross-origin
 *     Trims the URL path off the Referer header for cross-origin
 *     navigations — internal admin URLs (e.g.
 *     /admin/leads/12345) don't leak into third-party analytics
 *     when an operator clicks an outbound link.
 *
 * The form-widget embeddable surface (resources/views/forms/show.blade.php)
 * legitimately needs to render in a third-party iframe.  Its route
 * opts out via per-route `->withoutMiddleware(SecurityHeaders::class)`.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }
        if (! $response->headers->has('X-Content-Type-Options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }
        if (! $response->headers->has('Referrer-Policy')) {
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        // Additional headers:

        // Strict-Transport-Security — force HTTPS for 1 year + subdomains.
        // Only emitted on secure connections in production so local HTTP
        // dev still works.  Operators upgrading to HTTPS need only flip
        // the config once; future visits then refuse to downgrade.
        if ($request->secure()
            && app()->environment('production')
            && ! $response->headers->has('Strict-Transport-Security')
        ) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // Permissions-Policy — deny browser capabilities the CRM admin
        // panel doesn't need.  Reduces blast radius if a future XSS
        // does land: the injected script can't request camera /
        // microphone / geolocation / payment-request / USB / etc.
        // permissions on behalf of the operator.
        if (! $response->headers->has('Permissions-Policy')) {
            $response->headers->set(
                'Permissions-Policy',
                'accelerometer=(), autoplay=(), camera=(), display-capture=(), '
                . 'encrypted-media=(), fullscreen=(self), geolocation=(), '
                . 'gyroscope=(), magnetometer=(), microphone=(), midi=(), '
                . 'payment=(), picture-in-picture=(), publickey-credentials-get=(), '
                . 'screen-wake-lock=(), sync-xhr=(), usb=(), web-share=(), '
                . 'xr-spatial-tracking=()'
            );
        }

        // X-Permitted-Cross-Domain-Policies — denies Flash / Adobe
        // Reader plugins from loading cross-domain policy files from
        // our origin.  Legacy Flash is dead but plugin-based browsers
        // (and some PDF readers) still honour the header; cheap belt-
        // and-suspenders defense against cross-domain data theft.
        if (! $response->headers->has('X-Permitted-Cross-Domain-Policies')) {
            $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        }

        return $response;
    }
}
