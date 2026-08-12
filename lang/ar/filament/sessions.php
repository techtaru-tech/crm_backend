<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — SessionsPage translation strings
|--------------------------------------------------------------------------
|
| Page title and header action for the active sessions page.
| Consumed via __('filament/sessions.<key>').
|
*/

return [
    'nav_label'             => 'الجلسات النشطة',
    'title'                 => 'الجلسات النشطة',
    'revoke_all_others'     => 'إنهاء جميع الجلسات الأخرى',

    // ─── Blade view ───────────────────────────────────────────────────
    'section_heading'       => 'جلساتك النشطة',
    'pill_current'          => 'الحالية',
    'meta_browser_on'       => ':browser على :platform',
    'meta_last_active'      => ':ip · آخر نشاط :ago',
    'btn_revoke'            => 'إنهاء',
    'confirm_revoke'        => 'هل أنت متأكد من أنك تريد إنهاء هذه الجلسة؟',
    'empty_no_sessions'     => 'لا توجد جلسات نشطة.',

    // ─── Browser/platform fallbacks (written to DB by UserSession::parseDeviceInfo) ───
    'browser_unknown'       => 'غير معروف',
    'platform_unknown'      => 'غير معروف',
];
