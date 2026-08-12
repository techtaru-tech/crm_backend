<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — AutomationTemplateBrowser translation strings
|--------------------------------------------------------------------------
|
| Notification copy for the automation-templates browser page.
| Consumed via __('filament/automation_template_browser.<key>').
|
*/

return [

    // ----- Page title (rendered in <h1> via getTitle()) -----
    'title'                        => 'قوالب الأتمتة',

    'notif_no_tenant'              => 'لا يوجد سياق مساحة عمل.',
    'notif_install_failed_title'   => 'تعذر تثبيت القالب',
    'notif_installed_title'        => 'تم تثبيت القالب',
    'notif_installed_body'         => 'راجع الأتمتة وفعّلها لبدء استخدامها.',

    // ─── Page body ───
    'intro'                        => 'اختر أتمتة جاهزة لتثبيتها في مساحة عملك. تُحفظ القوالب المثبَّتة كأتمتة معطَّلة لتتمكن من مراجعة الخطوات وتفعيلها عندما تكون جاهزًا.',
    'use_template'                 => 'استخدام القالب',

    // ─── Step counter (pluralized) ───
    'step_count'                   => '{1} :count خطوة|[2,*] :count خطوات',
];
