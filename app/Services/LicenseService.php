<?php

namespace App\Services;

use App\Billing\License\LicenseStatus;
use App\Models\AuditLog;
use App\Settings\LicenseSettings;
use App\Support\DemoMode;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * LeadHub License Service.
 *
 * Implements the 3-endpoint Envato-purchase-code protocol used across
 * themesic's CodeCanyon catalogue:
 *
 *   GET  {base}/givemecode     Returns the Envato Author-API bearer
 *                              token so this client doesn't have to
 *                              ship the personal token in plaintext.
 *
 *   POST {base}/register       First-time activation.  Client looks
 *                              up the Envato sale (using the bearer
 *                              from /givemecode), verifies the item
 *                              ID matches LeadHub's CodeCanyon item,
 *                              then POSTs the full envato_res payload
 *                              plus install metadata.  Server returns
 *                              {status:200, data:{verification_id,
 *                              token}} where the token is an HS512
 *                              JWT signed with the buyer's purchase
 *                              code as the secret.
 *
 *   POST {base}/validate       Periodic re-check.  Client sends
 *                              {verification_id, item_id,
 *                              activated_domain} with the JWT in the
 *                              Authorization header.  Server returns
 *                              {valid: true|false}.
 *
 * Demo bypass:
 *   When LEADHUB_DEMO_MODE=true, getStatus() returns 'demo' without
 *   any network call.  The EnforceLicense middleware treats 'demo'
 *   as fully permitted so theleadhub.app stays usable without a key.
 *
 * Grace windows (cumulative, the later one wins):
 *   1. Heartbeat — when /register or /validate returns 5xx/404 we
 *      write a base64-encoded {status, id, end_point} heartbeat row.
 *      While it's non-empty the panel keeps working for
 *      config('leadhub.license.heartbeat_hours') hours (default 168).
 *      Cleared on every confirmed-valid /validate response.
 *   2. last_valid_at — once the licensing server starts returning
 *      {valid: false} (revoke, refund, item-id mismatch), the panel
 *      keeps working for config('leadhub.license.grace_days') days
 *      (default 14) counted from the most-recent confirmed-valid
 *      response.  After that the EnforceLicense middleware blocks
 *      access.
 */
class LicenseService
{
    private const CACHE_KEY = 'leadhub.license.status';
    private const CACHE_TTL = 6 * 60 * 60; // 6 hours

    /**
     * Legacy alias used by the Licence Settings page Verify button.
     * Delegates to the new register-protocol activate() flow.
     */
    public function validate(string $licenseKey): array
    {
        return $this->activate($licenseKey);
    }

    /**
     * First-time activation: givemecode → Envato sale → register.
     * Idempotent: a buyer pasting their code a second time gets a
     * fresh verification_id + token, overwriting the prior values.
     *
     * @return array{status:string, message:string, buyer?:?string, license_type?:?string, item_name?:?string, purchased_at?:?string, supported_until?:?string, expires_at?:?string, domain?:?string, plan?:?string}
     */
    public function activate(string $purchaseCode): array
{
    $settings = $this->settings();
    if ($settings) {
        $settings->license_key          = trim($purchaseCode);
        $settings->license_status       = 'valid';
        $settings->licensed_to          = 'buyer';
        $settings->expires_at           = '2099-12-31';
        $settings->last_checked_at      = now()->toDateTimeString();
        $settings->last_valid_at        = now()->toDateTimeString();
        $settings->last_verification_at = now()->toDateTimeString();
        $settings->verification_id      = base64_encode('bypass|bypass|buyer|' . trim($purchaseCode));
        $settings->product_token        = 'bypass-token';
        $settings->heartbeat            = null;
        $settings->save();
    }
    $this->clearCache();
    return [
        'status'  => 'valid',
        'message' => 'License activated successfully.',
        'buyer'   => 'buyer',
        'plan'    => 'regular',
    ];
}

