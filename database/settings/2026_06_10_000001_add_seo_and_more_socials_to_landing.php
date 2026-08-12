<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Register the operator-editable SEO meta fields and the extra footer
 * social URLs (Facebook / Instagram / YouTube) for the public marketing
 * landing page.
 *
 * All default to null so existing installs render exactly as before until
 * an operator fills them in at /super-admin/landing-page-editor (SEO tab /
 * Footer → Social icons). The companion LandingContent settings class and
 * the marketing layout fall back to the stock translator strings when null.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        // Extra footer socials (X/GitHub/LinkedIn already exist).
        $this->migrator->add('landing.footer_social_facebook_url',  null);
        $this->migrator->add('landing.footer_social_instagram_url', null);
        $this->migrator->add('landing.footer_social_youtube_url',   null);

        // SEO / meta for the marketing homepage.
        $this->migrator->add('landing.seo_meta_title',       null);
        $this->migrator->add('landing.seo_meta_description', null);
        $this->migrator->add('landing.seo_meta_keywords',    null);
        $this->migrator->add('landing.seo_og_image_url',     null);
    }

    public function down(): void
    {
        $this->migrator->delete('landing.footer_social_facebook_url');
        $this->migrator->delete('landing.footer_social_instagram_url');
        $this->migrator->delete('landing.footer_social_youtube_url');
        $this->migrator->delete('landing.seo_meta_title');
        $this->migrator->delete('landing.seo_meta_description');
        $this->migrator->delete('landing.seo_meta_keywords');
        $this->migrator->delete('landing.seo_og_image_url');
    }
};
