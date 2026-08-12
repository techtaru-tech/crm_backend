<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| OutboundWebhook model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/outbound_webhook.<key>').
*/

return [
    // ─── EVENTS ────────────────────────────────────
    'event_lead_created'         => 'Lead Created',
    'event_lead_updated'         => 'Lead Updated',
    'event_lead_deleted'         => 'Lead Deleted',
    'event_lead_stage_changed'   => 'Lead Stage Changed',
    'event_form_submitted'       => 'Form Submitted',
    'event_automation_triggered' => 'Automation Triggered',
];
