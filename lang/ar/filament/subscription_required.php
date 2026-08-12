<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SubscriptionRequired — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/subscription_required.<key>').
*/

return [
    'title'                    => 'الاشتراك مطلوب',

    // Reasons - headings
    'heading_trial_expired'    => 'انتهت فترتك التجريبية',
    'heading_cancelled'        => 'تم إلغاء اشتراكك',
    'heading_expired'          => 'انتهى اشتراكك',
    'heading_suspended'        => 'مساحة العمل هذه موقوفة',
    'heading_default'          => 'الاشتراك مطلوب',

    // Reasons - subheadings
    'subheading_trial_expired' => 'انتهت فترتك التجريبية لمدة 14 يومًا. اختر خطة أدناه لمتابعة استخدام LeadHub.',
    'subheading_cancelled'     => 'تم إلغاء اشتراكك. أعد تفعيله لاستعادة الوصول.',
    'subheading_expired'       => 'انقضى دفع اشتراكك. يُرجى التجديد لاستعادة الوصول.',
    'subheading_suspended'     => 'يُرجى التواصل مع مشرفك.',
    'subheading_default'       => 'يُرجى اختيار خطة للمتابعة.',

    // Footer / actions
    'sign_out'                 => 'تسجيل الخروج',

    // ─── Page body (resources/views/filament/pages/subscription-required.blade.php) ──
    'current_status_prefix'    => 'الحالة الحالية:',
    'most_popular_tag'         => 'الأكثر شهرة',
    'price_per_interval'       => '/:interval',
    'seats_unlimited'          => 'مقاعد فريق غير محدودة',
    'seats_count'              => ':count مقعد فريق',
    'leads_unlimited'          => 'عملاء محتملون غير محدودين',
    'leads_count'              => ':count عميل محتمل',
    'forms_unlimited'          => 'نماذج غير محدودة',
    'forms_count'              => ':count نموذج',
    'feature_integrations'     => 'التكاملات',
    'feature_api_access'       => 'الوصول إلى API',
    'feature_custom_domain'    => 'نطاق مخصص',
    'pay_with_label'           => 'الدفع عبر :gateway',
    'contact_sales_btn'        => 'تواصل مع المبيعات — :plan',
    'switch_accounts_label'    => 'هل تريد التبديل بين الحسابات؟',
    'sales_mailto_subject'     => 'الترقية إلى :plan',
    'interval_month'           => 'شهر',
    'interval_year'            => 'سنة',
    'interval_week'            => 'أسبوع',
    'interval_day'             => 'يوم',
];
