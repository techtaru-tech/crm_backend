<?php

return [
    [
        'key'            => 'welcome_new_lead',
        'name'           => 'Welcome New Lead',
        'description'    => 'Send a welcome email when any new lead is created',
        'trigger_type'   => 'lead_created',
        'trigger_config' => [],
        'steps' => [
            [
                'type'   => 'action',
                'config' => [
                    'action'  => 'send_email',
                    'subject' => 'Welcome, {first_name}!',
                    'body'    => '<p>Hi {first_name},</p><p>Thanks for your interest — we will be in touch shortly.</p>',
                ],
            ],
        ],
    ],
    [
        'key'            => 'hot_lead_alert',
        'name'           => 'Hot Lead Alert',
        'description'    => 'Notify team when a lead score crosses 80',
        'trigger_type'   => 'lead_score_threshold',
        'trigger_config' => ['threshold' => 80, 'direction' => 'above'],
        'steps' => [
            [
                'type'   => 'action',
                'config' => [
                    'action'  => 'notify_users',
                    'target'  => 'all_admins',
                    'message' => 'Hot lead alert: {first_name} {last_name} reached score {lead_score}',
                ],
            ],
        ],
    ],
    [
        'key'            => 'assign_by_source',
        'name'           => 'Round-Robin Assignment',
        'description'    => 'Distribute new leads evenly across your sales reps',
        'trigger_type'   => 'lead_created',
        'trigger_config' => [],
        'steps' => [
            [
                'type'   => 'action',
                'config' => [
                    'action' => 'assign_lead',
                    'mode'   => 'round_robin',
                ],
            ],
        ],
    ],
    [
        'key'            => 'followup_no_activity',
        'name'           => 'Follow-up Reminder',
        'description'    => 'Create a follow-up task when a lead has no activity for 3 days',
        'trigger_type'   => 'no_activity',
        'trigger_config' => ['days' => 3],
        'steps' => [
            [
                'type'   => 'action',
                'config' => [
                    'action'         => 'create_task',
                    'title'          => 'Follow up with {first_name}',
                    'hours_from_now' => 1,
                    'priority'       => 'high',
                ],
            ],
        ],
    ],
    [
        'key'            => 'stage_change_notification',
        'name'           => 'Stage Change Notification',
        'description'    => 'Notify the assigned user when a lead moves to Proposal stage',
        'trigger_type'   => 'lead_stage_changed',
        'trigger_config' => [],
        'steps' => [
            [
                'type'   => 'condition',
                'config' => [
                    'type'  => 'field_contains',
                    'field' => 'pipeline_stage.name',
                    'value' => 'Proposal',
                ],
            ],
            [
                'type'   => 'action',
                'config' => [
                    'action'  => 'notify_users',
                    'target'  => 'assigned',
                    'message' => 'Lead {first_name} {last_name} moved to Proposal',
                ],
            ],
        ],
    ],
    [
        'key'            => 'reengage_cold',
        'name'           => 'Re-engage Cold Leads',
        'description'    => 'Email leads inactive for 30 days with a re-engagement message',
        'trigger_type'   => 'no_activity',
        'trigger_config' => ['days' => 30],
        'steps' => [
            [
                'type'   => 'action',
                'config' => [
                    'action'  => 'send_email',
                    'subject' => 'Still interested, {first_name}?',
                    'body'    => '<p>Hi {first_name}, we noticed you have not heard from us in a while. Anything we can help with?</p>',
                ],
            ],
        ],
    ],
    [
        'key'            => 'vip_tagger',
        'name'           => 'Auto-Tag VIP Leads',
        'description'    => 'Automatically add a VIP tag when deal value exceeds $10k',
        'trigger_type'   => 'lead_created',
        'trigger_config' => [],
        'steps' => [
            [
                'type'   => 'condition',
                'config' => [
                    'type'  => 'score_gt',
                    'value' => 50,
                ],
            ],
            [
                'type'   => 'action',
                'config' => [
                    'action' => 'add_tag',
                    'tag'    => 'VIP',
                ],
            ],
        ],
    ],
    [
        'key'            => 'slack_new_lead',
        'name'           => 'Slack Alert on New Lead',
        'description'    => 'Post to Slack when a new lead is received',
        'trigger_type'   => 'lead_created',
        'trigger_config' => [],
        'steps' => [
            [
                'type'   => 'action',
                'config' => [
                    'action'  => 'send_slack',
                    'channel' => '#sales',
                    'message' => 'New lead: {first_name} {last_name} from {source}',
                ],
            ],
        ],
    ],
    [
        'key'            => 'sla_escalation_hot_lead',
        'name'           => 'SLA: Escalate Hot Leads Fast',
        'description'    => 'If a lead with score > 80 has no contact in 2 hours, notify the manager',
        'trigger_type'   => 'no_activity',
        'trigger_config' => ['hours' => 2],
        'steps' => [
            ['type' => 'condition', 'config' => ['type' => 'score_gt', 'value' => 80]],
            ['type' => 'action',    'config' => ['action' => 'notify_users', 'target' => 'all_admins', 'message' => 'HOT LEAD untouched for 2h: {first_name} {last_name} (score {lead_score})']],
        ],
    ],
    [
        'key'            => 'lead_recycling',
        'name'           => 'Lead Recycling',
        'description'    => 'Reassign unworked leads to another rep after 30 days',
        'trigger_type'   => 'no_activity',
        'trigger_config' => ['days' => 30],
        'steps' => [
            ['type' => 'action', 'config' => ['action' => 'assign_lead', 'mode' => 'round_robin']],
            ['type' => 'action', 'config' => ['action' => 'add_tag', 'tag' => 'recycled']],
            ['type' => 'action', 'config' => ['action' => 'notify_users', 'target' => 'assigned', 'message' => 'A recycled lead was reassigned to you: {first_name} {last_name}']],
        ],
    ],
];
