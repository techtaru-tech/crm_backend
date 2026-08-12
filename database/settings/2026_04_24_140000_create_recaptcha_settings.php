<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Google reCAPTCHA v3 settings.  Seeds with the master switch off
 * and sensible defaults on the per-guard toggles, so enabling is
 * a single-click once the operator pastes their site + secret key.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('recaptcha.enabled',           false);
        $this->migrator->add('recaptcha.site_key',          null);
        $this->migrator->add('recaptcha.secret_key',        null);
        $this->migrator->add('recaptcha.guard_register',    true);
        $this->migrator->add('recaptcha.guard_admin_login', true);
        $this->migrator->add('recaptcha.guard_sa_login',    true);
        $this->migrator->add('recaptcha.min_score',         0.5);
    }

    public function down(): void
    {
        foreach ([
            'recaptcha.enabled',
            'recaptcha.site_key',
            'recaptcha.secret_key',
            'recaptcha.guard_register',
            'recaptcha.guard_admin_login',
            'recaptcha.guard_sa_login',
            'recaptcha.min_score',
        ] as $key) {
            $this->migrator->delete($key);
        }
    }
};
