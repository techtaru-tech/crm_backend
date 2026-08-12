<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Filament\Pages\Auth\ReCaptchaAwareLogin;
use App\Settings\RecaptchaSettings;

/**
 * Super-admin login page — identical behaviour to Filament's
 * default Login, plus optional reCAPTCHA v3 guard routed through
 * the RecaptchaSettings.guard_sa_login toggle.
 */
class SuperAdminLogin extends ReCaptchaAwareLogin
{
    public function getTitle(): string
    {
        return __('filament/sa_login.title');
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function recaptchaGuardPredicate(): bool
    {
        return RecaptchaSettings::resolveSafe()?->shouldGuardSaLogin() ?? false;
    }

    protected function recaptchaAction(): string
    {
        return 'sa_login';
    }
}
