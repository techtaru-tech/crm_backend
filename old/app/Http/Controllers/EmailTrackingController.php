<?php

namespace App\Http\Controllers;

use App\Models\LeadEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    /**
     * Tracking pixel endpoint. Always returns a 1x1 transparent GIF so
     * mail clients don't show a broken image.
     */
    public function open(string $token): Response
    {
        $email = $this->resolveByToken($token);

        if ($email && $email->opened_at === null) {
            $email->opened_at = now();
            $email->saveQuietly();
        }

        // 1x1 transparent GIF
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($gif, 200, [
            'Content-Type'  => 'image/gif',
            'Content-Length' => (string) strlen($gif),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
        ]);
    }

    /**
     * Click tracking endpoint — records click then redirects to `?to=`.
     *
     * Open-redirect defence: we only redirect when the `to` URL actually
     * appears inside the original email's HTML body.  Unknown URLs (the
     * attacker changing `?to=` after the fact) fall through to `/` so
     * the endpoint cannot be abused as a phishing redirector.
     */
    public function click(string $token, Request $request)
    {
        $email = $this->resolveByToken($token);

        if ($email && $email->clicked_at === null) {
            $email->clicked_at = now();
            $email->saveQuietly();
        }

        $to     = (string) $request->query('to', '');
        $parsed = $to !== '' ? parse_url($to) : false;
        $scheme = strtolower($parsed['scheme'] ?? '');

        $schemeValid = in_array($scheme, ['http', 'https'], true);
        $inBody      = $email && is_string($email->body_html)
            && str_contains($email->body_html, $to);

        if ($schemeValid && $inBody) {
            return redirect()->away($to);
        }

        return redirect('/');
    }

    /**
     * Look up a LeadEmail by its tracking token using a constant-time
     * comparison after the index lookup.
     *
     * The `tracking_token` column carries a unique index, so the database
     * already does the heavy lifting — but `hash_equals()` is layered on
     * as defence-in-depth so the secondary verification is timing-safe
     * even if a custom DB driver ever leaks comparison timing through
     * the index lookup.  Tokens that are empty or longer than the
     * 64-char column ceiling short-circuit before touching the DB.
     */
    private function resolveByToken(string $token): ?LeadEmail
    {
        $token = trim($token);

        if ($token === '' || strlen($token) > 64) {
            return null;
        }

        $email = LeadEmail::withoutGlobalScope('tenant')
            ->where('tracking_token', $token)
            ->first();

        if (! $email || ! is_string($email->tracking_token)) {
            return null;
        }

        return hash_equals($email->tracking_token, $token) ? $email : null;
    }
}
