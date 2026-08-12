<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('security.enforce_2fa', false);
        $this->migrator->add('security.ip_whitelist', []);
        $this->migrator->add('security.session_lifetime', (int) config('leadhub.security.session_lifetime', 120));
        $this->migrator->add('security.max_login_attempts', (int) config('leadhub.security.max_login_attempts', 5));
        $this->migrator->add('security.lockout_duration', (int) config('leadhub.security.lockout_duration', 15));
    }
};
