<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Notification type labels (translator-first, English fallback)
|--------------------------------------------------------------------------
|
| Maps every `notification_type` value emitted by classes in
| app/Notifications/* (and persisted to notification_digests /
| notification_preferences) to a human-readable label that the
| hourly digest email and any other UI surface can render.
|
| Looked up via `__('notification_types.<type>')`.  If a key is
| missing for a given type the caller falls back to ucwords() of the
| underscored type string, so adding new notification types stays
| backward-compatible.
|
| Keys mirror NotificationPreference::TYPES plus task_reminder which
| is dispatched by LeadTaskReminderNotification, and export_failed
| from ExportFailedNotification.
|
*/

return [
    'lead_received'           => 'New Lead Received',
    'lead_assigned'           => 'Lead Assigned to Me',
    'lead_stage_changed'      => 'Lead Moved to Stage',
    'automation_ran'          => 'Automation Triggered',
    'integration_sync_failed' => 'Integration Sync Failed',
    'export_ready'            => 'Export Ready',
    'export_failed'           => 'Export Failed',
    'team_mentioned'          => 'Team Mention in Note',
    'task_reminder'           => 'Task Reminder',
];
