<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — IndustryPackInstaller translation strings
|--------------------------------------------------------------------------
|
| Notification copy for the Industry Packs installer page.
| Consumed via __('filament/industry_pack_installer.<key>').
|
*/

return [

    // ----- Navigation -----
    'nav_label' => 'الحزم القطاعية',

    // ----- Page title (rendered in <h1> via getTitle()) -----
    'title'     => 'الحزم القطاعية',

    'notif_no_tenant'            => 'لا يوجد سياق مساحة عمل.',
    'notif_install_failed_title' => 'تعذر تثبيت الحزمة القطاعية',
    'notif_installed_title'      => 'تم تثبيت الحزمة القطاعية',
    'notif_summary_body'         => ':pipelines خطوط أنابيب، :stages مراحل، :custom_fields حقول مخصصة، :tags وسوم، :email_templates قوالب بريد، :automations أتمتة، :forms نماذج.',

    // wire:confirm
    'confirm_install_pack'       => 'هل تريد تثبيت حزمة :name؟ سيؤدي هذا إلى إنشاء خطوط أنابيب وحقول مخصصة ووسوم وأتمتة ونماذج في مساحة عملك.',

    // ─── Page body ───
    'intro'                      => 'تجهّز الحزم القطاعية مساحة عملك بمجموعة جاهزة من خطوط الأنابيب والحقول المخصصة والوسوم وقوالب البريد والأتمتة والنماذج المُكيَّفة لقطاع محدد. تُثبَّت الأتمتة معطَّلة لتتمكن من مراجعتها قبل تشغيلها. تشغيل الحزمة مرتين آمن — تُكتشف العناصر الموجودة بالاسم/المعرّف/المفتاح وتُتخطى.',
    'stat_pipelines'             => 'خطوط الأنابيب',
    'stat_custom_fields'         => 'الحقول المخصصة',
    'stat_tags'                  => 'الوسوم',
    'stat_email_templates'       => 'قوالب البريد الإلكتروني',
    'stat_automations'           => 'الأتمتة',
    'stat_forms'                 => 'النماذج',
    'install_pack'               => 'تثبيت الحزمة',
];
