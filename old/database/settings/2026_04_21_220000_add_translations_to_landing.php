<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds a `landing.translations` settings key — a per-locale override
 * map for the translatable text fields on the marketing landing.
 *
 * Shape:
 *   [
 *     'hero_headline' => ['ar' => '...', 'es' => '...', 'hi' => '...'],
 *     'hero_subtext'  => ['ar' => '...', ...],
 *     ...
 *   ]
 *
 * English reads from the primary scalar fields (hero_headline,
 * hero_subtext, etc.) — this map only holds non-English values,
 * resolved via LandingContent::t($key).
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('landing.translations', []);
    }

    public function down(): void
    {
        $this->migrator->delete('landing.translations');
    }
};
