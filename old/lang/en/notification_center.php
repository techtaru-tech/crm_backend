<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Notification Center (bell dropdown) message templates
|--------------------------------------------------------------------------
|
| One-line summaries rendered in the top-bar notification center for each
| supported notification `type`. Resolved live by
| App\Livewire\NotificationCenter::buildMessage() — these strings are
| NOT stored in the database, so simply switching the user's locale
| translates every row on the next render.
|
| Placeholders use Laravel's :placeholder convention; pass values via
| __('notification_center.key', ['placeholder' => $value]).
|
| Keys match the `data['type']` values emitted by the notification
| classes in app/Notifications/*.
|
*/

return [
    'lead_received'           => 'New lead: :lead_name',
    'lead_assigned'           => 'Lead assigned to you: :lead_name',
    'lead_stage_changed'      => 'Lead moved to :to_stage: :lead_name',
    'automation_ran'          => 'Automation ":automation_name" ran on :lead_name',
    'integration_sync_failed' => 'Sync failed for :integration_name on :lead_name',
    'export_ready'            => 'Your export is ready for download',
    'team_mentioned'          => ':mentioned_by mentioned you in a note',
    'default'                 => 'Notification',
];
