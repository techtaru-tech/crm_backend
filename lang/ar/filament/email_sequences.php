<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| EmailSequenceResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/email_sequences.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'          => 'تسلسلات البريد الإلكتروني',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'        => 'تسلسل بريد إلكتروني',
    'plural_model_label' => 'تسلسلات البريد الإلكتروني',

    // ----- Form fields -----
    'status'             => 'الحالة',

    // ----- itemLabel template strings -----
    'item_label_step_prefix' => 'خطوة — ',
    'item_label_day_short'   => 'ي',
    'item_label_hour_short'  => 'س',
    'item_label_no_subject'  => '(بدون موضوع)',

    // ----- Sequence Info -----
    'sequence_name'      => 'اسم التسلسل',
    'description'        => 'الوصف',

    // ----- Behavior -----
    'stop_on_reply'      => 'الإيقاف عند رد العميل المحتمل',
    'stop_on_reply_help' => 'إلغاء الاشتراك تلقائيًا عند تسجيل بريد وارد من العميل المحتمل.',
    'stop_on_won'        => 'الإيقاف عند كسب العميل المحتمل',
    'stop_on_won_help'   => 'إلغاء الاشتراك تلقائيًا عندما تصبح حالة العميل المحتمل «تم الكسب».',

    // ----- Steps -----
    'steps_description'  => 'تُرسَل رسائل البريد من أعلى إلى أسفل. يُحتسب التأخير من الخطوة السابقة (أو من تاريخ التسجيل للخطوة الأولى).',
    'add_step'           => 'إضافة خطوة',
    'delay_days'         => 'التأخير (أيام)',
    'delay_hours'        => 'التأخير (ساعات)',
    'load_template'      => 'تحميل من قالب',
    'load_template_help' => 'اختر قالب بريد محفوظًا لملء الموضوع والنص أدناه. يمكنك تعديلهما بعد التحميل.',
    'subject'            => 'الموضوع',
    'subject_help'       => 'العناصر النائبة: {first_name}, {last_name}, {company}, {email}',
    'body'               => 'النص',
    'body_help'          => 'العناصر النائبة: {first_name}, {last_name}, {company}, {email}',

    // ----- Filter labels -----
    'filter_label_status' => 'الحالة',

    // ----- Table -----
    'col_name'           => 'الاسم',
    'col_status'         => 'الحالة',
    'col_steps'          => 'الخطوات',
    'col_active_enroll'  => 'التسجيلات النشطة',
    'col_completed'      => 'مكتملة',
    'col_created'        => 'تاريخ الإنشاء',

    // ----- Row actions -----
    'preview'            => 'معاينة',
    'preview_modal_heading' => 'معاينة: :name',
    'preview_description' => 'يُعرَض استبدال الرموز ببيانات عيّنة — {first_name}=محمد، {last_name}=أحمد، {company}=شركة آكمي، {email}=mohammed@acme.com.',
    'preview_close'      => 'إغلاق',
    'send_test'          => 'إرسال اختباري',
    'send_test_to'       => 'إرسال اختبار إلى',
    'which_step'         => 'أي خطوة؟',
    'duplicate'          => 'تكرار',

    // ----- Notifications -----
    'notif_step_not_found'        => 'الخطوة غير موجودة.',
    'notif_test_email_sent'       => 'تم إرسال رسالة الاختبار إلى :email',
    'notif_test_email_failed'     => 'فشل الإرسال: :error',
    'notif_sequence_duplicated'   => 'تم تكرار التسلسل.',
    'notif_duplicate_failed'      => 'تعذر تكرار التسلسل.',

    // ----- Enrollments relation manager -----
    'enrollments_relation_title'  => 'التسجيلات',
    'col_lead'                    => 'العميل المحتمل',
    'col_email'                   => 'البريد الإلكتروني',
    'col_step'                    => 'الخطوة',
    'col_next_send'               => 'الإرسال التالي',
    'col_next_send_at'            => 'تاريخ الإرسال التالي',
    'col_enrolled_at'             => 'تاريخ التسجيل',

    // ----- Preview view -----
    'preview_delay_label'         => 'التأخير',
    'preview_sample_lead'         => 'معاينة بعميل محتمل تجريبي',
    'preview_no_steps'            => 'لم تُحدَّد أي خطوات بعد. أضف خطوات في صفحة التحرير.',

    // ----- Preview / test send micro-strings -----
    'preview_delay_immediate'     => 'فوري',
    'test_send_step_option_label' => 'الخطوة :step — ',
    'test_subject_prefix'         => '[اختبار] :subject',
    'preview_sample_first_name'   => 'محمد',
    'preview_sample_last_name'    => 'أحمد',
    'preview_sample_company_name' => 'شركة آكمي',
    'preview_sample_email'        => 'mohammed@acme.com',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'          => 'مسودة',
    'option_status_active'         => 'نشط',
    'option_status_paused'         => 'متوقف',
    'option_enrollment_active'     => 'نشط',
    'option_enrollment_completed'  => 'مكتمل',
    'option_enrollment_replied'    => 'تم الرد',
    'option_enrollment_unenrolled' => 'تم إلغاء التسجيل',

    // ─── Status badge labels (table column) ────────────────────────
    'status_draft'                 => 'مسودة',
    'status_active'                => 'نشط',
    'status_paused'                => 'متوقف',

    // ─── Duplicate action copy ─────────────────────────────────────
    'duplicate_copy_suffix'        => '(نسخة)',

    // ─── Delay format short tokens (preview) ───────────────────────
    'delay_days_short'             => 'ي',
    'delay_hours_short'            => 'س',
];
