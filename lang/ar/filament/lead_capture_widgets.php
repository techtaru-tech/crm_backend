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
    'nav_label'                     => 'أدوات العملاء المحتملين',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                   => 'أداة التقاط عملاء محتملين',
    'plural_model_label'            => 'أدوات التقاط العملاء المحتملين',

    // ─── Form fields ───────────────────────────────────────────────────
    'name'                          => 'الاسم',
    'headline'                      => 'العنوان الرئيسي',
    'subheadline'                   => 'العنوان الفرعي',
    'button_text'                   => 'نص الزر',
    'success_message'               => 'رسالة النجاح',
    'primary_color'                 => 'اللون الأساسي',
    'text_color'                    => 'لون النص',
    'position'                      => 'الموضع',
    'created_at'                    => 'تاريخ الإنشاء',

    // ─── Form defaults & placeholders ──────────────────────────────────
    'default_headline'              => 'تواصل معنا',
    'subheadline_placeholder'       => 'عنوان فرعي اختياري',
    'default_button_text'           => 'إرسال الرسالة',
    'default_success_message'       => 'شكرًا لك! سنتواصل معك قريبًا.',

    // ─── Form: appearance options ──────────────────────────────────────
    'position_bottom_right'         => 'أسفل اليمين',
    'position_bottom_left'          => 'أسفل اليسار',
    'position_top_right'            => 'أعلى اليمين',
    'position_top_left'             => 'أعلى اليسار',

    // ─── Form: form fields ─────────────────────────────────────────────
    'show_phone'                    => 'إظهار حقل الهاتف',
    'require_phone'                 => 'الهاتف إلزامي',
    'show_company'                  => 'إظهار حقل الشركة',
    'show_message'                  => 'إظهار حقل الرسالة',
    'require_message'               => 'الرسالة إلزامية',

    // ─── Form: routing ─────────────────────────────────────────────────
    'route_leads_to_pipeline'       => 'توجيه العملاء المحتملين إلى خط الأنابيب',
    'initial_stage'                 => 'المرحلة الأولية',

    // ─── Form: status ──────────────────────────────────────────────────
    'widget_is_active'              => 'الأداة نشطة',

    // ─── Table columns ─────────────────────────────────────────────────
    'active'                        => 'نشط',
    'leads_captured'                => 'العملاء المحتملون الملتقَطون',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_preview'                => 'معاينة',
    'action_get_snippet'            => 'احصل على رمز التضمين',
    'snippet_modal_heading'         => 'رمز التضمين',
    'snippet_modal_description'     => 'انسخ الرمز أدناه والصقه في HTML موقعك، قبل وسم </body> الإغلاقي مباشرة.',
    'snippet_label'                 => 'رمز تضمين الأداة',
    'modal_close'                   => 'إغلاق',
];
