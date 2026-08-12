<?php

declare(strict_types=1);

return [

    // ─── Navigation / page header ─────────────────────────────────────
    'nav_label'                 => 'السوق',
    'title'                     => 'سوق القوالب',
    'heading'                   => 'سوق القوالب',
    'subheading'                => 'ثبّت أتمتة ومسارات قمعية وتسلسلات بريد إلكتروني ونماذج جاهزة شاركتها مساحات عمل أخرى. العناصر المميّزة مُدقّقة من قِبل المشغّل.',

    // ─── Install flow notifications ───────────────────────────────────
    'notif_not_found_title'     => 'القالب غير موجود أو غير منشور.',
    'notif_no_workspace_title'  => 'لا يوجد سياق مساحة عمل.',
    'notif_paid_title'          => 'القوالب المدفوعة غير مدعومة بعد',
    'notif_paid_body'           => 'يكلف هذا القالب :price :currency. سيتوفر مسار الدفع في تحديث لاحق.',
    'notif_installed_title'     => 'تم تثبيت القالب',
    'notif_installed_body'      => 'تم إنشاء :count سجل من النوع :type. اعثر عليه في القسم المطابق — راجع المحتوى قبل التفعيل.',
    'notif_install_failed_title' => 'فشل التثبيت',
    'notif_install_unknown_error' => 'خطأ غير معروف',

    // wire:confirm
    'confirm_install_template'  => 'هل تريد تثبيت «:name» في مساحة عملك؟ ستجده في القسم المطابق، مضبوطًا كمسوّدة لكي تتمكن من مراجعته قبل التفعيل.',

    // ─── Page body (resources/views/filament/pages/marketplace.blade.php) ──
    'search_placeholder'        => 'ابحث في القوالب بالاسم أو الوصف…',
    'all_types'                 => 'جميع الأنواع',
    'all_categories'            => 'جميع الفئات',
    'featured_only'             => 'المميّزة فقط',
    'empty_no_matches_title'    => 'لا توجد نتائج مطابقة',
    'empty_no_templates_title'  => 'السوق فارغ',
    'empty_no_matches_body'     => 'جرّب إزالة عوامل التصفية أو استخدام كلمات بحث مختلفة.',
    'empty_no_templates_body'   => 'لم يُنشر أي قالب بعد. كن الأول — أنشئ قالبًا وشاركه من محرر الأتمتة أو النماذج أو تسلسلات البريد الإلكتروني.',
    'templates_count'           => '{1} :count قالب متاح|[2,*] :count قالب متاح',
    'featured_tag'              => '⭐ مميّز',
    'by_owner_prefix'           => 'بواسطة',
    'installs_count'            => '{1} :count تثبيت|[2,*] :count تثبيت',
    'free_price'                => 'مجاني',
    'install_free_btn'          => 'تثبيت مجاني',
    'install_paid_btn'          => 'تثبيت — :price :currency',
];
