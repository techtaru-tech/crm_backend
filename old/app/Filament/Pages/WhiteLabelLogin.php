<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Auth\ReCaptchaAwareLogin;
use App\Settings\RecaptchaSettings;

/**
 * White-label login page for tenants.  Extends ReCaptchaAwareLogin
 * so the tenant /admin login picks up the reCAPTCHA v3 check when
 * the SA has enabled it in /super-admin/recaptcha-settings.
 *
 * Tenant branding (app name, logo, bg colour) is injected via
 * getTitle() / getBrandName() + CSS from InjectTenantBranding
 * middleware — no view overrides needed.
 */
class WhiteLabelLogin extends ReCaptchaAwareLogin
{
    public function getTitle(): string
    {
        $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;
        $appName = $tenant?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub');
        return __('filament/white_label_login.sign_in_title', ['app' => $appName]);
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function recaptchaGuardPredicate(): bool
    {
        return RecaptchaSettings::resolveSafe()?->shouldGuardAdminLogin() ?? false;
    }

    protected function recaptchaAction(): string
    {
        return 'admin_login';
    }
}
