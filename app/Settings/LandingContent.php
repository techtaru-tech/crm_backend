<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * DB-backed content for the public marketing landing page. Lets the
 * CodeCanyon buyer rewrite the hero, features, CTA, and footer
 * without touching blade files.
 *
 * Every field defaults to a sensible stock copy when unset so a
 * fresh install still renders a complete page out of the box.
 */
class LandingContent extends Settings
{
    /* ---------- Hero ---------- */
    public ?string $hero_eyebrow = null;
    public ?string $hero_headline = null;
    public ?string $hero_headline_highlight = null;
    public ?string $hero_subtext = null;
    public ?string $hero_cta_label = null;
    public ?string $hero_note = null;

    /* ---------- Features section ---------- */
    public ?string $features_headline = null;
    public ?string $features_subtext = null;

    /**
     * Array of feature cards, each shaped as:
     *   ['title' => 'Smart lead capture', 'body' => '…']
     */
    public array $features = [];

    /* ---------- Pricing blurb ---------- */
    public ?string $pricing_headline = null;
    public ?string $pricing_subtext = null;

    /* ---------- Final CTA ---------- */
    public ?string $cta_headline = null;
    public ?string $cta_subtext = null;

    /* ---------- Stats strip (light landing variant) ----------
       The four "19 / Lead Sources ... 0 / Lock-in" tiles rendered
       beneath the hero on marketing.landing-light.blade.php.  Each
       tile has a number + label slot, both ?string so a null falls
       back to the translator key (marketing.light_statN_n /_l) and
       existing installs keep their stock values until an operator
       overrides them from /super-admin/landing-page-editor.

       Stat 3's number is "∞" in the original blade because there's
       no enforced cap on tenants — set it as a string here too so
       the operator can write any unicode glyph they want. */
    public ?string $stat1_number = null;
    public ?string $stat1_label  = null;
    public ?string $stat2_number = null;
    public ?string $stat2_label  = null;
    public ?string $stat3_number = null;
    public ?string $stat3_label  = null;
    public ?string $stat4_number = null;
    public ?string $stat4_label  = null;

    /* ---------- Navbar visibility toggles ----------
       Null = show (default). False = hide. Gives existing installs
       the same rendered nav they have today until an operator
       explicitly flips something off. */
    public ?bool $nav_show_pricing = null;
    public ?bool $nav_show_docs    = null;
    public ?bool $nav_show_sign_in = null;

    /* ---------- Footer customisation ---------- */
    public ?bool   $footer_show_nav_links   = null;
    public ?string $footer_tagline          = null;
    public ?string $footer_copyright        = null;
    public ?bool   $footer_show_static_pages = null;
    public ?bool   $footer_show_brand_column = null;
    public ?bool   $footer_show_social       = null;

    /* Social handles / URLs.  When the URL is non-empty, the
       corresponding icon renders in the footer.  Empty = hide. */
    public ?string $footer_social_x_url        = null;
    public ?string $footer_social_github_url   = null;
    public ?string $footer_social_linkedin_url = null;
    public ?string $footer_social_facebook_url  = null;
    public ?string $footer_social_instagram_url = null;
    public ?string $footer_social_youtube_url   = null;

    /* ---------- SEO / meta (public marketing site) ----------
       Operator-editable meta for the marketing homepage. When null, the
       layout falls back to the stock translator strings it rendered before,
       so existing installs are unchanged until an operator fills these in
       from /super-admin/landing-page-editor → SEO tab. */
    public ?string $seo_meta_title       = null;
    public ?string $seo_meta_description = null;
    public ?string $seo_meta_keywords    = null;
    public ?string $seo_og_image_url     = null;

    /* ---------- Registration legal links ----------
       Overrides the "Terms of Service" and "Privacy Policy" URLs
       that the registration form links to.  When null, they fall
       back to /pages/terms and /pages/privacy — the static pages
       the StaticPagesSeeder creates on first install.  Operators
       who host legal text on a different domain (e.g. Notion,
       Docusign, lawyer-provided URLs) can paste those here. */
    public ?string $register_terms_url   = null;
    public ?string $register_privacy_url = null;

    /* ---------- Per-locale overrides ----------
       English always lives in the primary scalar fields above; this
       map holds translation overrides for every other supported
       locale.  Shape:
         [
           'hero_headline' => ['ar' => '...', 'es' => '...', 'hi' => '...'],
           'hero_subtext'  => ['ar' => '...', ...],
           ...
         ]
       Null locale values fall back to the English scalar field. */
    public array $translations = [];

    public static function group(): string
    {
        return 'landing';
    }

    /** True unless the operator explicitly toggled the link off. */
    public function navShows(string $key): bool
    {
        $val = $this->{'nav_show_' . $key} ?? null;
        return $val === null ? true : (bool) $val;
    }

    /**
     * Return the current-locale value for a translatable field.  English
     * reads from the primary scalar; every other locale reads from the
     * `translations` map with fall-through to English when empty or
     * missing — so a half-translated locale still renders a complete
     * page.
     */
    public function t(string $key, ?string $locale = null): string
    {
        $locale    = $locale ?: app()->getLocale();
        $english   = (string) ($this->{$key} ?? '');

        if ($locale === 'en' || $locale === '') {
            return $english;
        }

        $override = $this->translations[$key][$locale] ?? null;
        return ($override !== null && $override !== '') ? (string) $override : $english;
    }
}
