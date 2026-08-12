<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadCaptureWidgetResource translation strings
|--------------------------------------------------------------------------
|
| Labels, defaults, placeholders and action copy for the Lead Capture
| Widgets resource at /admin/lead-capture-widgets.
| Consumed via __('filament/lead_capture_widgets.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                     => 'Lead Widgets',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                   => 'Lead Capture Widget',
    'plural_model_label'            => 'Lead Capture Widgets',

    // ─── Form fields ───────────────────────────────────────────────────
    'name'                          => 'Name',
    'headline'                      => 'Headline',
    'subheadline'                   => 'Subheadline',
    'button_text'                   => 'Button Text',
    'success_message'               => 'Success Message',
    'primary_color'                 => 'Primary Color',
    'text_color'                    => 'Text Color',
    'position'                      => 'Position',
    'created_at'                    => 'Created At',

    // ─── Form defaults & placeholders ──────────────────────────────────
    'default_headline'              => 'Get in touch',
    'subheadline_placeholder'       => 'Optional subtitle',
    'default_button_text'           => 'Send Message',
    'default_success_message'       => "Thank you! We'll be in touch shortly.",

    // ─── Form: appearance options ──────────────────────────────────────
    'position_bottom_right'         => 'Bottom Right',
    'position_bottom_left'          => 'Bottom Left',
    'position_top_right'            => 'Top Right',
    'position_top_left'             => 'Top Left',

    // ─── Form: form fields ─────────────────────────────────────────────
    'show_phone'                    => 'Show phone field',
    'require_phone'                 => 'Require phone',
    'show_company'                  => 'Show company field',
    'show_message'                  => 'Show message field',
    'require_message'               => 'Require message',

    // ─── Form: routing ─────────────────────────────────────────────────
    'route_leads_to_pipeline'       => 'Route leads to pipeline',
    'initial_stage'                 => 'Initial stage',

    // ─── Form: status ──────────────────────────────────────────────────
    'widget_is_active'              => 'Widget is active',

    // ─── Table columns ─────────────────────────────────────────────────
    'active'                        => 'Active',
    'leads_captured'                => 'Leads Captured',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_preview'                => 'Preview',
    'action_get_snippet'            => 'Get Embed Code',
    'snippet_modal_heading'         => 'Embed Code',
    'snippet_modal_description'     => 'Copy the snippet below and paste it into your website HTML, just before the closing </body> tag.',
    'snippet_label'                 => 'Widget Embed Snippet',
    'modal_close'                   => 'Close',
];
