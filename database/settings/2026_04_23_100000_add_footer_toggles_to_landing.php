<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds operator-controlled toggles for the rich footer (static-page
 * column, social column, brand column) plus the three social URLs.
 *
 * Null = default (show).  False = hide.  Empty URL = hide that one
 * social icon while keeping the others visible.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('landing.footer_show_static_pages',  null);
        $this->migrator->add('landing.footer_show_brand_column',  null);
        $this->migrator->add('landing.footer_show_social',        null);
        $this->migrator->add('landing.footer_social_x_url',        null);
        $this->migrator->add('landing.footer_social_github_url',   null);
        $this->migrator->add('landing.footer_social_linkedin_url', null);
    }

    public function down(): void
    {
        $this->migrator->delete('landing.footer_show_static_pages');
        $this->migrator->delete('landing.footer_show_brand_column');
        $this->migrator->delete('landing.footer_show_social');
        $this->migrator->delete('landing.footer_social_x_url');
        $this->migrator->delete('landing.footer_social_github_url');
        $this->migrator->delete('landing.footer_social_linkedin_url');
    }
};
