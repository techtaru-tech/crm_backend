<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Public lead-capture widget (app/Http/Controllers/PublicWidgetController.php)
|------------------------------------------------------------
| Strings rendered into the embeddable widget loader.js served
| to third-party customer sites, plus the standalone widget
| preview HTML page. Accessed via __('public_widget.<key>').
*/

return [

    // ─── Default widget config (used when tenant hasn't customised) ───
    'default_headline'        => 'Get in touch',
    'default_button_label'    => 'Send Message',
    'default_success_message' => "Thank you! We'll be in touch.",

    // ─── Embedded form field placeholders ─────────────────────────────
    'field_first_name' => 'First name *',
    'field_email'      => 'Email address *',
    'field_phone'      => 'Phone',
    'field_company'    => 'Company',
    'field_message'    => 'Message',

    // ─── Embedded form runtime states ─────────────────────────────────
    'try_again' => 'Try again',

    // ─── Preview HTML page ────────────────────────────────────────────
    'preview_title'   => 'Widget preview',
    'preview_suffix'  => ' — preview',
    'preview_banner'  => '🔍 Widget Preview — this is a test page showing how the widget looks when embedded on a real site.',

    'preview_mock_h1'    => 'Example Customer Website',
    'preview_mock_intro' => 'This is a mock page. The widget should appear in the configured corner.',

    'preview_how_heading'              => 'How the preview works',
    'preview_how_paragraph_html'       => 'The widget loader is embedded at the bottom of this page. Click it, fill out the form, and submit — a real lead <strong>will</strong> be captured in your workspace (use test data).',
    'preview_main_content_placeholder' => "Your site's main content would go here",

    'preview_settings_heading'       => 'Widget settings in effect',
    'preview_setting_position'       => 'Position:',
    'preview_setting_primary_colour' => 'Primary colour:',
    'preview_setting_active'         => 'Active in production:',

    'preview_active_yes' => 'yes',
    'preview_active_no'  => 'no (will not render on real site)',
];
