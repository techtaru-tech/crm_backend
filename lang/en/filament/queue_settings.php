<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| QueueSettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/queue_settings.<key>').
*/

return [
    'title'                => 'Queue & Worker Status',
    'navigation_label'     => 'Queue & Workers',

    // Section headings
    'queue_configuration'  => 'Queue Configuration',

    // ─── Blade view — queue config grid ────────────────────────────────
    'connection'           => 'Connection',
    'driver'               => 'Driver',

    // ─── Blade view — Horizon ─────────────────────────────────────────
    'horizon_lede'         => 'Laravel Horizon is installed. Monitor queues in real time:',
    'horizon_open_dashboard' => 'Open Horizon Dashboard',

    // ─── Blade view — operator notice banner ──────────────────────────
    'operator_notice_title' => 'Background jobs are managed by the service operator',
    'operator_notice_body' => "Automations, email delivery, scheduled reports, and reminders are processed by a background worker that's configured at the server level. If jobs seem delayed or stuck, contact your service provider — they manage the cron schedule and worker infrastructure on your behalf.",
];
