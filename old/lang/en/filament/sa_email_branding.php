<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin EmailBrandingPage — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_email_branding.<key>').
*/

return [
    'title'                              => 'Email Branding',
    'navigation_label'                   => 'Email Branding',

    // Header section
    'header_section_description'         => 'The coloured band at the top of every outbound email. Tenant-specific primary-colour overrides still win when a tenant sets its own in Branding — these values are the script-wide defaults.',
    'header_style_label'                 => 'Style',
    'header_style_solid'                 => 'Solid colour',
    'header_style_gradient'              => 'Linear gradient',
    'header_color_primary_gradient'      => 'Gradient start colour',
    'header_color_primary_solid'         => 'Background colour',
    'header_color_secondary_label'       => 'Gradient end colour',
    'header_gradient_angle_label'        => 'Gradient angle (degrees)',
    'header_gradient_angle_helper'       => '0 = bottom to top · 90 = left to right · 135 = diagonal (default) · 180 = top to bottom.',

    // Footer section
    'footer_section_description'         => 'The small band at the bottom of every email. Plain colour by design — a gradient here competes with the CTA block above.',
    'footer_color_label'                 => 'Background colour',
    'footer_text_color_label'            => 'Text colour',
    'footer_text_color_helper'           => 'Should contrast with the background above for readability.',

    // Notifications & actions
    'save_failed_title'                  => 'Could not save email branding',
    'save_failed_body'                   => 'Details in the server log. Most common cause: settings migration has not been run yet — run `php artisan migrate --force` on the server.',
    'saved_title'                        => 'Email branding saved',
    'saved_body'                         => 'New colours apply to every email sent from now on.',
    'action_save'                        => 'Save',

    // ─── Live preview strip ───
    'preview_title'                      => 'Preview',
    'preview_subtitle'                   => 'Mirror of the outbound email layout — updates as you change the pickers.',
    'preview_sample_greeting'            => 'Hello Jane,',
    'preview_sample_body'                => 'Sample email body text. Real messages replace this with their own content.',
    'preview_footer_reason'              => 'You received this email because you are a user of :app.',
];