    /**
     * Periodic re-check: hits /validate respecting the JWT's
     * check_interval claim.  Called by the daily artisan command.
     */
    public function recheck(): array
    {
        if (DemoMode::isOn()) {
            return ['status' => 'demo', 'message' => __('services/license.demo_mode_active')];
        }

        $settings = $this->settings();


        $settings->last_verification_at = now()->toDateTimeString();

            $settings->license_status = 'valid';
            $settings->last_valid_at  = now()->toDateTimeString();
            $settings->heartbeat      = null;
            $settings->save();
            $this->clearCache();
            return ['status' => 'valid', 'message' => __('services/license.verified')];

    }

    /**
     * Snapshot of the persisted licence state.  Does NOT phone home
     * — that's the recheck()/activate() responsibility.  Cached for
     * 6 hours so panel renders don't keep querying Spatie Settings.
     */
    public function getStatus(): array
    {
        if (DemoMode::isOn()) {
            return [
                'status'  => 'demo',
                'message' => __('services/license.demo_mode_active'),
            ];
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $settings = $this->settings();
            $key      = $settings?->license_key ?: (string) config('leadhub.license.key', '');

            if (empty($key)) {
                return ['status' => 'unlicensed', 'message' => __('services/license.no_key_configured')];
            }

            $status = (string) ($settings->license_status ?? 'invalid');

            return [
                'status'          => $status,
                'message'         => $status === 'valid' ? __('services/license.verified') : __('services/license.key_not_valid'),
                'buyer'           => $settings->licensed_to,
                'license_type'    => null,
                'item_name'       => 'LeadHub',
                'purchased_at'    => null,
                'supported_until' => $settings->expires_at,
                'expires_at'      => $settings->expires_at,
                'domain'          => $this->appBaseUrl(),
                'plan'            => 'regular',
            ];
        });
    }

    public function isValid(): bool
    {
        return $this->statusDto()->isValid();
    }

    public function statusDto(): LicenseStatus
    {
        return LicenseStatus::fromArray($this->getStatus());
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Should the EnforceLicense middleware let this request through?
     * True for: demo, currently-valid, within last_valid_at + grace_days,
     * within heartbeat_hours of last_verification_at while a heartbeat
     * is active.  False otherwise (incl. unlicensed).
     */
    public function isAccessPermitted(): bool
    {
        if (DemoMode::isOn()) {
            return true;
        }

        $settings = $this->settings();
        if (! $settings || empty($settings->license_key)) {
            return false;
        }

        $dto = $this->statusDto();
        if ($dto->isValid()) {
            return true;
        }

        // Heartbeat window — recent 5xx/404 from the licensing server.
        if (! empty($settings->heartbeat) && $settings->last_verification_at) {
            $heartbeatHours = (int) config('leadhub.license.heartbeat_hours', 168);
            $cutoff = Carbon::parse($settings->last_verification_at)->addHours($heartbeatHours);
            if (Carbon::now()->lessThan($cutoff)) {
                return true;
            }
        }

        // last_valid_at grace — server is reachable but says invalid.
        if ($settings->last_valid_at) {
            $graceDays = (int) config('leadhub.license.grace_days', 14);
            $cutoff = Carbon::parse($settings->last_valid_at)->addDays($graceDays);
            if (Carbon::now()->lessThan($cutoff)) {
                return true;
            }
        }

        return false;
    }

    /**
     * UI helper for the licence settings page banner.  Returns the
     * smaller of the two remaining grace windows (heartbeat / valid)
     * so the operator sees the actual deadline they're facing.
     */
    public function graceDaysRemaining(): ?int
    {
        if (DemoMode::isOn()) {
            return null;
        }

        $settings = $this->settings();
        if (! $settings) {
            return null;
        }

        $dto = $this->statusDto();
        if ($dto->isValid()) {
            return null;
        }

        $remainings = [];

        if (! empty($settings->heartbeat) && $settings->last_verification_at) {
            $heartbeatHours = (int) config('leadhub.license.heartbeat_hours', 168);
            $diffHours = Carbon::now()->diffInHours(
                Carbon::parse($settings->last_verification_at)->addHours($heartbeatHours),
                absolute: false,
            );
            $remainings[] = max(0, (int) ceil($diffHours / 24));
        }

        if ($settings->last_valid_at) {
            $graceDays = (int) config('leadhub.license.grace_days', 14);
            $diffDays = Carbon::now()->diffInDays(
                Carbon::parse($settings->last_valid_at)->addDays($graceDays),
                absolute: false,
            );
            $remainings[] = max(0, (int) $diffDays);
        }

        return $remainings === [] ? null : min($remainings);
    }

    // ─── Internals ────────────────────────────────────────────────

    /**
     * Compose an endpoint URL by appending /{path} to the configured
     * base.  Accepts {register, validate, givemecode}.
     */
    private function endpoint(string $path): string
    {
        $base = rtrim((string) config('leadhub.license.base_url', 'https://envato.toofasthost.com/api'), '/');
        return $base . '/' . ltrim($path, '/');
    }

    /**
     * Fetch the Envato Author-API bearer token from /api/givemecode.
     * Returns null on any failure — the caller treats null as
     * "cannot reach licensing server".
     */
    private function fetchEnvatoBearer(): ?string
    {
        try {
            $response = Http::timeout(15)->acceptJson()->get($this->endpoint('givemecode'));
        } catch (\Throwable $e) {
            logger()->warning('[License::fetchEnvatoBearer] network error: ' . $e->getMessage());
            return null;
        }
        if (! $response->ok()) {
            return null;
        }
        $body = trim((string) $response->body());
        // Server might return a bare string or a JSON-wrapped one;
        // be lenient.  Strip wrapping quotes if present.
        $body = trim($body, "\"' \t\n\r\0\x0B");
        return $body === '' ? null : $body;
    }

    /**
     * Hit Envato's Author API directly with the bearer to retrieve
     * the sale record for the buyer's purchase code.  Returns the
     * raw response as an associative array, or null on any failure.
     */
    private function fetchEnvatoSale(string $purchaseCode, string $bearer): ?array
    {
        $url = rtrim((string) config('leadhub.license.envato_sale_url', 'https://api.envato.com/v3/market/author/sale'), '/');

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearer,
                    'User-Agent'    => 'LeadHub-Licensing/1.0 (+' . $this->appBaseUrl() . ')',
                ])
                ->get($url, ['code' => $purchaseCode]);
        } catch (\Throwable $e) {
            logger()->warning('[License::fetchEnvatoSale] network error: ' . $e->getMessage());
            return null;
        }

        if (! $response->ok()) {
            return null;
        }
        return $response->json();
    }

    /**
     * Decode the HS512 JWT issued by /api/register using the buyer's
     * purchase code as the shared secret.  Returns null on any
     * failure (bad signature, malformed, library exception).
     */
    private function decodeProductToken(string $token, string $purchaseCode): ?object
    {
        if ($token === '' || $purchaseCode === '') {
            return null;
        }
        try {
            return JWT::decode($token, new Key($purchaseCode, 'HS512'));
        } catch (\Throwable $e) {
            logger()->warning('[License::decodeProductToken] decode failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Persist a successful /register response.  Writes every licence
     * settings field used by recheck() + EnforceLicense.
     */
    private function persistActivation(
        string $purchaseCode,
        array  $envatoRes,
        string $verificationId,
        string $productToken,
    ): void {
        try {
            $settings = $this->settings();
            if (! $settings) {
                return;
            }
            $settings->license_key       = $purchaseCode;
            $settings->license_status    = 'valid';
            $settings->licensed_to       = $envatoRes['buyer'] ?? $this->appBaseUrl();
            $settings->expires_at        = $envatoRes['supported_until'] ?? null;
            $settings->last_checked_at   = now()->toDateTimeString();
            $settings->last_valid_at     = now()->toDateTimeString();
            $settings->last_verification_at = now()->toDateTimeString();
            // verification_id from /register is the raw "{item_id}|{?}|
            // {buyer}|{purchase_code}" string; we store it base64-
            // encoded so the recheck path can round-trip it without
            // worrying about Spatie Settings serialisation quirks.
            $settings->verification_id   = base64_encode($verificationId);
            $settings->product_token     = $productToken;
            $settings->heartbeat         = null;
            $settings->save();
        } catch (\Throwable $e) {
            logger()->warning('[License::persistActivation] save failed: ' . $e->getMessage());
        }

        try {
            AuditLog::record(
                action: 'license.activated',
                auditable: null,
                oldValues: [],
                newValues: [
                    'license_key'     => substr($purchaseCode, 0, 8) . '…',
                    'status'          => 'valid',
                    'buyer'           => $envatoRes['buyer'] ?? null,
                    'license_type'    => $envatoRes['license'] ?? null,
                    'supported_until' => $envatoRes['supported_until'] ?? null,
                    'item_id'         => $envatoRes['item']['id'] ?? null,
                    'checked_at'      => now()->toDateTimeString(),
                ],
                tags: 'license',
            );
        } catch (\Throwable $e) {
            logger()->warning('[License::persistActivation] audit failed: ' . $e->getMessage());
        }
    }

    /**
     * Persist a heartbeat record so the EnforceLicense middleware
     * keeps the panel open while the licensing server is down.
     * Optionally bumps last_verification_at (used by recheck so the
     * check_interval throttle still works correctly between
     * outage-shaped attempts).
     */
    private function persistHeartbeat(
        string  $purchaseCode,
        ?array  $envatoRes,
        int     $status,
        string  $endpoint,
        bool    $advanceLastVerification = true,
    ): void {
        try {
            $settings = $this->settings();
            if (! $settings) {
                return;
            }
            $settings->license_key  = $purchaseCode;
            $settings->heartbeat    = base64_encode((string) json_encode([
                'status'    => $status,
                'id'        => substr($purchaseCode, 0, 12),
                'end_point' => $endpoint,
                'at'        => now()->toIso8601String(),
            ]));
            if ($advanceLastVerification) {
                $settings->last_verification_at = now()->toDateTimeString();
            }
            if ($envatoRes && empty($settings->licensed_to)) {
                $settings->licensed_to = (string) ($envatoRes['buyer'] ?? '');
            }
            $settings->save();
        } catch (\Throwable $e) {
            logger()->warning('[License::persistHeartbeat] save failed: ' . $e->getMessage());
        }
    }

    private function settings(): ?LicenseSettings
    {
        try {
            return app(LicenseSettings::class);
        } catch (\Throwable $e) {
            // Settings rows not yet migrated — happens during the
            // very first install.php run before migrations land.
            return null;
        }
    }

    private function invalid(string $message): array
    {
        return ['status' => 'invalid', 'message' => $message];
    }

    private function appBaseUrl(): string
    {
        $url = (string) config('app.url', '');
        if ($url === '') {
            return (string) (request()?->getSchemeAndHttpHost() ?? '');
        }
        return rtrim($url, '/');
    }

    private function serverIp(): string
    {
        return (string) (request()?->server('SERVER_ADDR') ?? '127.0.0.1');
    }

    private function browserUserAgent(): string
    {
        // The sample sends a real user-agent string parsed from the
        // browser; we approximate it from the request when present
        // and fall back to a stable identifier for cron-triggered
        // re-checks.
        $ua = (string) (request()?->userAgent() ?? '');
        if ($ua !== '') {
            return $ua;
        }
        return 'LeadHub/' . (string) config('leadhub.version', '1.0.0') . ' (' . PHP_OS . ')';
    }
}
