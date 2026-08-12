<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — سلاسل ترجمة RecurringInvoiceResource
|--------------------------------------------------------------------------
|
| إدارة "الفواتير المتكررة / المستحقات" الخاصة بالمستأجر. تُستخدم عبر
| __('filament/recurring_invoices.<key>').
|
*/

return [

    'nav_label'           => 'الفواتير المتكررة',
    'model_label'         => 'فاتورة متكررة',
    'plural_model_label'  => 'الفواتير المتكررة',

    // Form
    'section_schedule'      => 'جدول التكرار',
    'section_schedule_desc' => 'رسم ثابت شهري أو سنوي. ينشئ LeadHub فاتورة حقيقية في كل تاريخ تشغيل ويُذكّرك عند حلول موعد استحقاقها.',
    'field_lead'            => 'العضو / العميل',
    'field_company'         => 'الشركة (اختياري)',
    'field_title'           => 'الوصف',
    'field_amount'          => 'المبلغ',
    'field_currency'        => 'العملة',
    'field_interval'        => 'تُحصَّل كل',
    'interval_month'        => 'شهر',
    'interval_year'         => 'سنة',
    'field_anchor_day'      => 'يوم الفوترة من الشهر',
    'field_anchor_day_help' => 'اختياري. 1-28. يتم ضبط تاريخ التشغيل التالي على هذا اليوم في كل فترة.',
    'field_next_run_date'   => 'تاريخ التشغيل التالي',
    'field_due_days'        => 'تُستحق بعد (أيام)',
    'field_due_days_help'   => 'عدد الأيام بعد إنشاء كل فاتورة حتى تصبح مستحقة.',
    'field_auto_send'       => 'إرسال كل فاتورة تلقائيًا',
    'field_auto_send_help'  => 'تعليم كل فاتورة مُنشأة كمُرسَلة بدلاً من مسودة.',
    'field_active'          => 'نشطة',
    'field_notes'           => 'ملاحظات',

    // Table
    'col_title'    => 'الوصف',
    'col_member'   => 'العضو',
    'col_amount'   => 'المبلغ',
    'col_interval' => 'الفترة',
    'col_next_run' => 'التشغيل التالي',
    'col_active'   => 'نشطة',
];
