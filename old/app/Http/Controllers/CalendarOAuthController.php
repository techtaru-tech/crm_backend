<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use App\Services\Calendar\GoogleCalendarSyncService;
use App\Services\Calendar\OutlookCalendarSyncService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http as HttpClient;
use Illuminate\Support\Facades\Log;

/**
 * OAuth flow for user-personal calendar connections.
 *
 * Distinct from IntegrationOAuthController (tenant-level Salesforce/
 * HubSpot etc.) and OAuthConnectionController (inbound lead-source
 * capture).  This is per-USER consent so each member connects their
 * own calendar.
 *
 * Flow:
 *   GET  /admin/calendar/connect/{provider}   → redirect to provider's consent screen
 *   GET  /admin/calendar/oauth/callback       → handle redirect_back, mint CalendarConnection
 *   POST /admin/calendar/disconnect/{id}      → revoke + delete
 *
 * State token is an HMAC-SHA256 digest bound to the authenticated
 * user's id and the application key.  The provider receives the digest
 * verbatim and echoes it back; the callback recomputes it from the
 * session-stashed nonce + auth()->id() and compares with hash_equals.
 * Attackers without the session nonce AND the user binding cannot
 * forge a valid state, even if they capture the digest in transit.
 */
class CalendarOAuthController extends Controller
{
    public function __construct(
        protected GoogleCalendarSyncService $google,
        protected OutlookCalendarSyncService $outlook,
    ) {
    }

    public function connect(Request $request, string $provider): RedirectResponse
    {
        if (! in_array($provider, [CalendarConnection::PROVIDER_GOOGLE, CalendarConnection::PROVIDER_OUTLOOK], true)) {
            return redirect('/admin/settings/calendar')->withErrors(['calendar' => __('messages.calendar_unsupported_provider', ['provider' => $provider])]);
        }

        // User-bound CSRF state.  The nonce stays server-side; the digest
        // is what's handed to the OAuth provider.  Both pieces are needed
        // to recompute the HMAC at callback time, so a captured digest
        // alone cannot be replayed against another user's session.
        $nonce = bin2hex(random_bytes(20));
        $state = $this->computeState($nonce, $request->user()?->getAuthIdentifier());

        $request->session()->put('calendar_oauth_nonce', $nonce);
        $request->session()->put('calendar_oauth_state', $state);
        $request->session()->put('calendar_oauth_provider', $provider);

        $redirectUri = \App\Support\AdminUrl::for('calendar/oauth/callback');
        $url = $provider === CalendarConnection::PROVIDER_OUTLOOK
            ? $this->outlook->authUrl($redirectUri, $state)
            : $this->google->authUrl($redirectUri, $state);

        return redirect()->away($url);
    }

    public function callback(Request $request): RedirectResponse
    {
        $code  = (string) $request->query('code', '');
        $state = (string) $request->query('state', '');
        $error = (string) $request->query('error', '');

        if ($error !== '') {
            return redirect('/admin/settings/calendar')->withErrors([
                'calendar' => __('auth_flow.calendar_oauth.consent_rejected', ['error' => $error]),
            ]);
        }

        // User binding has to be resolved FIRST — the HMAC was minted
        // against this user's id at connect() time, and the verification
        // step must mirror that.  Without an authed user we cannot
        // recompute the digest, so an unauthenticated callback is a
        // hard 403, not a redirect.
        $user = $request->user();
        if (! $user || ! $user->tenant_id) {
            Log::warning('CalendarOAuthController::callback no auth user on callback', [
                'ip'         => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 200),
            ]);
            abort(403, __('messages.calendar_oauth_no_session'));
        }

        $nonce         = (string) $request->session()->pull('calendar_oauth_nonce', '');
        $expectedState = (string) $request->session()->pull('calendar_oauth_state', '');
        $recomputed    = $nonce !== ''
            ? $this->computeState($nonce, $user->getAuthIdentifier())
            : '';

        $stateOk = $state !== ''
            && $expectedState !== ''
            && $recomputed !== ''
            && hash_equals($expectedState, $state)
            && hash_equals($recomputed, $state);

        if (! $stateOk) {
            Log::warning('CalendarOAuthController::callback OAuth state mismatch', [
                'user_id' => $user->id,
                'ip'      => $request->ip(),
                'reason'  => 'state failed HMAC verification (possible CSRF attempt)',
            ]);
            abort(403, __('messages.oauth_state_mismatch'));
        }

        if ($code === '') {
            return redirect('/admin/settings/calendar')->withErrors([
                'calendar' => __('auth_flow.calendar_oauth.no_authorization_code'),
            ]);
        }

        $provider = (string) $request->session()->pull('calendar_oauth_provider', CalendarConnection::PROVIDER_GOOGLE);

