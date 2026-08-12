<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — CompanyImportResource translation strings
|--------------------------------------------------------------------------
|
| Column labels + wizard copy for the Company Imports resource at
| /admin/company-imports. Consumed via __('filament/company_imports.<key>').
| Mirrors filament/lead_imports.php with Company-appropriate wording.
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'استيرادات الشركات',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'استيراد شركات',
    'plural_model_label'                => 'استيرادات الشركات',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'الحالة',
    'created_at'                        => 'تاريخ الإنشاء',
    'file'                              => 'الملف',
    'total'                             => 'الإجمالي',
    'imported'                          => 'مُستورَد',
    'dupes'                             => 'مكررات',
    'errors'                            => 'أخطاء',
    'imported_by'                       => 'استورده',

    // ─── Upload step ──────────────────────────────────────────────────
    'csv_or_excel'                      => 'ملف CSV أو Excel',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_name'                        => 'اسم الشركة',
    'field_domain'                      => 'النطاق',
    'field_industry'                    => 'القطاع',
    'field_size'                        => 'الحجم',
    'field_website'                     => 'الموقع الإلكتروني',
    'field_phone'                       => 'الهاتف',
    'field_address'                     => 'العنوان',
    'field_city'                        => 'المدينة',
    'field_country'                     => 'الدولة',
    'field_notes'                       => 'الملاحظات',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'تعذّر قراءة الملف: ',
    'notif_queued_title'                => 'تمت إضافة الاستيراد إلى الطابور! سيتم إضافة الشركات قريبًا.',
    'import_csv_excel'                  => 'استيراد CSV/Excel',

    // ─── Job errors (persisted to company_imports.errors JSON) ────────
    'job_row_cap_exceeded'              => 'يتجاوز الاستيراد الحد الأقصى :max للصفوف (الملف يحتوي على :rows صف). يُرجى تقسيم الملف إلى استيرادات أصغر.',

    // ─── Wizard ───────────────────────────────────────────────────────
    'step_1_heading'                    => 'الخطوة 1: رفع الملف',
    'upload_and_preview'                => 'الرفع ومعاينة الأعمدة',
    'step_2_heading'                    => 'الخطوة 2: المعاينة وتعيين الأعمدة',
    'preview_paragraph'                 => 'تم اكتشاف :total صف. تعرض المعاينة أدناه أول 10 صفوف. عيّن كل عمود CSV إلى حقل شركة (تُتخطى الأعمدة غير المعيَّنة).',
    'recognized_count'                  => 'مُعرَّف: :count',
    'unmapped_count'                     => 'غير مُعيَّن: :count',
    'option_skip'                       => '— تخطي —',
    'reject_reupload'                   => 'رفض / إعادة الرفع',
    'accept_start_import'               => 'قبول وبدء الاستيراد',
    'step_3_heading'                    => 'بدأ الاستيراد!',
    'step_3_body_html'                  => 'تمت إضافة مهمة الاستيراد إلى الطابور. سيتم إضافة الشركات في الخلفية. راجع قائمة <a href=":url" class="text-primary-600 underline">استيرادات الشركات</a> لمتابعة التقدم.',
];
