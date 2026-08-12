<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Integrations\IntegrationRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * OAuth 2.0 flow for CRM/marketing integrations that require it:
 * - HubSpot (Authorization Code)
 * - Salesforce (Authorization Code + refresh)
 * - ZohoCRM (Authorization Code + refresh)
 * - Google Sheets / Google OAuth (Authorization Code + refresh)
 */
class IntegrationOAuthController extends Controller
{
    private const REDIRECT_PARAM_SESSION_KEY = 'integration_oauth_type';
    private const STATE_SESSION_KEY          = 'integration_oauth_state';
    private const INTEGRATION_ID_KEY         = 'integration_oauth_id';

    private const SCOPES = [
        'hubspot'       => 'crm.objects.contacts.read crm.objects.contacts.write crm.objects.deals.write',
        'salesforce'    => 'api refresh_token offline_access',
        'zohocrm'       => 'ZohoCRM.modules.leads.ALL ZohoCRM.settings.ALL',
        'google_sheets' => 'https://www.googleapis.com/auth/spreadsheets',
    ];

    private const AUTH_URLS = [
        'hubspot'       => 'https://app.hubspot.com/oauth/authorize',
        'salesforce'    => null,
        'zohocrm'       => 'https://accounts.zoho.com/oauth/v2/auth',
        'google_sheets' => 'https://accounts.google.com/o/oauth2/v2/auth',
    ];

    private const TOKEN_URLS = [
        'hubspot'       => 'https://api.hubapi.com/oauth/v1/token',
        'salesforce'    => null,
        'zohocrm'       => 'https://accounts.zoho.com/oauth/v2/token',
        'google_sheets' => 'https://oauth2.googleapis.com/token',
    ];

    /**
     * Build the OAuth authorization URL and redirect the user.
     * GET /integrations/oauth/{type}/redirect?integration_id={id}
     */
    public function redirect(Request $request, string $type)
    {
        $tenantId      = Auth::user()->tenant_id;
        $integrationId = $request->query('integration_id');

        $integration = $integrationId
            ? Integration::where('tenant_id', $tenantId)->find($integrationId)
            : null;

        $config = $integration ? $integration->getConfig() : [];
        $authUrl = $this->buildAuthUrl($type, $config, $request);

        if (! $authUrl) {
            return redirect('/admin/integrations')
                ->with('error', __('messages.integration_oauth_unavailable', ['type' => $type]));
        }

        $state = Str::random(40);
        session([
            self::STATE_SESSION_KEY          => $state,
            self::REDIRECT_PARAM_SESSION_KEY => $type,
            self::INTEGRATION_ID_KEY         => $integrationId,
        ]);

        return redirect()->away($authUrl . '&state=' . $state);
    }

    /**
     * Handle the OAuth callback and store tokens.
     * GET /integrations/oauth/{type}/callback
     */
    public function callback(Request $request, string $type)
    {
        $tenantId      = Auth::user()->tenant_id;
        $expectedState = session(self::STATE_SESSION_KEY);
        $integrationId = session(self::INTEGRATION_ID_KEY);

        session()->forget([self::STATE_SESSION_KEY, self::REDIRECT_PARAM_SESSION_KEY, self::INTEGRATION_ID_KEY]);

        // Fix note: constant-time state comparison.  `!==` is a
        // timing oracle that recovers the OAuth state token character-
        // by-character; an attacker who races a callback against a
        // valid in-flight OAuth handshake could hijack the connection.
        // hash_equals() short-circuits in constant time.
        if (! hash_equals((string) $expectedState, (string) $request->query('state', ''))) {
            return redirect('/admin/integrations')
                ->with('error', __('messages.integration_oauth_state_mismatch'));
        }

        if ($request->has('error')) {
            return redirect('/admin/integrations')
                ->with('error', __('messages.integration_oauth_denied', ['reason' => $request->query('error_description', $request->query('error'))]));
        }

        $code = $request->query('code');
        if (! $code) {
            return redirect('/admin/integrations')
                ->with('error', __('messages.integration_oauth_no_code'));
        }

        $integration = $integrationId
            ? Integration::where('tenant_id', $tenantId)->find($integrationId)
            : Integration::where('tenant_id', $tenantId)->where('type', $type)->first();

        if (! $integration) {
            $integration = new Integration([
                'tenant_id' => $tenantId,
                'type'      => $type,
                // Translator-routed display name — buyer-facing once the
                // record is persisted and rendered on /admin/integrations.
                'name'      => IntegrationRegistry::getLabel($type),
                'enabled'   => false,
            ]);
        }

        $config = $integration->exists ? $integration->getConfig() : [];

        try {
            $tokens = $this->exchangeCode($type, $code, $config, $request);
        } catch (\Throwable $e) {
            Log::error("Integration OAuth token exchange failed for {$type}", ['error' => $e->getMessage()]);
            return redirect('/admin/integrations')
                ->with('error', __('messages.integration_oauth_exchange_failed', ['error' => $e->getMessage()]));
        }

        $newConfig = array_merge($config, $tokens, ['oauth_connected' => true]);
        $integration->setConfig($newConfig);

        if ($integration->exists) {
            $integration->update([
                'config'  => $integration->config,
                'status'  => Integration::STATUS_CONNECTED,
                'enabled' => true,
            ]);
        } else {
            $integration->config = $integration->config;
            $integration->status = Integration::STATUS_CONNECTED;
            $integration->enabled = true;
            $integration->save();
        }

        return redirect('/admin/integrations')
            ->with('success', __('messages.integration_oauth_connected', ['label' => IntegrationRegistry::getLabel($type)]));
    }

