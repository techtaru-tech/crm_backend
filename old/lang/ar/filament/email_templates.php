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
    'nav_label'                         => 'قوالب البريد الإلكتروني',
    'model_label'                       => 'قالب بريد إلكتروني',
    'plural_model_label'                => 'قوالب البريد الإلكتروني',

    // ─── Form ──────────────────────────────────────────────────────────
    'template_name'                     => 'اسم القالب',
    'email_subject'                     => 'موضوع البريد',
    'subject_helper'                    => 'يدعم: {{lead.first_name}}, {{lead.last_name}}, {{lead.email}}, {{lead.company}}, {{lead.source}}, {{lead.status}}',
    'html_body'                         => 'نص HTML',
    'html_body_helper'                  => 'استخدم {{lead.first_name}}, {{lead.email}}, إلخ للتخصيص.',
    'plain_text_body'                   => 'نص عادي (اختياري — يُولَّد تلقائيًا إذا تُرك فارغًا)',

    // ─── Table columns ─────────────────────────────────────────────────
    'name'                              => 'الاسم',
    'subject'                           => 'الموضوع',
    'created'                           => 'تاريخ الإنشاء',
];
