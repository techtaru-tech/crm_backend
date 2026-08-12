<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — EmailTemplateResource translation strings
|--------------------------------------------------------------------------
|
| Labels and helper text for the Email Templates resource at
| /admin/email-templates.
| Consumed via __('filament/email_templates.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Email Templates',
    'model_label'                       => 'Email Template',
    'plural_model_label'                => 'Email Templates',

    // ─── Form ──────────────────────────────────────────────────────────
    'template_name'                     => 'Template Name',
    'email_subject'                     => 'Email Subject',
    'subject_helper'                    => 'Supports: {{lead.first_name}}, {{lead.last_name}}, {{lead.email}}, {{lead.company}}, {{lead.source}}, {{lead.status}}',
    'html_body'                         => 'HTML Body',
    'html_body_helper'                  => 'Use {{lead.first_name}}, {{lead.email}}, etc. for personalization.',
    'plain_text_body'                   => 'Plain Text Body (optional — auto-generated if blank)',

    // ─── Table columns ─────────────────────────────────────────────────
    'name'                              => 'Name',
    'subject'                           => 'Subject',
    'created'                           => 'Created',
];
