<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BrandingSettingsPage strings — accessed via __('filament/branding_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: configures per-tenant branding & white-label
| values (app name, logo, favicon, colors, login page, email branding).
|
| Buyers translate or adapt by editing this file or copying it to
| lang/<locale>/filament/branding_settings.php and translating values.
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Branding',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Branding & White-label',

    // --- Section descriptions ------------------------------------------
    'identity_description'       => 'App name and logo — replaces "LeadHub" everywhere.',
    'colors_description'         => 'CSS custom properties are regenerated on save and injected into all tenant-scoped views.',
    'login_page_description'     => 'Customize the look of the login page for your users.',
    'email_branding_description' => 'These values appear in all outbound emails sent by the platform.',

    // --- Identity section ----------------------------------------------
    'application_name'        => 'Application Name',
    'upload_logo'             => 'Upload Logo (PNG/SVG, recommended 300×80px)',
    'upload_logo_helper'      => 'Upload replaces the URL below. Displayed in topbar, login page, emails, and PDFs.',
    'logo_url'                => 'Or Logo URL',
    'logo_url_placeholder'    => 'https://cdn.yourcompany.com/logo.png',
    'logo_url_helper'         => 'Provide a URL if not uploading a file. Leave blank to use the uploaded file.',
    'upload_favicon'          => 'Upload Favicon (ICO/PNG, 32×32px)',
    'upload_favicon_helper'   => 'Upload replaces the URL below.',
    'favicon_url'             => 'Or Favicon URL',
    'favicon_url_placeholder' => 'https://cdn.yourcompany.com/favicon.ico',
    'favicon_url_helper'      => 'Provide a URL if not uploading a file.',

    // --- Colors section ------------------------------------------------
    'primary_color' => 'Primary Color',
    'accent_color'  => 'Accent Color',

    // --- Login page section --------------------------------------------
    'background_color'         => 'Background Color',
    'background_color_helper'  => 'Used when no background image is set.',
    'upload_login_bg'          => 'Upload Login Background Image',
    'upload_login_bg_helper'   => 'Recommended: 1920×1080px. Upload replaces the URL below.',
    'login_bg_url'             => 'Or Background Image URL',
    'login_bg_url_placeholder' => 'https://cdn.yourcompany.com/login-bg.jpg',

    // --- Email branding section ----------------------------------------
    'email_sender_name'       => 'Email Sender Name',
    'email_from'              => 'From Email Address',
    'footer_text'             => 'Email & Form Footer Text',
    'footer_text_placeholder' => '© 2026 YourCompany. All rights reserved.',
    'footer_text_helper'      => 'Shown at the bottom of all outbound emails and public forms.',

    // --- Header actions ------------------------------------------------
    'save_branding' => 'Save Branding',

];
