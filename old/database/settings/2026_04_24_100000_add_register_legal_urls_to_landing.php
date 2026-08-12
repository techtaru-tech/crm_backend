<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds the two operator-override URLs that the public registration
 * form uses for its "Terms of Service" and "Privacy Policy" links.
 * Null = fall back to /pages/terms and /pages/privacy (the static
 * pages the StaticPagesSeeder creates on first install).
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('landing.register_terms_url',   null);
        $this->migrator->add('landing.register_privacy_url', null);
    }

    public function down(): void
    {
        $this->migrator->delete('landing.register_terms_url');
        $this->migrator->delete('landing.register_privacy_url');
    }
};
