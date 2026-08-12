<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Automation Templates — display labels
|--------------------------------------------------------------------------
| Mirrors the `name` and `description` fields of config/automation_templates.php.
| Consumed by AutomationTemplateService::templates() and the
| IndustryPackService::installAutomations() lookup, both of which apply
| translator-first-with-fallback to the original config value.
|
| Keys MUST match the `key` field of each entry in
| config/automation_templates.php so unknown / buyer-added templates fall
| through to the literal config value.
*/

return [

    'welcome_new_lead' => [
        'name'        => 'Welcome New Lead',
        'description' => 'Send a welcome email when any new lead is created',
    ],

    'hot_lead_alert' => [
        'name'        => 'Hot Lead Alert',
        'description' => 'Notify team when a lead score crosses 80',
    ],

    'assign_by_source' => [
        'name'        => 'Round-Robin Assignment',
        'description' => 'Distribute new leads evenly across your sales reps',
    ],

    'followup_no_activity' => [
        'name'        => 'Follow-up Reminder',
        'description' => 'Create a follow-up task when a lead has no activity for 3 days',
    ],

    'stage_change_notification' => [
        'name'        => 'Stage Change Notification',
        'description' => 'Notify the assigned user when a lead moves to Proposal stage',
    ],

    'reengage_cold' => [
        'name'        => 'Re-engage Cold Leads',
        'description' => 'Email leads inactive for 30 days with a re-engagement message',
    ],

    'vip_tagger' => [
        'name'        => 'Auto-Tag VIP Leads',
        'description' => 'Automatically add a VIP tag when deal value exceeds $10k',
    ],

    'slack_new_lead' => [
        'name'        => 'Slack Alert on New Lead',
        'description' => 'Post to Slack when a new lead is received',
    ],

    'sla_escalation_hot_lead' => [
        'name'        => 'SLA: Escalate Hot Leads Fast',
        'description' => 'If a lead with score > 80 has no contact in 2 hours, notify the manager',
    ],

    'lead_recycling' => [
        'name'        => 'Lead Recycling',
        'description' => 'Reassign unworked leads to another rep after 30 days',
    ],
];
