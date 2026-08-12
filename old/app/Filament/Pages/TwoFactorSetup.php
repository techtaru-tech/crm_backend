<?php

namespace App\Filament\Pages;

use App\Services\TwoFactorAuthService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class TwoFactorSetup extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|UnitEnum|null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return __('filament/two_factor_setup.nav_label');
    }
    // Visible in the sidebar so every user can enroll their 2FA, even
    // when `enforce_2fa` is off — otherwise the QR setup is unreachable.
    protected string $view = 'filament.pages.two-factor-setup';

    public ?string $pendingSecret = null;
    public ?string $qrCodeUrl = null;
    public ?string $qrCodeDataUri = null;
    public ?array $recoveryCodes = null;
    public string $verificationCode = '';

    public function mount(): void
    {
        $user = auth()->user();
        if ($user->two_factor_enabled) {
            $this->recoveryCodes = $user->two_factor_recovery_codes ?? [];
        }
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/two_factor_setup.page_title');
    }

    public function generateSecret(): void
    {
        $service = app(TwoFactorAuthService::class);
        $this->pendingSecret = $service->generateSecret();
        $this->qrCodeUrl     = $service->getQrCodeUrl(auth()->user(), $this->pendingSecret);
        $this->qrCodeDataUri = $service->getQrCodeInline(auth()->user(), $this->pendingSecret);
    }

    public function enableTwoFactor(): void
    {
        $service = app(TwoFactorAuthService::class);

        if (! $this->pendingSecret) {
            Notification::make()->title(__('notifications.twofa_generate_secret_first'))->danger()->send();
            return;
        }

        if ($service->enable(auth()->user(), $this->pendingSecret, $this->verificationCode)) {
            $this->pendingSecret    = null;
            $this->qrCodeUrl        = null;
            $this->qrCodeDataUri    = null;
            $this->verificationCode = '';
            $this->recoveryCodes    = auth()->user()->fresh()->two_factor_recovery_codes;
            Notification::make()->title(__('notifications.twofa_enabled'))->success()->send();
        } else {
            Notification::make()->title(__('notifications.twofa_invalid_code'))->danger()->send();
        }
    }

    public function disableTwoFactor(): void
    {
        app(TwoFactorAuthService::class)->disable(auth()->user());
        $this->recoveryCodes = null;
        Notification::make()->title(__('notifications.twofa_disabled'))->warning()->send();
    }

    public function regenerateRecoveryCodes(): void
    {
        $codes = app(TwoFactorAuthService::class)->regenerateRecoveryCodes(auth()->user());
        $this->recoveryCodes = $codes->all();
        Notification::make()->title(__('notifications.twofa_recovery_regenerated'))->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
