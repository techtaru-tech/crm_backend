<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin LandingPageEditor — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_landing_page_editor.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_landing_page_editor.php.
*/

return [

    // ----- Page title -----
    'title'                       => 'Landing Page Editor',
    'navigation_label'            => 'Landing Page',
    'tabs_outer'                  => 'Landing',
    'tabs_locale'                 => 'Locale',

    // ----- Hero tab -----
    'tab_hero'                    => 'Hero',
    'hero_eyebrow'                => 'Eyebrow / badge text',
    'hero_eyebrow_placeholder'    => 'e.g. New — multi-currency billing is live',
    'hero_headline'               => 'Main headline',
    'hero_headline_placeholder'   => 'The lead workspace your team',
    'hero_headline_highlight'     => 'Highlight (gradient) phrase',
    'hero_headline_highlight_placeholder' => 'actually wants to use',
    'hero_subtext'                => 'Sub-headline',
    'hero_subtext_placeholder'    => 'Capture leads from any source…',
    'hero_cta_label'              => 'Primary CTA button label',
    'hero_cta_label_placeholder'  => 'Start your free trial',
    'hero_note'                   => 'Trust note (below CTA)',
    'hero_note_placeholder'       => 'No credit card required · Cancel any time',

    // ----- Features tab -----
    'tab_features'                => 'Features',
    'features_headline'           => 'Section headline',
    'features_subtext'            => 'Section subtext',
    'feature_cards'               => 'Feature cards',
    'feature_title'               => 'Title',
    'feature_body'                => 'Body',

    // ----- Pricing tab -----
    'tab_pricing'                 => 'Pricing Blurb',
    'pricing_headline'            => 'Section headline',
    'pricing_subtext'             => 'Section subtext',

    // ----- Final CTA tab -----
    'tab_final_cta'               => 'Final CTA',
    'cta_headline'                => 'Headline',
    'cta_subtext'                 => 'Subtext',

    // ----- Stats tab -----
    'tab_stats'                   => 'Stats',
    'stats_blank_helper'          => 'Leave blank to use the stock value shipped in the marketing translation files.',
    'stat3_number_helper'         => 'Default is the "∞" glyph. Override only if you want to show a real cap.',
    'stat1_number'                => 'Stat 1 — Number',
    'stat1_label'                 => 'Stat 1 — Label',
    'stat2_number'                => 'Stat 2 — Number',
    'stat2_label'                 => 'Stat 2 — Label',
    'stat3_number'                => 'Stat 3 — Number',
    'stat3_label'                 => 'Stat 3 — Label',
    'stat4_number'                => 'Stat 4 — Number',
    'stat4_label'                 => 'Stat 4 — Label',

    // ----- Navigation tab -----
    'tab_navigation'              => 'Navigation',
    'built_in_navbar_description' => 'Toggle off any built-in link you don\'t want in the marketing navbar. Static pages created under System → Static Pages are managed there.',
    'show_pricing_link'           => 'Show "Pricing" link',
    'show_docs_link'              => 'Show "Docs" link',
    'show_sign_in_link'           => 'Show "Sign in" link',
    'registration_legal_description' => 'Where the "Terms of Service" and "Privacy Policy" links on the public /register form should point. Leave blank to link to the /pages/terms and /pages/privacy static pages the seeder creates on first install.',
    'register_terms_url'          => 'Terms of Service URL',
    'register_terms_url_helper'   => 'Relative (e.g. /pages/terms) or absolute (e.g. https://legal.example.com/terms).',
    'register_privacy_url'        => 'Privacy Policy URL',
    'register_privacy_url_helper' => 'Relative or absolute. Opens in a new tab from the registration form.',

    // ----- Footer tab -----
    'tab_footer'                  => 'Footer',
    'columns_footer_description'  => 'Each column can be hidden independently. The rich footer is used by the Light (default) landing variant; simpler variants use a minimal built-in footer that these toggles also gate.',
    'brand_tagline_column'        => 'Brand + tagline column',
    'product_links'               => 'Product links (Pricing / Docs / Sign in)',
    'static_pages_column'         => 'Static pages column (Resources)',
    'static_pages_column_helper'  => 'Links to whatever static pages you have marked "Show in footer" under System → Static Pages.',
    'social_icons_description'    => 'Fill the URL for any icon you want to display. Leave blank to hide that one icon. Master toggle below kills all three at once.',
    'show_social_icons'           => 'Show any social icons at all',
    'social_x_url'                => 'X (Twitter) URL',
    'social_github_url'           => 'GitHub URL',
    'social_linkedin_url'         => 'LinkedIn URL',
    'placeholder_x_url'           => 'https://x.com/yourhandle',
    'placeholder_github_url'      => 'https://github.com/youraccount',
    'placeholder_linkedin_url'    => 'https://linkedin.com/company/yourcompany',
    'social_facebook_url'         => 'Facebook URL',
    'social_instagram_url'        => 'Instagram URL',
    'social_youtube_url'          => 'YouTube URL',
    'placeholder_facebook_url'    => 'https://facebook.com/yourpage',
    'placeholder_instagram_url'   => 'https://instagram.com/yourhandle',
    'placeholder_youtube_url'     => 'https://youtube.com/@yourchannel',

    // ----- SEO tab -----
    'tab_seo'                     => 'SEO',
    'seo_section'                 => 'Search engine & social meta',
    'seo_section_description'     => 'Controls the page title, meta description, keywords and the image shown when your homepage is shared on social media. Leave any field blank to keep the built-in default.',
    'seo_meta_title'              => 'Meta title',
    'seo_meta_title_placeholder'  => 'YourApp — Self-hosted CRM you own',
    'seo_meta_title_helper'       => 'Up to ~60 characters. Used as the browser tab title and the social share title. Blank = your app name.',
    'seo_meta_description'        => 'Meta description',
    'seo_meta_description_placeholder' => 'A self-hosted CRM with lead sources, automations and white-label multi-tenancy.',
    'seo_meta_description_helper' => 'Up to ~160 characters. Shown under your title in Google results and in social share previews.',
    'seo_meta_keywords'           => 'Meta keywords',
    'seo_meta_keywords_placeholder' => 'crm, self-hosted crm, lead management',
    'seo_meta_keywords_helper'    => 'Comma-separated. Optional — most search engines ignore these, but some directories use them.',
    'seo_og_image_url'            => 'Social share image URL',
    'seo_og_image_url_placeholder'=> 'https://yourdomain.com/og-image.png',
    'seo_og_image_url_helper'     => 'A 1200x630 PNG/JPG shown when your homepage is shared on social media (Open Graph / Twitter card). Blank = your brand logo.',
    'footer_tagline'              => 'Tagline (inside brand column)',
    'footer_tagline_placeholder'  => 'A self-hosted CRM you actually own.',
    'footer_copyright'            => 'Copyright line (bottom bar)',
    'footer_copyright_placeholder'=> 'Defaults to: © :year YourAppName. All rights reserved.',
    'footer_copyright_helper'     => 'Plain text or HTML. Leave blank for the default.',

    // ----- Translations tab -----
    'tab_translations'            => 'Translations',
    'per_language_overrides_description' => 'English is edited in the tabs above. Fill each non-English locale here to show translated copy to visitors on that language. Empty fields fall back to English automatically.',
    'tr_hero_headline'            => 'Hero headline',
    'tr_hero_headline_highlight'  => 'Hero highlight phrase',
    'tr_hero_subtext'             => 'Hero subtext',
    'tr_hero_cta_label'           => 'Hero CTA label',
    'tr_hero_note'                => 'Hero note (trust line)',
    'tr_hero_eyebrow'             => 'Hero eyebrow / badge',
    'tr_features_headline'        => 'Features headline',
    'tr_features_subtext'         => 'Features subtext',
    'tr_pricing_headline'         => 'Pricing headline',
    'tr_pricing_subtext'          => 'Pricing subtext',
    'tr_cta_headline'             => 'Closing CTA headline',
    'tr_cta_subtext'              => 'Closing CTA subtext',
    'tr_footer_tagline'           => 'Footer tagline',

    // ----- Save notification -----
    'saved_title'                 => 'Landing page content saved.',
    'saved_body'                  => 'Visit the landing page to verify your changes.',

    // ----- Header actions -----
    'preview'                     => 'Preview landing page',
    'save_changes'                => 'Save changes',

    // ----- Hero -----
    'hero_eyebrow'                => 'Marketing Content',
    'page_hero_title'             => 'Rewrite your public landing page without touching code',
    'hero_body_html'              => 'Every field below renders live on the public landing page at <code class="lpe-code">/</code>. Leave a field blank to fall back to the built-in default copy, so your site stays complete even while you edit.',
];
