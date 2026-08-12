<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    public function __construct(protected Google2FA $google2fa) {}

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQrCodeUrl(User $user, string $secret): string
    {
        $companyName = config('leadhub.branding.app_name', 'LeadHub');

        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $user->email,
            $secret
        );
    }

    /**
     * Render the otpauth secret as an inline QR-code data URI
     * (data:image/svg+xml;base64,…).
     *
     * Uses pragmarx/google2fa-qrcode, which auto-selects the
     * chillerlan/php-qrcode backend (both are hard dependencies of
     * filament/filament, so they ship in every build) — the exact path
     * Filament's own MFA uses. The previous implementation newed up
     * \BaconQrCode\Writer directly in the Blade view, but bacon/bacon-qr-code
     * is NOT a shipped dependency, so the setup page 500'd with
     * "Class \"BaconQrCode\\Writer\" not found".
     *
     * Returns null (never throws) if no QR backend is available, so the
     * setup page degrades to manual key entry instead of crashing.
     */
    public function getQrCodeInline(User $user, string $secret): ?string
    {
        try {
            $google2faQr = new \PragmaRX\Google2FAQRCode\Google2FA();

            return $google2faQr->getQRCodeInline(
                config('leadhub.branding.app_name', 'LeadHub'),
                $user->email,
                $secret
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[2fa] QR render failed; falling back to manual key entry', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return (bool) $this->google2fa->verifyKey($secret, $code);
    }

    public function generateRecoveryCodes(int $count = 8): Collection
    {
        return collect(range(1, $count))->map(fn () => Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4)));
    }

    public function enable(User $user, string $secret, string $verificationCode): bool
    {
        if (! $this->verifyCode($secret, $verificationCode)) {
            return false;
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_secret'         => $secret,
            'two_factor_recovery_codes' => $recoveryCodes->all(),
            'two_factor_enabled'        => true,
        ]);

        return true;
    }

    public function disable(User $user): void
    {
        $user->update([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_enabled'        => false,
        ]);
    }

    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];

        $normalizedCode = strtoupper(trim($code));
        $index = array_search($normalizedCode, array_map('strtoupper', $codes));

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->update(['two_factor_recovery_codes' => array_values($codes)]);

        return true;
    }

    public function regenerateRecoveryCodes(User $user): Collection
    {
        $codes = $this->generateRecoveryCodes();
        $user->update(['two_factor_recovery_codes' => $codes->all()]);
        return $codes;
    }
}
