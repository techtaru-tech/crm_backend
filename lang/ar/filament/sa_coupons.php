<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin CouponResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_coupons.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_coupons.php.
*/

return [

    // ----- Navigation -----
    'nav_label'                   => 'القسائم',

    // ----- Code & description section -----
    'code_helper'                 => 'يكتب العملاء هذا عند الدفع. غير حسّاس لحالة الأحرف — يُخزَّن بأحرف كبيرة.',
    'description_helper'          => 'ملاحظة داخلية — لماذا هذا الرمز، أي حملة، عدد الاستردادات المتوقع.',

    // ----- Discount section -----
    'discount_type_helper'        => 'النسبة المئوية تخصم %. الثابت يخصم مبلغًا بعملة محددة. تمديد الفترة التجريبية يضيف أيامًا إلى الفترة التجريبية للمستأجر — بدون خصم مالي.',
    'discount_value_suffix_days'  => 'أيام',
    'discount_value_helper_percent' => '0–100. 100 تعني «مجاني خلال فترة الخصم».',
    'discount_value_helper_fixed' => 'مبلغ بعملة كاملة، مثل 20 لـ 20 دولارًا.',
    'discount_value_helper_trial' => 'أيام تُضاف إلى trial_ends_at للمستأجر عند الاسترداد.',
    'currency_helper'             => 'اتركه فارغًا للتطبيق على أي عملة.',

    // ----- Limits & targeting section -----
    'max_total_uses'              => 'الحد الأقصى لعدد الاستخدامات',
    'max_total_uses_placeholder'  => 'غير محدود',
    'max_total_uses_helper'       => 'إجمالي الاستردادات عبر جميع المستأجرين. اتركه فارغًا لجعله غير محدود.',
    'max_per_tenant'              => 'الحد الأقصى لكل مستأجر',
    'max_per_tenant_helper'       => 'عدد المرات التي يمكن لمستأجر واحد فيها استرداد هذا الرمز. 1 = للمرة الأولى فقط.',
    'applies_to_plans_placeholder'=> 'جميع الخطط',
    'applies_to_plans_helper'     => 'اتركه فارغًا للتطبيق على كل خطة.',

    // ----- Validity window section -----
    'starts_at_placeholder'       => 'الآن',
    'starts_at_helper'            => 'فارغ = نشط على الفور.',
    'ends_at_placeholder'         => 'لا تنتهي أبدًا',
    'ends_at_helper'              => 'فارغ = بدون انتهاء.',
    'is_active_helper'            => 'الرموز غير النشطة لا تُعتمد أبدًا، حتى ضمن نافذة التاريخ.',

    // ----- Table columns -----
    'column_type'                 => 'النوع',
    'column_value'                => 'القيمة',
    'column_uses'                 => 'الاستخدامات',
    'column_status'               => 'الحالة',
    'column_ends_at_placeholder'  => 'أبدًا',
    'trial_days_suffix'           => 'أيام تجريبية',

    // ----- Filters -----
    'filter_label_discount_type'  => 'النوع',
    'filter_active'               => 'نشط',
    'filter_active_yes'           => 'نعم',
    'filter_active_no'            => 'لا',

    // ----- Field labels (form + table) -----
    'code'                        => 'الرمز',
    'description'                 => 'الوصف',
    'discount_type'               => 'نوع الخصم',
    'discount_value'              => 'قيمة الخصم',
    'currency'                    => 'العملة',
    'applies_to_plans'            => 'ينطبق على الخطط',
    'starts_at'                   => 'يبدأ في',
    'ends_at'                     => 'ينتهي في',
    'is_active'                   => 'نشط',
    'created_at'                  => 'تاريخ الإنشاء',

    // ----- Model labels -----
    'model_label'                 => 'قسيمة',
    'plural_model_label'          => 'القسائم',

    // ----- Status badge labels (table column) -----
    'status_active'               => 'نشط',
    'status_scheduled'            => 'مجدول',
    'status_expired'              => 'منتهٍ',
    'status_exhausted'            => 'مُستنفَد',
    'status_inactive'             => 'غير نشط',

];
