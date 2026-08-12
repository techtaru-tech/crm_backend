<?php

declare(strict_types=1);

/*
|-----------------------------------------------------------------
| SuperAdmin BrandingPage - Filament admin strings
|-----------------------------------------------------------------
| Accessed via __('filament/sa_branding.<key>').
| The page lives at /super-admin/branding and edits the
| script-wide BrandingSettings rows (app name, primary / accent
| colours, logo, favicon, login background, footer text).
*/

return [
    'navigation_label'              => 'Branding',
    'title'                         => 'Branding',

    // ── Identity section ──────────────────────────────────────
    'section_identity'              => 'Brand identity',
    'section_identity_description'  => 'The name and visuals that represent your platform across the public landing page, login screen, and tenant panels.',
    'app_name_label'                => 'Brand name',
    'app_name_helper'               => 'The product name shown in the browser tab, login page, and panel header. Change from "LeadHub" to your own brand.',
    'logo_url_label'                => 'Logo URL',
    'logo_url_helper'               => 'Public URL to your brand logo (PNG or SVG). Leave blank to use the default mark.',
    'logo_file_label'               => 'Or upload a logo file',
    'logo_file_helper'              => 'PNG / SVG / JPG up to 2 MB. Uploading replaces the URL above with the file you upload.',
    'favicon_url_label'             => 'Favicon URL',
    'favicon_url_helper'            => 'Public URL to a 32x32 (or larger) ICO / PNG for the browser tab.',
    'favicon_file_label'            => 'Or upload a favicon file',
    'favicon_file_helper'           => 'Square PNG / ICO / SVG (32x32 or larger). Uploading replaces the URL above.',
    'footer_text_label'             => 'Footer text',
    'footer_text_helper'            => 'Short tagline or copyright line shown in the public footer. Markdown is not parsed.',

    // ── Colours section ───────────────────────────────────────
    'section_colors'                => 'Brand colours',
    'section_colors_description'    => 'Colour palette applied to buttons, links, and accents across both the public site and the admin panels.',
    'primary_color_label'           => 'Primary colour',
    'accent_color_label'            => 'Accent colour',

    // ── Login screen section ──────────────────────────────────
    'section_login'                 => 'Login screen',
    'section_login_description'     => 'Customise the background of the /admin and /super-admin login screens.',
    'login_bg_color_label'          => 'Login background colour',
    'login_bg_image_label'          => 'Login background image URL',

    // ── Actions / notifications ───────────────────────────────
    'action_save'                   => 'Save branding',
    'saved_title'                   => 'Branding saved',
    'saved_body'                    => 'Your changes are live across the public site and the admin panels.',
    'save_failed_title'             => 'Could not save branding',
    'save_failed_body'              => 'The save failed and was rolled back. Check storage/logs/laravel.log for the detailed error.',
];
