<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Automation run log + action defaults
|------------------------------------------------------------
| Used by:
|   - app/Jobs/RunAutomation.php (log messages persisted to AutomationRun.log)
|   - app/Services/Automations/Actions/*.php (default action payloads)
|   - resources/views/filament/resources/automation-run-history.blade.php
|     (status badge labels)
|
| Accessed via __('automation_runs.<key>').
*/

return [

    'log' => [
        'condition_not_met'   => 'لم يتم استيفاء الشرط — توقفت الأتمتة.',
        'delaying_minutes'    => 'تأخير لمدة :minutes دقيقة',
        'email_send_failed'   => 'فشل إرسال البريد الإلكتروني: ',
        'unknown_error'       => 'خطأ غير معروف',
    ],

    'defaults' => [
        'task_title'              => 'مهمة متابعة',
        'notify_users_message'    => 'تم تشغيل الأتمتة على العميل المحتمل: :lead',
        'slack_message'           => 'تم تشغيل أتمتة LeadHub للعميل المحتمل: :lead',
    ],

    'status' => [
        'success' => 'نجاح',
        'failed'  => 'فشل',
        'running' => 'قيد التشغيل',
        'pending' => 'قيد الانتظار',
        'partial' => 'جزئي',
        // ─── Step-level result codes (RunAutomation step.result) ────
        // Stored literally in AutomationRun.log[].result; the Blade
        // view at automation-run-history.blade.php does translator-
        // first lookup against these keys when rendering step rows.
        'passed'  => 'مرّ',
        'skipped' => 'تم التخطي',
        'ok'      => 'تم',
    ],

];
