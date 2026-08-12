<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds navbar-visibility toggles and footer customisation fields to
 * the `landing` settings group (LandingContent).  Nulls on creation
 * so existing installs keep their current nav/footer verbatim — the
 * helper `LandingContent::navShows()` treats null as "show".
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('landing.nav_show_pricing',      null);
        $this->migrator->add('landing.nav_show_docs',         null);
        $this->migrator->add('landing.nav_show_sign_in',      null);
        $this->migrator->add('landing.footer_show_nav_links', null);
        $this->migrator->add('landing.footer_tagline',        null);
        $this->migrator->add('landing.footer_copyright',      null);
    }

    public function down(): void
    {
        $this->migrator->delete('landing.nav_show_pricing');
        $this->migrator->delete('landing.nav_show_docs');
        $this->migrator->delete('landing.nav_show_sign_in');
        $this->migrator->delete('landing.footer_show_nav_links');
        $this->migrator->delete('landing.footer_tagline');
        $this->migrator->delete('landing.footer_copyright');
    }
};
