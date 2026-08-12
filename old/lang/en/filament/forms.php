<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| FormResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/forms.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/forms.php.
*/

return [

    // ----- Navigation -----
    'nav_label'                 => 'Forms',
    'model_label'               => 'Form',
    'plural_model_label'        => 'Forms',

    // ----- Basic Details -----
    'form_name'                 => 'Form Name',
    'slug'                      => 'Slug',
    'slug_help'                 => 'Used in the public URL.',
    'display_title'             => 'Display Title',
    'description'               => 'Description',
    'submit_button_label'       => 'Submit Button Label',
    'thank_you_message'         => 'Thank-You Message',
    'redirect_url'              => 'Redirect URL after submission',
    'multi_step_form'           => 'Multi-Step Form',
    'multi_step_form_help'      => 'Group fields into numbered steps.',
    'active'                    => 'Active',

    // ----- Appearance -----
    'background_color'          => 'Background Color',
    'background_image_url'      => 'Background Image URL',
    'google_font_name'          => 'Google Font Name',
    'google_font_name_placeholder' => 'e.g. Inter',
    'logo_url'                  => 'Logo URL',

    // ----- Pipeline Connection -----
    'pipeline'                  => 'Pipeline',
    'stage'                     => 'Stage',

    // ----- Spam Protection -----
    'enable_recaptcha'          => 'Enable reCAPTCHA v3',
    'recaptcha_site_key'        => 'reCAPTCHA Site Key',
    'recaptcha_secret_key'      => 'reCAPTCHA Secret Key',

    // ----- Form Fields -----
    'fields_section_description' => 'Drag and drop to reorder. The GDPR consent field is always added last and cannot be removed or edited.',
    'add_field'                 => 'Add Field',
    'field_type'                => 'Field Type',
    'field_label'               => 'Label',
    'field_placeholder'         => 'Placeholder / Helper',
    'field_key'                 => 'Field Key',
    'field_key_placeholder'     => 'e.g. email, first_name',
    'field_key_help'            => 'Maps field to lead property.',
    'step_number'               => 'Step #',
    'required'                  => 'Required',
    'options'                   => 'Options (one per line)',
    'options_help'              => 'Enter each option on a new line.',
    'field_gdpr_consent_default_label' => 'GDPR Consent',
    // Default sentence saved on a form's GDPR consent FormField row at
    // create/edit time. Distinct from the column-header label above —
    // this is the consent text the end-user ticks on the public form.
    'gdpr_default_field_label'  => 'I agree to the processing of my personal data in accordance with the Privacy Policy.',
    'field_gdpr_locked_suffix'  => '(locked — cannot be removed)',

    // ----- Embed Snippet -----
    'embed_section_description' => 'Paste this snippet into any webpage to display a floating lead capture widget.',
    'widget_embed_code'         => 'Widget Embed Code',

    // ----- Live Preview -----
    'live_preview_description'  => 'A live preview of how the form will appear to visitors.',
    'live_preview_open_in_new_tab' => 'Open in new tab',
    'live_preview_iframe_title' => 'Form Preview',

    // ----- Table columns -----
    'col_form_name'             => 'Form Name',
    'col_slug'                  => 'Slug',
    'col_active'                => 'Active',
    'col_multi_step'            => 'Multi-step',
    'col_submissions'           => 'Submissions',
    'col_created'               => 'Created',

    // ----- Empty State -----
    'empty_heading'             => 'No forms yet',
    'empty_description'         => 'Build an embeddable capture form in minutes. Drop the snippet into any site — submissions arrive as leads automatically.',
    'create_first_form'         => 'Create your first form',

    // ----- Sub-pages: Actions -----
    'view_public_form'          => 'View Public Form',
    'analytics'                 => 'Analytics',
    'copy_embed_snippet'        => 'Copy Embed Snippet',
    'live_preview'              => 'Live Preview',

    // ----- Embed snippet copy toast (Alpine.js $dispatch notify) -----
    'snippet_copied_toast'      => 'Snippet copied!',

    // ----- Embed snippet panel (Placeholder content) -----
    'save_form_first_for_snippet' => 'Save the form first to generate the embed snippet.',
    'copy'                      => 'Copy',
    'copied'                    => 'Copied!',
    'public_url'                => 'Public URL',

    // ----- Analytics page -----
    'analytics_back_to_form'        => '← Back to Form',
    'analytics_breadcrumb_prefix'   => 'Analytics: :name',
    'analytics_total_submissions'   => 'Total Submissions',
    'analytics_form_status'         => 'Form Status',
    'analytics_status_active'       => 'Active',
    'analytics_status_inactive'     => 'Inactive',
    'analytics_total_fields'        => 'Total Fields',
    'analytics_submissions_30d'     => 'Submissions (Last 30 Days)',
    'analytics_field_completion'    => 'Field Completion Rate',
    'analytics_step_dropoff'        => 'Step Drop-off Funnel',
    'analytics_step_label'          => 'Step :n',
    'analytics_step_reached'        => ':count reached',
    'analytics_embed_snippet'       => 'Embed Snippet',
    'analytics_embed_snippet_intro' => 'Paste this snippet into any website to embed the floating lead capture widget:',
    'analytics_public_form_url'     => 'Public form URL:',

];
