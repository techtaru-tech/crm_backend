<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Mail\PortalMagicLinkMail;
use App\Models\Lead;
use App\Models\PortalAccessToken;
use App\Models\PortalSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Public customer portal authentication — magic-link style.
 *
 * A Lead enters their email; if we find them in any tenant we email
 * them a 30-minute one-time token that exchanges for a 7-day portal
 * session cookie.  No password required — the lead doesn't have one.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('portal.login');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));

        // Find the lead across ALL tenants (public endpoint, no tenant context).
        // If multiple match, pick the most recently updated one.
        $lead = Lead::withoutGlobalScope('tenant')
            ->where('email', $email)
            ->latest('updated_at')
            ->first();

        // Always show the same success page to avoid user-enumeration.
        if ($lead) {
            $token = PortalAccessToken::create([
                'lead_id'    => $lead->id,
                'token'      => Str::random(64),
                'expires_at' => now()->addMinutes(30),
                'ip_address' => $request->ip(),
            ]);

            try {
                Mail::to($email)->send(new PortalMagicLinkMail($lead, $token->token));
            } catch (\Throwable) {
                // Swallow mail errors — the user will just request again.
            }
        }

        return redirect()->route('portal.login.sent');
    }

    public function showSent()
    {
        return view('portal.sent');
    }

    public function verify(string $token, Request $request): RedirectResponse
    {
        // M-S1: atomic claim of the magic-link.  The race the previous
        // read-then-update pattern allowed: an attacker who got a
        // glance at a forwarded-but-not-yet-clicked email link could
        // open it in parallel with the legitimate recipient — both
        // pass isValid(), both update used_at, both get a session
        // cookie.  The whereNull('used_at') in the UPDATE makes only
        // ONE of the concurrent requests claim the token; the others
        // see $updated === 0 and bounce to /portal/login.
        $updated = PortalAccessToken::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['used_at' => now()]);

        if ($updated !== 1) {
            return redirect()->route('portal.login')
                ->with('error', __('messages.portal_magic_link_invalid'));
        }

        $accessToken = PortalAccessToken::where('token', $token)->firstOrFail();

        $session = PortalSession::create([
            'lead_id'        => $accessToken->lead_id,
            'session_token'  => Str::random(64),
            'last_active_at' => now(),
            'expires_at'     => now()->addDays(7),
            'ip_address'     => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 500),
        ]);

        return redirect()->route('portal.dashboard')
            ->cookie('portal_session', $session->session_token, 60 * 24 * 7, '/', null, $request->secure(), true);
    }

    public function logout(Request $request): RedirectResponse
    {
        $cookie = $request->cookie('portal_session');
        if ($cookie) {
            PortalSession::where('session_token', $cookie)->delete();
        }

        // Mirror the flags the verify cookie was set with (Secure when
        // the request is HTTPS, HttpOnly always true).  A strict browser
        // may otherwise refuse to overwrite the original cookie because
        // the attribute set differs, leaving the stale cookie alive
        // until its 7-day TTL expires.
        return redirect()->route('portal.login')
            ->cookie('portal_session', '', -1, '/', null, $request->secure(), true);
    }
}