    /**
     * Refresh an expired access token using the stored refresh_token.
     */
    public static function refreshToken(Integration $integration): bool
    {
        $type   = $integration->type;
        $config = $integration->getConfig();
        $refreshToken = $config['refresh_token'] ?? null;

        if (! $refreshToken) return false;

        $tokenUrl = self::TOKEN_URLS[$type] ?? null;
        if (! $tokenUrl) return false;

        try {
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id'     => $config['client_id'] ?? '',
                'client_secret' => $config['client_secret'] ?? '',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $config['access_token']     = $data['access_token'] ?? $config['access_token'];
                $config['refresh_token']    = $data['refresh_token'] ?? $refreshToken;
                $config['token_expires_at'] = now()->addSeconds($data['expires_in'] ?? 3600)->toIso8601String();
                $integration->setConfig($config);
                $integration->save();
                return true;
            }
        } catch (\Throwable) {}

        return false;
    }

    private function buildAuthUrl(string $type, array $config, Request $request): ?string
    {
        $clientId    = $config['client_id'] ?? '';
        $redirectUri = route('integration.oauth.callback', ['type' => $type]);

        if (! $clientId) return null;

        return match ($type) {
            'hubspot' => $this->hubspotAuthUrl($clientId, $redirectUri),
            'salesforce' => $this->salesforceAuthUrl($config, $redirectUri),
            'zohocrm' => $this->zohoAuthUrl($clientId, $redirectUri),
            'google_sheets' => $this->googleAuthUrl($clientId, $redirectUri),
            default => null,
        };
    }

    private function hubspotAuthUrl(string $clientId, string $redirectUri): string
    {
        return 'https://app.hubspot.com/oauth/authorize?' . http_build_query([
            'client_id'    => $clientId,
            'redirect_uri' => $redirectUri,
            'scope'        => self::SCOPES['hubspot'],
            'response_type' => 'code',
        ]);
    }

    private function salesforceAuthUrl(array $config, string $redirectUri): string
    {
        $domain = rtrim($config['instance_url'] ?? 'https://login.salesforce.com', '/');
        return "{$domain}/services/oauth2/authorize?" . http_build_query([
            'client_id'     => $config['client_id'] ?? '',
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
        ]);
    }

    private function zohoAuthUrl(string $clientId, string $redirectUri): string
    {
        return 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => self::SCOPES['zohocrm'],
            'access_type'   => 'offline',
        ]);
    }

    private function googleAuthUrl(string $clientId, string $redirectUri): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => self::SCOPES['google_sheets'],
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);
    }

    private function exchangeCode(string $type, string $code, array $config, Request $request): array
    {
        $redirectUri = route('integration.oauth.callback', ['type' => $type]);
        $tokenUrl    = self::TOKEN_URLS[$type] ?? null;

        if ($type === 'salesforce') {
            return $this->salesforceExchange($code, $config, $redirectUri);
        }

        if (! $tokenUrl) throw new \RuntimeException(__('auth_flow.oauth.no_token_url', ['type' => $type]));

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $config['client_id'] ?? '',
            'client_secret' => $config['client_secret'] ?? '',
            'redirect_uri'  => $redirectUri,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(__('auth_flow.oauth.token_exchange_failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]));
        }

        $data = $response->json();

        $tokens = [
            'access_token'     => $data['access_token'] ?? null,
            'refresh_token'    => $data['refresh_token'] ?? null,
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600)->toIso8601String(),
        ];

        if ($type === 'salesforce' && isset($data['instance_url'])) {
            $tokens['instance_url'] = $data['instance_url'];
        }

        return $tokens;
    }

    private function salesforceExchange(string $code, array $config, string $redirectUri): array
    {
        $domain = rtrim($config['instance_url'] ?? 'https://login.salesforce.com', '/');

        // M-S3: validate the SA-supplied instance_url before posting
        // the OAuth code + client_secret to it.  Two checks:
        //   1. host must match the Salesforce-domain pattern so a
        //      malicious tenant admin can't redirect the token
        //      exchange to their own server.
        //   2. UrlSafetyGuard rejects RFC1918/loopback/IMDS even
        //      within the salesforce.com subdomain space (defence-in-
        //      depth against DNS rebinding).
        $host = (string) parse_url($domain, PHP_URL_HOST);
        if (! preg_match('/^[a-z0-9.-]+\.(my\.)?salesforce\.com$/i', $host)
            && ! preg_match('/^[a-z0-9.-]+\.force\.com$/i', $host)
            && $host !== 'login.salesforce.com'
            && $host !== 'test.salesforce.com'
        ) {
            throw new \RuntimeException(__('auth_flow.oauth.salesforce_invalid_url', ['host' => $host]));
        }
        try {
            \App\Support\UrlSafetyGuard::assertSafe($domain);
        } catch (\App\Support\UnsafeUrlException $e) {
            throw new \RuntimeException(__('auth_flow.oauth.salesforce_safety_failed', ['error' => $e->getMessage()]));
        }

        $response = Http::asForm()->post("{$domain}/services/oauth2/token", [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $config['client_id'] ?? '',
            'client_secret' => $config['client_secret'] ?? '',
            'redirect_uri'  => $redirectUri,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(__('auth_flow.oauth.salesforce_token_failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]));
        }

        $data = $response->json();
        return [
            'access_token'     => $data['access_token'] ?? null,
            'refresh_token'    => $data['refresh_token'] ?? null,
            'instance_url'     => $data['instance_url'] ?? null,
            'token_expires_at' => now()->addSeconds(7200)->toIso8601String(),
        ];
    }
}
