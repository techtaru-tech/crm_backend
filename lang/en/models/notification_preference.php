<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| NotificationPreference model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/notification_preference.<key>').
*/

return [
    // ─── TYPES ─────────────────────────────────────
    'type_lead_received'           => 'New Lead Received',
    'type_lead_assigned'           => 'Lead Assigned to Me',
    'type_lead_stage_changed'      => 'Lead Moved to Stage',
    'type_automation_ran'          => 'Automation Triggered',
    'type_integration_sync_failed' => 'Integration Sync Failed',
    'type_export_ready'            => 'Export Ready',
    'type_team_mentioned'          => 'Team Mention in Note',
];
