<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('branding.app_name', config('leadhub.branding.app_name', 'LeadHub'));
        $this->migrator->add('branding.primary_color', config('leadhub.branding.primary_color', '#4f46e5'));
        $this->migrator->add('branding.accent_color', config('leadhub.branding.accent_color', '#06b6d4'));
        $this->migrator->add('branding.logo_url', null);
        $this->migrator->add('branding.favicon_url', null);
        $this->migrator->add('branding.login_bg_color', '#f3f4f6');
        $this->migrator->add('branding.login_bg_image', null);
        $this->migrator->add('branding.email_sender_name', config('leadhub.branding.app_name', 'LeadHub'));
        $this->migrator->add('branding.email_from', config('mail.from.address', 'hello@example.com'));
        $this->migrator->add('branding.footer_text', '');
    }
};
