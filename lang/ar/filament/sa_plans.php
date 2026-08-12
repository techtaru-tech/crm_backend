<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin PlanResource — Filament admin strings
|------------------------------------------------------------
*/

return [

    // ----- Navigation -----
    'nav_label'                     => 'الخطط',
    'tabs_outer'                    => 'خطة',

    // ----- Tabs -----
    'tab_basics'                    => 'الأساسيات',
    'tab_limits'                    => 'الحدود',
    'tab_features'                  => 'الميزات',
    'tab_gateway_ids'               => 'معرّفات البوابات',

    // ----- Basics tab -----
    'plan_key'                      => 'مفتاح الخطة',
    'plan_key_helper'               => 'معرّف داخلي. أحرف صغيرة، بدون مسافات. يُستخدَم في الروابط والشيفرة البرمجية.',
    'name_helper'                   => 'يظهر في صفحة التسعير ولوحة الفوترة.',

    // ----- Pricing section -----
    'monthly_price'                 => 'السعر الشهري',
    'monthly_price_helper'          => 'المبلغ الشهري المتكرر.',
    'interval_monthly'              => 'شهرياً',
    'interval_yearly'               => 'سنوياً',
    'interval_weekly'               => 'أسبوعياً',
    'interval_daily'                => 'يومياً',
    // Short interval labels (badge column on table — short, capitalized).
    'interval_month'                => 'شهر',
    'interval_year'                 => 'سنة',
    'interval_helper'               => 'عدد مرات تكرار السعر بعد الفترة التجريبية.',
    'trial_days'                    => 'أيام الفترة التجريبية',
    'trial_days_suffix'             => 'أيام',
    'trial_days_helper'             => 'مدة الفترة التجريبية المجانية عندما يبدأ مساحة العمل بهذه الخطة. 0 = بدون فترة تجريبية — تتم الفوترة من اليوم الأول.',
    'annual_price'                  => 'السعر السنوي (مقدماً)',
    'annual_price_helper'           => 'اختياري. المبلغ الإجمالي مقدماً لمدة 12 شهراً. اتركه فارغاً إذا كنت لا تقدم فوترة سنوية لهذه الخطة.',
    'annual_discount_percent'       => 'نسبة الخصم السنوي %',
    'annual_discount_percent_helper'=> 'للعرض فقط — يغذي شارة «وفّر N%» على صفحة التسعير (مثلاً 20 = «وفّر 20% بالفوترة السنوية»).',

    // ----- Visibility section -----
    'active'                        => 'نشطة',
    'active_helper'                 => 'الخطط غير النشطة مخفية في كل مكان ولا يمكن الاشتراك بها.',
    'public'                        => 'عامة',
    'public_helper'                 => 'إظهار في صفحة التسعير العامة.',
    'highlight'                     => 'تمييز',
    'highlight_helper'              => 'يُعلِّم هذه الخطة باعتبارها الموصى بها (شارة على صفحة التسعير).',
    'sort_order'                    => 'ترتيب الفرز',
    'sort_order_helper'             => 'الأرقام الأقل تظهر أولاً.',

    // ----- Limits tab -----
    'limits_description'            => 'استخدم -1 لغير المحدود، أو 0 لتعطيل الميزة بالكامل، أو أي عدد صحيح موجب لحدّ صارم.',
    'limit_key'                     => 'مفتاح الحد',
    'limit_value'                   => 'القيمة',
    'add_limit'                     => 'إضافة حد',

    // ----- Features tab -----
    'features_description'          => 'بدّل الميزات التي تفتحها هذه الخطة. يجب أن تكون القيم «true» أو «false».',
    'feature_key'                   => 'مفتاح الميزة',
    'feature_enabled'               => 'مفعّلة',
    'add_feature'                   => 'إضافة ميزة',

    // ----- Gateway IDs tab -----
    'gateway_ids_description'       => 'اربط هذه الخطة بمعرّف المنتج/السعر في كل بوابة دفع. اتركه فارغاً إذا كانت البوابة معطّلة.',
    'stripe_price_id'               => 'معرّف سعر Stripe',
    'paddle_price_id'               => 'معرّف سعر Paddle',
    'razorpay_plan_id'              => 'معرّف خطة Razorpay',
    'paystack_plan_code'            => 'كود خطة Paystack',

    // ----- Table columns -----
    'column_number'                 => '#',
    'column_active'                 => 'نشطة',
    'column_public'                 => 'عامة',
    'column_highlight'              => 'تمييز',

    // ----- Filters -----
    'filter_active_label'           => 'نشطة',
    'filter_active'                 => 'نشطة',
    'filter_inactive'               => 'غير نشطة',
    'filter_label_interval'         => 'الفاصل الزمني',

    // ----- Field labels (form + table) -----
    'name'                          => 'الاسم',
    'description'                   => 'الوصف',
    'currency'                      => 'العملة',
    'interval'                      => 'الفاصل الزمني',
    'limits'                        => 'الحدود',
    'features'                      => 'الميزات',
    'price'                         => 'السعر',
    'updated_at'                    => 'تاريخ التحديث',

    // ----- Model labels -----
    'model_label'                   => 'خطة',
    'plural_model_label'            => 'الخطط',

];
