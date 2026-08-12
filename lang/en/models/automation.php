<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Automation model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/automation.<key>').
*/

return [
    // ─── TRIGGERS ──────────────────────────────────
    'trigger_lead_created'         => 'New Lead Received',
    'trigger_lead_stage_changed'   => 'Lead Stage Changed',
    'trigger_lead_assigned'        => 'Lead Assigned to User',
    'trigger_tag_added'            => 'Tag Added to Lead',
    'trigger_lead_score_threshold' => 'Lead Score Crosses Threshold',
    'trigger_no_activity'          => 'No Activity (Time-based)',
    'trigger_form_submitted'       => 'Form Submitted',
    'trigger_manual'               => 'Manual Trigger',
];
