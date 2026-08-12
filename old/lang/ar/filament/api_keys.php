<?php

declare(strict_types=1);

return [

    // ----- Navigation -----
    'nav_label'         => 'مفاتيح API',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'        => 'مفتاح API',
    'plural_model_label' => 'مفاتيح API',

    // ----- Table column labels -----
    'col_name'           => 'الاسم',
    'suffix_per_hour'    => '/ساعة',

    // ----- Form fields -----
    'key_name'          => 'اسم المفتاح',
    'key_name_placeholder' => 'مثال: تكامل Zapier',
    'key_name_help'     => 'تسمية وصفية لتعريف هذا المفتاح.',
    'permissions'       => 'الأذونات',
    'permissions_help'  => 'اتركه فارغًا لمنح جميع الأذونات.',
    'rate_limit'        => 'حد المعدل (طلب/ساعة)',
    'expires_at'        => 'تاريخ الانتهاء',
    'expires_at_help'   => 'اتركه فارغًا بلا انتهاء.',

    // ----- Table -----
    'col_key_prefix'    => 'بادئة المفتاح',
    'col_rate_limit'    => 'حد المعدل',
    'col_last_used'     => 'آخر استخدام',
    'col_expires'       => 'تاريخ الانتهاء',
    'col_active'        => 'نشط',
    'col_created'       => 'تاريخ الإنشاء',
    'never'             => 'أبدًا',

    // ----- Actions -----
    'revoke'            => 'إبطال',
    'new_api_key'       => 'مفتاح API جديد',

    // ----- Empty state -----
    'empty_heading'     => 'لا توجد مفاتيح API بعد',
    'empty_description' => 'أنشئ مفتاح API للتكامل مع الخدمات الخارجية عبر واجهة API بنمط REST.',

    // ----- Blade view: new-key banner -----
    'banner_title'        => 'تم إنشاء مفتاح API بنجاح',
    'banner_msg_lede'     => 'انسخ مفتاح API الجديد أدناه.',
    'banner_msg_only_once' => 'هذه هي المرة الوحيدة التي سيُعرض فيها.',
    'banner_msg_store_safe' => 'احفظه في مكان آمن — لن تتمكن من رؤيته مرة أخرى.',
    'banner_copy_button'  => 'نسخ المفتاح',
    'banner_copied_label' => 'تم النسخ.',

];
