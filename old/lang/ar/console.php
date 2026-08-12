<?php

declare(strict_types=1);

/*
|------------------------------------------------------------------------------
| Console command output strings (UI-surfacing exception)
|------------------------------------------------------------------------------
|
| Convention: console command output (->info / ->warn / ->error / ->line) is
| normally kept literal English. It is consumed by sysadmins via STDOUT/STDERR,
| log aggregators, and incident-response tooling — translating it would break
| log parsers and support scripts.
|
| EXCEPTION: a command is considered UI-surfacing — and its output strings
| MUST be translated — when one of the following is true:
|
|   1. The output is captured via Artisan::output() and rendered in a Filament
|      Notification body (i.e. shown to a logged-in admin in the panel).
|   2. The output is persisted to a table that a Filament resource reads back
|      (audit_logs surfaced in UI, notifications table, etc.).
|   3. The output is returned in an HTTP response.
|
| As of the current audit, only the `leadhub:check-updates` command meets
| criterion (1) — its output is rendered in the Super Admin Updates page via
| app/Filament/SuperAdmin/Pages/Updates.php::checkForUpdates(). Hence the keys
| below. Every other command under app/Console/Commands/* is cron/CLI only and
| stays literal.
|
| If a future command starts surfacing through any of the above channels, add
| its translatable strings here under a new namespaced section.
|
*/

return [
    'check_updates' => [
        'no_server_configured' => 'لم يتم إعداد رابط خادم التحديثات (LEADHUB_UPDATE_SERVER).',
        'server_error'         => 'أعاد خادم التحديثات خطأً: :status',
        'update_available'     => 'تحديث متاح: :current ← :latest',
        'up_to_date'           => 'محدّث (الإصدار :current).',
        'check_failed'         => 'فشل التحقق من التحديثات: :message',
    ],
];
