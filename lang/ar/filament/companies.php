<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — سلاسل ترجمة CompanyResource
|--------------------------------------------------------------------------
|
| التسميات والخيارات ونصوص المرشحات ونصوص الإجراءات لمورد الشركات على
| /admin/companies ومدير علاقاته LeadsRelationManager (جهات الاتصال).
| يُستهلك عبر __('filament/companies.<key>').
|
*/

return [

    // ─── تسميات النموذج ───────────────────────────────────────────────
    'model_label'                       => 'شركة',
    'plural_model_label'                => 'الشركات',

    // ─── التنقل ───────────────────────────────────────────────────────
    'nav_label'                         => 'الشركات',

    // ─── النموذج: معلومات الشركة ──────────────────────────────────────
    'domain'                            => 'النطاق',
    'domain_placeholder'                => 'acme.com',
    'account_owner'                     => 'مالك الحساب',

    // ─── خيارات القطاع ────────────────────────────────────────────────
    'industry_technology'               => 'التكنولوجيا',
    'industry_finance'                  => 'التمويل',
    'industry_healthcare'               => 'الرعاية الصحية',
    'industry_retail'                   => 'البيع بالتجزئة',
    'industry_manufacturing'            => 'التصنيع',
    'industry_education'                => 'التعليم',
    'industry_real_estate'              => 'العقارات',
    'industry_construction'             => 'البناء',
    'industry_hospitality'              => 'الضيافة',
    'industry_transportation'           => 'النقل',
    'industry_energy'                   => 'الطاقة',
    'industry_media'                    => 'الإعلام',
    'industry_nonprofit'                => 'غير ربحي',
    'industry_government'               => 'حكومي',
    'industry_other'                    => 'أخرى',

    // ─── خيارات الحجم ────────────────────────────────────────────────
    'size_small'                        => 'صغيرة (1-49)',
    'size_medium'                       => 'متوسطة (50-249)',
    'size_large'                        => 'كبيرة (250-999)',
    'size_enterprise'                   => 'مؤسسية (+1000)',
    'size_small_short'                  => 'صغيرة',
    'size_medium_short'                 => 'متوسطة',
    'size_large_short'                  => 'كبيرة',
    'size_enterprise_short'             => 'مؤسسية',

    // ─── أعمدة الجدول ────────────────────────────────────────────────
    'contacts'                          => 'جهات الاتصال',
    'open_pipeline'                     => 'خط الأنابيب المفتوح',
    'owner'                             => 'المالك',

    // ─── تسميات المرشحات ────────────────────────────────────────────
    'filter_label_status'               => 'الحالة',

    // ─── LeadsRelationManager (جهات الاتصال) ────────────────────────
    'relation_title'                    => 'جهات الاتصال',
    'lead_status_new'                   => 'جديد',
    'lead_status_contacted'             => 'تم الاتصال به',
    'lead_status_qualified'             => 'مؤهَّل',
    'lead_status_converted'             => 'محوَّل',
    'lead_status_lost'                  => 'مفقود',
    'assigned_to'                       => 'مُسنَد إلى',
    'name'                              => 'الاسم',
    'stage'                             => 'المرحلة',
    'deal_value'                        => 'قيمة الصفقة',
    'action_add_contact'                => 'إضافة جهة اتصال',
    'action_view'                       => 'عرض',

    // ─── صفحة العرض: قسم معلومات الشركة ─────────────────────────────
    'section_company_info'              => 'معلومات الشركة',
    'section_deal_summary'              => 'ملخص الصفقة',
    'section_contacts'                  => 'جهات الاتصال',
    'section_notes'                     => 'الملاحظات',
    'website'                           => 'الموقع الإلكتروني',
    'phone'                             => 'الهاتف',
    'industry'                          => 'القطاع',
    'size'                              => 'الحجم',
    'address'                           => 'العنوان',
    'won_deals'                         => 'الصفقات المربوحة',

    // ─── صفحة العرض: علامات التبويب ─────────────────────────────────
    'tab_contacts_with_count'           => 'جهات الاتصال (:count)',
    'tab_notes'                         => 'الملاحظات',

    // ─── صفحة العرض: جدول جهات الاتصال ──────────────────────────────
    'no_contacts'                       => 'لا توجد جهات اتصال مرتبطة بهذه الشركة بعد.',
    'col_name'                          => 'الاسم',
    'col_email'                         => 'البريد الإلكتروني',
    'col_status'                        => 'الحالة',
    'col_deal_value'                    => 'قيمة الصفقة',
    'lead_no_name'                      => '(بدون اسم)',

    // ─── صفحة العرض: الملاحظات ──────────────────────────────────────
    'no_notes'                          => 'لا توجد ملاحظات بعد.',

    // ─── تسميات حقول النموذج (المورد) ───────────────────────────────
    'field_name_label'                  => 'الاسم',
    'field_industry_label'              => 'القطاع',
    'field_size_label'                  => 'الحجم',
    'field_website_label'               => 'الموقع الإلكتروني',
    'field_phone_label'                 => 'الهاتف',
    'field_address_label'               => 'العنوان',
    'field_city_label'                  => 'المدينة',
    'field_country_label'               => 'الدولة',
    'field_notes_label'                 => 'الملاحظات',

    // ─── تسميات حقول النموذج (LeadsRelationManager) ────────────────
    'field_first_name_label'            => 'الاسم الأول',
    'field_last_name_label'             => 'اسم العائلة',
    'field_email_label'                 => 'البريد الإلكتروني',
    'field_lead_phone_label'            => 'الهاتف',
    'field_source_label'                => 'المصدر',
    'field_status_label'                => 'الحالة',

    // ─── تسميات أعمدة الجدول ────────────────────────────────────────
    'col_company_name'                  => 'الاسم',
    'col_domain'                        => 'النطاق',
    'col_industry'                      => 'القطاع',
    'col_size'                          => 'الحجم',
    'col_city'                          => 'المدينة',
    'col_country'                       => 'الدولة',
    'col_created_at'                    => 'تاريخ الإنشاء',

    // ─── تسميات أعمدة الجدول (LeadsRelationManager) ────────────────
    'col_lead_email'                    => 'البريد الإلكتروني',
    'col_lead_phone'                    => 'الهاتف',
    'col_lead_status'                   => 'الحالة',
    'col_lead_created_at'               => 'تاريخ الإنشاء',
];
