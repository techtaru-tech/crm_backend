<?php

namespace App\Http\Controllers;

use App\Models\LeadSourceConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class OAuthConnectionController extends Controller
{
    protected array $providers = [
        'meta'          => ['label' => 'Meta (Facebook)'],
        'instagram'     => ['label' => 'Instagram'],
        'tiktok'        => ['label' => 'TikTok'],
        'linkedin'      => ['label' => 'LinkedIn'],
        'google_ads'    => ['label' => 'Google Ads'],
        'microsoft_ads' => ['label' => 'Microsoft Ads'],
    ];

    public function redirect(Request $request, string $source, int $connectionId)
    {
        $connection = LeadSourceConnection::findOrFail($connectionId);

        $state = Str::random(32);
        session([
            "oauth_state_{$source}" => $state,
            "oauth_connection_id"   => $connectionId,
        ]);

        $redirectUrl = route('oauth.callback', ['source' => $source]);
        $url         = $this->buildAuthUrl($source, $connection, $state, $redirectUrl);

        if (! $url) {
            return back()->with('error', __('messages.oauth_not_configured_for_source', ['source' => $source]));
        }

        return Redirect::away($url);
    }

    public function callback(Request $request, string $source)
    {
        $expectedState  = session("oauth_state_{$source}");
        $connectionId   = session('oauth_connection_id');

        if (! $connectionId || ! $expectedState) {
            return redirect('/admin/lead-source-connections')->with('error', __('messages.oauth_session_expired'));
        }

        // Fix note: constant-time comparison.  `!==` is a timing
        // oracle that recovers the state token character-by-character;
        // hash_equals() short-circuits in constant time regardless of
        // match position.  The sibling CalendarOAuthController already
        // uses this pattern.
        if (! hash_equals((string) $expectedState, (string) $request->query('state', ''))) {
            return redirect('/admin/lead-source-connections')->with('error', __('messages.oauth_state_invalid'));
        }

        if ($request->has('error')) {
            return redirect('/admin/lead-source-connections')->with('error', __('messages.oauth_authorization_denied', ['reason' => $request->query('error_description', $request->query('error'))]));
        }

        $code = $request->query('code');
        // Fix note: explicit tenant scope (see redirect() above).
        $tenantId   = (int) auth()->user()->tenant_id;
        $connection = LeadSourceConnection::where('tenant_id', $tenantId)
            ->where('id', $connectionId)
            ->first();

        if (! $connection || ! $code) {
            return redirect('/admin/lead-source-connections')->with('error', __('messages.oauth_connection_not_found'));
        }

        try {
            $tokens = $this->exchangeCode($source, $code, $connection, route('oauth.callback', ['source' => $source]));
        } catch (\Throwable $e) {
            Log::error("OAuth token exchange failed for {$source}", ['error' => $e->getMessage()]);
            return redirect('/admin/lead-source-connections')->with('error', __('messages.oauth_token_exchange_failed', ['error' => $e->getMessage()]));
        }

        if (empty($tokens)) {
            return redirect('/admin/lead-source-connections')->with('error', __('messages.oauth_token_retrieval_failed'));
        }

        $credentials = $connection->credentials ?? [];
        $credentials = array_merge($credentials, $tokens);
        $connection->update([
            'credentials' => $credentials,
            'status'      => 'connected',
        ]);

        session()->forget(["oauth_state_{$source}", 'oauth_connection_id']);

        return redirect('/admin/lead-source-connections')->with('success', __('messages.oauth_connected_success'));
    }

    protected function buildAuthUrl(string $source, LeadSourceConnection $connection, string $state, string $redirectUrl): ?string
    {
        $creds = $connection->credentials ?? [];

        return match($source) {
            'meta', 'instagram' => $this->metaAuthUrl($creds, $state, $redirectUrl, $source),
            'tiktok'        => $this->tiktokAuthUrl($creds, $state, $redirectUrl),
            'linkedin'      => $this->linkedinAuthUrl($creds, $state, $redirectUrl),
            'google_ads'    => $this->googleAuthUrl($creds, $state, $redirectUrl),
            'microsoft_ads' => $this->microsoftAuthUrl($creds, $state, $redirectUrl),
            default         => null,
        };
    }

    protected function metaAuthUrl(array $creds, string $state, string $redirectUrl, string $source): ?string
    {
        $clientId = $creds['app_id'] ?? $creds['client_id'] ?? null;
        if (! $clientId) {
            return null;
        }

        $scopes = $source === 'instagram'
            ? 'instagram_basic,instagram_manage_messages,pages_show_list'
            : 'leads_retrieval,pages_show_list,pages_read_engagement,pages_manage_ads';

        return 'https://www.facebook.com/dialog/oauth?' . http_build_query([
            'client_id'    => $clientId,
            'redirect_uri' => $redirectUrl,
            'state'        => $state,
            'scope'        => $scopes,
            'response_type' => 'code',
        ]);
    }

    protected function tiktokAuthUrl(array $creds, string $state, string $redirectUrl): ?string
    {
        $clientKey = $creds['client_key'] ?? $creds['client_id'] ?? null;
        if (! $clientKey) {
            return null;
        }

        return 'https://www.tiktok.com/v2/auth/authorize/?' . http_build_query([
            'client_key'    => $clientKey,
            'redirect_uri'  => $redirectUrl,
            'state'         => $state,
            'scope'         => 'user.info.basic',
            'response_type' => 'code',
        ]);
    }

    protected function linkedinAuthUrl(array $creds, string $state, string $redirectUrl): ?string
    {
        $clientId = $creds['client_id'] ?? null;
        if (! $clientId) {
            return null;
        }

        return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUrl,
            'state'         => $state,
            'scope'         => 'r_liteprofile r_emailaddress rw_lead_gen_forms',
        ]);
    }

    protected function googleAuthUrl(array $creds, string $state, string $redirectUrl): ?string
    {
        $clientId = $creds['client_id'] ?? null;
        if (! $clientId) {
            return null;
        }

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'response_type'   => 'code',
            'client_id'       => $clientId,
            'redirect_uri'    => $redirectUrl,
            'state'           => $state,
            'scope'           => 'https://www.googleapis.com/auth/adwords https://www.googleapis.com/auth/userinfo.email',
            'access_type'     => 'offline',
            'prompt'          => 'consent',
        ]);
    }

    protected function microsoftAuthUrl(array $creds, string $state, string $redirectUrl): ?string
    {
        $clientId = $creds['client_id'] ?? null;
        if (! $clientId) {
            return null;
        }

        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUrl,
            'state'         => $state,
            'scope'         => 'https://ads.microsoft.com/msads.manage offline_access',
            'response_mode' => 'query',
        ]);
    }

    protected function exchangeCode(string $source, string $code, LeadSourceConnection $connection, string $redirectUrl): array
    {
        $creds = $connection->credentials ?? [];

        return match($source) {
            'meta', 'instagram' => $this->metaExchange($code, $creds, $redirectUrl),
            'tiktok'        => $this->tiktokExchange($code, $creds, $redirectUrl),
            'linkedin'      => $this->linkedinExchange($code, $creds, $redirectUrl),
            'google_ads'    => $this->googleExchange($code, $creds, $redirectUrl),
            'microsoft_ads' => $this->microsoftExchange($code, $creds, $redirectUrl),
            default         => [],
        };
    }

    protected function metaExchange(string $code, array $creds, string $redirectUrl): array
    {
        $response = Http::get('https://graph.facebook.com/oauth/access_token', [
            'client_id'     => $creds['app_id'] ?? $creds['client_id'],
            'client_secret' => $creds['app_secret'] ?? $creds['client_secret'],
            'redirect_uri'  => $redirectUrl,
            'code'          => $code,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(__('auth_flow.oauth.meta_token_failed', ['body' => $response->body()]));
        }

        $data        = $response->json();
        $userToken   = $data['access_token'] ?? null;
        $expiresIn   = $data['expires_in'] ?? 5184000;

        if (! $userToken) {
            throw new \RuntimeException(__('auth_flow.oauth.meta_no_access_token'));
        }

        $result = [
            'user_access_token' => $userToken,
            'token_expires_at'  => now()->addSeconds($expiresIn)->toIso8601String(),
        ];

        $pagesResponse = Http::get('https://graph.facebook.com/me/accounts', [
            'access_token' => $userToken,
            'fields'       => 'id,name,access_token',
        ]);

        if ($pagesResponse->successful()) {
            $pages = $pagesResponse->json('data') ?? [];
            $result['available_pages'] = collect($pages)
                ->map(fn ($p) => ['id' => $p['id'], 'name' => $p['name'], 'access_token' => $p['access_token']])
                ->all();

            if (! empty($pages)) {
                $firstPage = $pages[0];
                $result['page_access_token'] = $firstPage['access_token'];
                $result['page_id']           = $firstPage['id'];
                $result['page_name']         = $firstPage['name'];
            }
        } else {
            Log::warning('Meta: could not fetch pages after OAuth', [
                'error' => $pagesResponse->body(),
            ]);
        }

        return $result;
    }

    protected function tiktokExchange(string $code, array $creds, string $redirectUrl): array
    {
        $response = Http::post('https://open.tiktokapis.com/v2/oauth/token/', [
            'client_key'    => $creds['client_key'] ?? $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectUrl,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException($response->body());
        }

        $data = $response->json('data') ?? $response->json();

        return [
            'access_token'  => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
            'open_id'       => $data['open_id'] ?? null,
        ];
    }

    protected function linkedinExchange(string $code, array $creds, string $redirectUrl): array
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirectUrl,
            'client_id'     => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException($response->body());
        }

        $data = $response->json();

        return [
            'access_token'  => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 5184000)->toIso8601String(),
        ];
    }

    protected function googleExchange(string $code, array $creds, string $redirectUrl): array
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'redirect_uri'  => $redirectUrl,
            'grant_type'    => 'authorization_code',
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException($response->body());
        }

        $data = $response->json();

        return [
            'access_token'  => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
            'token_type'    => $data['token_type'] ?? 'Bearer',
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600)->toIso8601String(),
        ];
    }

    protected function microsoftExchange(string $code, array $creds, string $redirectUrl): array
    {
        $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'client_id'     => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'code'          => $code,
            'redirect_uri'  => $redirectUrl,
            'grant_type'    => 'authorization_code',
            'scope'         => 'https://ads.microsoft.com/msads.manage offline_access',
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException($response->body());
        }

        $data = $response->json();

        return [
            'access_token'  => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600)->toIso8601String(),
        ];
    }
}