        $redirectUri = \App\Support\AdminUrl::for('calendar/oauth/callback');
        $tokens = $provider === CalendarConnection::PROVIDER_OUTLOOK
            ? $this->outlook->exchangeAuthCode($code, $redirectUri)
            : $this->google->exchangeAuthCode($code, $redirectUri);

        if (! $tokens || empty($tokens['access_token'])) {
            $providerLabel = $provider === CalendarConnection::PROVIDER_OUTLOOK ? 'Microsoft' : 'Google';
            return redirect('/admin/settings/calendar')->withErrors([
                'calendar' => __('auth_flow.calendar_oauth.token_exchange_failed', ['provider' => $providerLabel]),
            ]);
        }

        $email = $this->fetchAccountEmail($tokens['access_token'] ?? '', $provider) ?? $user->email;

        try {
            $isOutlook = $provider === CalendarConnection::PROVIDER_OUTLOOK;

            CalendarConnection::query()
                ->updateOrCreate(
                    ['user_id' => $user->id, 'provider' => $provider],
                    [
                        'tenant_id'              => $user->tenant_id,
                        'external_account_email' => $email,
                        'access_token'           => $tokens['access_token'],
                        'refresh_token'          => $tokens['refresh_token'] ?? null,
                        'expires_at'             => Carbon::now()->addSeconds((int) ($tokens['expires_in'] ?? 3600)),
                        'scopes'                 => $isOutlook ? OutlookCalendarSyncService::SCOPES : GoogleCalendarSyncService::SCOPES,
                        'calendar_id'            => $isOutlook ? 'default' : 'primary',
                        'sync_token'             => null,
                        'status'                 => CalendarConnection::STATUS_ACTIVE,
                        'last_sync_error'        => null,
                    ],
                );
        } catch (\Throwable $e) {
            Log::error('CalendarOAuthController::callback persist failed', [
                'user_id'  => $user->id,
                'provider' => $provider,
                'error'    => $e->getMessage(),
            ]);
            return redirect('/admin/settings/calendar')->withErrors([
                'calendar' => __('auth_flow.calendar_oauth.connection_save_failed'),
            ]);
        }

        $providerLabel = $provider === CalendarConnection::PROVIDER_OUTLOOK ? 'Outlook' : 'Google';
        return redirect('/admin/settings/calendar')->with('success', __('auth_flow.calendar_oauth.connected_success', [
            'provider' => $providerLabel,
            'email'    => $email,
        ]));
    }

    public function disconnect(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect('/admin/settings/calendar');
        }

        $conn = CalendarConnection::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $conn) {
            return redirect('/admin/settings/calendar')->withErrors(['calendar' => __('messages.calendar_connection_not_found')]);
        }

        // Best-effort revoke for Google — non-blocking.
        if ($conn->provider === CalendarConnection::PROVIDER_GOOGLE && ! empty($conn->refresh_token)) {
            try {
                HttpClient::asForm()
                    ->timeout(10)
                    ->post('https://oauth2.googleapis.com/revoke', ['token' => $conn->refresh_token]);
            } catch (\Throwable) {
                // Row removal is the canonical disconnect — token revoke is courtesy.
            }
        }

        $conn->delete();

        return redirect('/admin/settings/calendar')->with('success', __('messages.calendar_disconnected_success'));
    }

    /**
     * Mint the OAuth state digest.  Bound to the authenticated user's
     * id + the application key so a captured digest from one session
     * cannot be replayed against a different user.  The nonce is the
     * only random component and is stored server-side.
     */
    protected function computeState(string $nonce, mixed $userId): string
    {
        if ($nonce === '' || $userId === null || $userId === '') {
            return '';
        }
        $secret = (string) config('app.key') . (string) $userId;
        return hash_hmac('sha256', $nonce, $secret);
    }

    protected function fetchAccountEmail(string $accessToken, string $provider = CalendarConnection::PROVIDER_GOOGLE): ?string
    {
        try {
            if ($provider === CalendarConnection::PROVIDER_OUTLOOK) {
                $response = HttpClient::withToken($accessToken)
                    ->timeout(10)
                    ->get(OutlookCalendarSyncService::USERINFO_URL);

                if ($response->successful()) {
                    // Microsoft returns 'mail' for work accounts, 'userPrincipalName' as fallback.
                    return $response->json('mail') ?? $response->json('userPrincipalName');
                }
            } else {
                $response = HttpClient::withToken($accessToken)
                    ->timeout(10)
                    ->get('https://openidconnect.googleapis.com/v1/userinfo');

                if ($response->successful()) {
                    return $response->json('email');
                }
            }
        } catch (\Throwable) {
            // Silent — fallback to user's LeadHub email.
        }
        return null;
    }
}
