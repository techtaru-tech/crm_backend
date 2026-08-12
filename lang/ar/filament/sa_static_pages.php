<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin StaticPageResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_static_pages.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_static_pages.php.
*/

return [

    // ----- Resource labels -----
    'static_page'                  => 'صفحة ثابتة',
    'static_pages'                 => 'الصفحات الثابتة',
    'tabs_outer'                   => 'اللغة',

    // ----- Page content section -----
    'page_content_description'     => 'العنوان والنص الذي يظهر في /pages/{slug}. يدعم المحرر الغني العناوين والقوائم والروابط والتنسيق الأساسي.',
    'slug_helper'                  => 'آمن للرابط. يُملأ تلقائيًا من العنوان.',
    'excerpt_helper'               => 'سطر واحد اختياري يُعرَض تحت العنوان في الصفحة.',
    'content'                      => 'المحتوى',

    // ----- SEO section -----
    'seo_description'              => 'وسوم الميتا التي تستخدمها محركات البحث ومعاينات وسائل التواصل.',
    'meta_description_helper'      => 'جملة أو جملتان. تُستخدم لـ <meta name="description"> ومعاينات وسائل التواصل. 150 حرفًا مثالي.',

    // ----- Translations section -----
    'translations_description'     => 'تجاوزات لكل لغة. املأ لغة غير إنجليزية لعرض نص مترجم بتلك اللغة؛ تعود الحقول الفارغة إلى المحتوى الإنجليزي أعلاه.',
    'title'                        => 'العنوان',
    'excerpt'                      => 'المقتطف',
    'meta_description'             => 'وصف الميتا',

    // ----- Visibility section -----
    'published'                    => 'منشور',
    'published_helper'             => 'الصفحات غير المنشورة تُرجِع 404 للجمهور.',
    'show_in_nav'                  => 'إظهار في التنقل الرئيسي',
    'show_in_nav_helper'           => 'يضيف رابطًا لهذه الصفحة في شريط تنقل الموقع التسويقي.',
    'show_in_footer'               => 'إظهار في التذييل',
    'show_in_footer_helper'        => 'يضيف رابطًا لهذه الصفحة في تذييل كل صفحة عامة.',
    'nav_order'                    => 'ترتيب العرض',
    'nav_order_helper'             => 'الرقم الأصغر = أبكر. يتحكم في ترتيب التنقل والتذييل.',

    // ----- Table columns -----
    'column_live'                  => 'مباشر',
    'column_nav'                   => 'تنقل',
    'column_footer'                => 'تذييل',
    'column_order'                 => 'الترتيب',

    // ----- Actions -----
    'view'                         => 'عرض',
    'view_live'                    => 'عرض مباشر',
    'new_static_page'              => 'صفحة ثابتة جديدة',

    // ----- Field labels (form + table) -----
    'slug'                         => 'المُعرّف الكنوي',
    'updated_at'                   => 'آخر تحديث',

    // ----- Model labels -----
    'model_label'                  => 'صفحة ثابتة',
    'plural_model_label'           => 'الصفحات الثابتة',

];
