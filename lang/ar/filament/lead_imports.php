<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadImportResource translation strings
|--------------------------------------------------------------------------
|
| Column labels for the Lead Imports resource at /admin/lead-imports.
| Consumed via __('filament/lead_imports.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'الاستيرادات',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'استيراد عملاء محتملين',
    'plural_model_label'                => 'استيرادات العملاء المحتملين',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'الحالة',
    'created_at'                        => 'تاريخ الإنشاء',
    'file'                              => 'الملف',
    'total'                             => 'الإجمالي',
    'imported'                          => 'مُستورَد',
    'dupes'                             => 'مكررات',
    'errors'                            => 'أخطاء',
    'imported_by'                       => 'استورده',

    // ─── CreateLeadImport upload step ─────────────────────────────────
    'csv_or_excel'                      => 'ملف CSV أو Excel',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_first_name'                  => 'الاسم الأول',
    'field_last_name'                   => 'الاسم الأخير',
    'field_full_name'                   => 'الاسم الكامل (يُقسَّم تلقائيًا)',
    'field_email'                       => 'البريد الإلكتروني',
    'field_phone'                       => 'الهاتف',
    'field_source'                      => 'المصدر',
    'field_status'                      => 'الحالة',
    'field_notes'                       => 'الملاحظات',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'تعذّر قراءة الملف: ',
    'notif_queued_title'                => 'تمت إضافة الاستيراد إلى الطابور! سيتم إضافة العملاء المحتملين قريبًا.',
    'import_csv_excel'                  => 'استيراد CSV/Excel',

    // ─── Job errors (persisted to lead_imports.errors JSON) ───────────
    'job_row_cap_exceeded'              => 'يتجاوز الاستيراد الحد الأقصى :max للصفوف (الملف يحتوي على :rows صف). يُرجى تقسيم الملف إلى استيرادات أصغر.',

    // ─── CreateLeadImport wizard ──────────────────────────────────────
    'step_1_heading'                    => 'الخطوة 1: رفع الملف',
    'upload_and_preview'                => 'الرفع ومعاينة الأعمدة',
    'step_2_heading'                    => 'الخطوة 2: المعاينة وتعيين الأعمدة',
    'preview_paragraph'                 => 'تم اكتشاف :total صف. تعرض المعاينة أدناه أول 10 صفوف. عيّن كل عمود CSV إلى حقل عميل محتمل (تُتخطى الأعمدة غير المعيَّنة).',
    'recognized_count'                  => 'مُعرَّف: :count',
    'unmapped_count'                    => 'غير مُعيَّن: :count',
    'option_skip'                       => '— تخطي —',
    'reject_reupload'                   => 'رفض / إعادة الرفع',
    'accept_start_import'               => 'قبول وبدء الاستيراد',
    'step_3_heading'                    => 'بدأ الاستيراد!',
    'step_3_body_html'                  => 'تمت إضافة مهمة الاستيراد إلى الطابور. سيتم إضافة العملاء المحتملين في الخلفية. يمكنك مراجعة صفحة <a href=":url" class="text-primary-600 underline">سجل الاستيراد</a> للتقدم.',
    'field_city' => 'المدينة',
    'field_priority' => 'الأولوية',
    'field_assigned_user' => 'المستخدم المُسند',
    'field_company' => 'الشركة',
    'field_deal_value' => 'الميزانية / قيمة الصفقة',
    'field_source_id' => 'معرّف العميل',
    'field_contacted_at' => 'آخر اتصال',
    'field_next_follow_up' => 'المتابعة التالية',
];
