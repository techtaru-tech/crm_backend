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
        'condition_not_met'   => 'Condition not met — automation stopped.',
        'delaying_minutes'    => 'Delaying :minutes minutes',
        'email_send_failed'   => 'Email send failed: ',
        'unknown_error'       => 'Unknown error',
    ],

    'defaults' => [
        'task_title'              => 'Follow-up task',
        'notify_users_message'    => 'Automation triggered on lead: :lead',
        'slack_message'           => 'LeadHub automation triggered for lead: :lead',
    ],

    'status' => [
        'success' => 'Success',
        'failed'  => 'Failed',
        'running' => 'Running',
        'pending' => 'Pending',
        'partial' => 'Partial',
        // ─── Step-level result codes (RunAutomation step.result) ────
        // Stored literally in AutomationRun.log[].result; the Blade
        // view at automation-run-history.blade.php does translator-
        // first lookup against these keys when rendering step rows.
        'passed'  => 'Passed',
        'skipped' => 'Skipped',
        'ok'      => 'OK',
    ],

];
