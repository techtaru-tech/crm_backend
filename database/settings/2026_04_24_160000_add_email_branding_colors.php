<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Email-header + footer colour / gradient controls for the SA
 * branding settings.  Defaults mirror the prior hard-coded
 * values so installs upgraded in place render identically
 * until the operator explicitly changes them.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('branding.email_header_style',           'solid');
        $this->migrator->add('branding.email_header_color_primary',   '#4f46e5');
        $this->migrator->add('branding.email_header_color_secondary', '#6366f1');
        $this->migrator->add('branding.email_header_gradient_angle',  135);
        $this->migrator->add('branding.email_footer_color',           '#f9fafb');
        $this->migrator->add('branding.email_footer_text_color',      '#6b7280');
    }

    public function down(): void
    {
        foreach ([
            'branding.email_header_style',
            'branding.email_header_color_primary',
            'branding.email_header_color_secondary',
            'branding.email_header_gradient_angle',
            'branding.email_footer_color',
            'branding.email_footer_text_color',
        ] as $key) {
            $this->migrator->delete($key);
        }
    }
};
