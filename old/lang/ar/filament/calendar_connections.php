<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| CalendarConnectionsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/calendar_connections.<key>').
*/

return [
    'title'                  => 'مزامنة التقويم',
    'heading'                => 'مزامنة التقويم',
    'subheading'             => 'اربط تقويم Google أو Outlook حتى تظهر الاجتماعات المحجوزة في CRM هناك تلقائيًا — وتتدفق الأحداث الخارجية إلى جدول الحجز.',
    'navigation_label'       => 'التقويم',

    // Provider labels
    'provider_google'        => 'Google Calendar',
    'provider_outlook'       => 'Outlook Calendar',

    // ─── Connection cards (resources/views/filament/pages/settings/calendar-connections.blade.php) ──
    'reconnect'              => 'إعادة الاتصال',
    'disconnect'             => 'فصل',
    'confirm_disconnect'     => 'هل تريد فصل :provider؟ تبقى الأحداث المتزامنة سابقًا في CRM، وستتوقف الأحداث الجديدة عن الظهور.',
    'not_connected_note'     => 'غير متصل. لن تظهر الحجوزات المُنشأة هنا على :provider حتى تربط حسابك.',

    // ─── Status pill + footer ──────────────────────────────────────
    'last_synced_prefix'     => 'آخر مزامنة',
    'last_synced'            => 'آخر مزامنة :when',
    'connect_prefix'         => 'اتصال',
    'coming_soon'            => 'قريبًا',
    'footnote'               => 'تعمل المزامنة كل 15 دقيقة. يحدث الدفع الصادر فورًا عند إنشاء حجز. لا نقرأ تقويمك خارج نطاق events.*.',
];
