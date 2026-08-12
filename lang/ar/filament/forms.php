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
    'nav_label'                 => 'النماذج',
    'model_label'               => 'نموذج',
    'plural_model_label'        => 'النماذج',

    // ----- Basic Details -----
    'form_name'                 => 'اسم النموذج',
    'slug'                      => 'المُعرّف الكنوي',
    'slug_help'                 => 'يُستخدم في الرابط العام.',
    'display_title'             => 'العنوان المعروض',
    'description'               => 'الوصف',
    'submit_button_label'       => 'تسمية زر الإرسال',
    'thank_you_message'         => 'رسالة الشكر',
    'redirect_url'              => 'رابط إعادة التوجيه بعد الإرسال',
    'multi_step_form'           => 'نموذج متعدد الخطوات',
    'multi_step_form_help'      => 'تجميع الحقول في خطوات مرقّمة.',
    'active'                    => 'نشط',

    // ----- Appearance -----
    'background_color'          => 'لون الخلفية',
    'background_image_url'      => 'رابط صورة الخلفية',
    'google_font_name'          => 'اسم خط Google',
    'google_font_name_placeholder' => 'مثل: Inter',
    'logo_url'                  => 'رابط الشعار',

    // ----- Pipeline Connection -----
    'pipeline'                  => 'خط الأنابيب',
    'stage'                     => 'المرحلة',

    // ----- Spam Protection -----
    'enable_recaptcha'          => 'تفعيل reCAPTCHA v3',
    'recaptcha_site_key'        => 'مفتاح موقع reCAPTCHA',
    'recaptcha_secret_key'      => 'مفتاح reCAPTCHA السري',

    // ----- Form Fields -----
    'fields_section_description' => 'اسحب وأفلت لإعادة الترتيب. يُضاف حقل موافقة GDPR دائمًا في الأخير ولا يمكن إزالته أو تحريره.',
    'add_field'                 => 'إضافة حقل',
    'field_type'                => 'نوع الحقل',
    'field_label'               => 'التسمية',
    'field_placeholder'         => 'نص توضيحي / مساعد',
    'field_key'                 => 'مفتاح الحقل',
    'field_key_placeholder'     => 'مثل: email, first_name',
    'field_key_help'            => 'يربط الحقل بخاصية العميل المحتمل.',
    'step_number'               => 'رقم الخطوة',
    'required'                  => 'إلزامي',
    'options'                   => 'الخيارات (واحد لكل سطر)',
    'options_help'              => 'أدخل كل خيار في سطر جديد.',
    'field_gdpr_consent_default_label' => 'موافقة GDPR',
    // Default sentence saved on a form's GDPR consent FormField row at
    // create/edit time. Distinct from the column-header label above —
    // this is the consent text the end-user ticks on the public form.
    'gdpr_default_field_label'  => 'أوافق على معالجة بياناتي الشخصية وفقًا لسياسة الخصوصية.',
    'field_gdpr_locked_suffix'  => '(مقفل — لا يمكن إزالته)',

    // ----- Embed Snippet -----
    'embed_section_description' => 'الصق هذا الرمز في أي صفحة ويب لعرض أداة التقاط عملاء محتملين عائمة.',
    'widget_embed_code'         => 'رمز تضمين الأداة',

    // ----- Live Preview -----
    'live_preview_description'  => 'معاينة مباشرة لكيفية ظهور النموذج للزوار.',
    'live_preview_open_in_new_tab' => 'فتح في علامة تبويب جديدة',
    'live_preview_iframe_title' => 'معاينة النموذج',

    // ----- Table columns -----
    'col_form_name'             => 'اسم النموذج',
    'col_slug'                  => 'المُعرّف الكنوي',
    'col_active'                => 'نشط',
    'col_multi_step'            => 'متعدد الخطوات',
    'col_submissions'           => 'عمليات الإرسال',
    'col_created'               => 'تاريخ الإنشاء',

    // ----- Empty State -----
    'empty_heading'             => 'لا توجد نماذج بعد',
    'empty_description'         => 'أنشئ نموذج التقاط قابلًا للتضمين في دقائق. ضع الرمز في أي موقع — تصل عمليات الإرسال كعملاء محتملين تلقائيًا.',
    'create_first_form'         => 'أنشئ أول نموذج',

    // ----- Sub-pages: Actions -----
    'view_public_form'          => 'عرض النموذج العام',
    'analytics'                 => 'التحليلات',
    'copy_embed_snippet'        => 'نسخ رمز التضمين',
    'live_preview'              => 'معاينة مباشرة',

    // ----- Embed snippet copy toast (Alpine.js $dispatch notify) -----
    'snippet_copied_toast'      => 'تم نسخ الرمز!',

    // ----- Embed snippet panel (Placeholder content) -----
    'save_form_first_for_snippet' => 'احفظ النموذج أولًا لتوليد رمز التضمين.',
    'copy'                      => 'نسخ',
    'copied'                    => 'تم النسخ!',
    'public_url'                => 'الرابط العام',

    // ----- Analytics page -----
    'analytics_back_to_form'        => '← العودة إلى النموذج',
    'analytics_breadcrumb_prefix'   => 'التحليلات: :name',
    'analytics_total_submissions'   => 'إجمالي عمليات الإرسال',
    'analytics_form_status'         => 'حالة النموذج',
    'analytics_status_active'       => 'نشط',
    'analytics_status_inactive'     => 'غير نشط',
    'analytics_total_fields'        => 'إجمالي الحقول',
    'analytics_submissions_30d'     => 'عمليات الإرسال (آخر 30 يومًا)',
    'analytics_field_completion'    => 'معدل إكمال الحقل',
    'analytics_step_dropoff'        => 'قمع التسرب بين الخطوات',
    'analytics_step_label'          => 'الخطوة :n',
    'analytics_step_reached'        => 'وصل :count',
    'analytics_embed_snippet'       => 'رمز التضمين',
    'analytics_embed_snippet_intro' => 'الصق هذا الرمز في أي موقع لتضمين أداة التقاط عملاء محتملين عائمة:',
    'analytics_public_form_url'     => 'رابط النموذج العام:',

];
