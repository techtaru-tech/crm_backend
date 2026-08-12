<?php

/**
 * Digital Marketing Agency industry pack.
 *
 * The strategic positioning play — replicates the GoHighLevel pattern
 * for agencies running on LeadHub.  Installs:
 *   - Discovery → Pitch → Won/Lost pipeline (default)
 *   - Long-term Account-Management pipeline (retainer clients)
 *   - 7 agency-canonical custom fields (retainer value, services,
 *     decision date, decision maker, source, current agency, budget)
 *   - Tags for common agency situations
 *   - 5 ready-to-send email templates (intro, recap, proposal, win-back,
 *     onboarding-welcome)
 *   - 2 automations (auto-assign + 7-day re-engagement)
 *   - 1 lead-magnet form template (free marketing audit)
 */

return [
    'key'         => 'agency',
    'name'        => 'Digital Marketing Agency',
    'description' => 'Sales pipeline, retainer tracking, and proposal-to-close automations tuned for digital marketing & creative agencies.',
    'icon'        => 'heroicon-o-megaphone',

    'pipelines' => [
        [
            'name'        => 'New Business Pipeline',
            'description' => 'Discovery → Pitched → Closed. Default pipeline for inbound and outbound prospecting.',
            'is_default'  => true,
            'stages' => [
                ['name' => 'New Lead',         'color' => '#94a3b8', 'win_probability' => 5,   'is_won' => false, 'is_lost' => false],
                ['name' => 'Discovery Call',   'color' => '#60a5fa', 'win_probability' => 20,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Audit / Proposal', 'color' => '#fbbf24', 'win_probability' => 40,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Pitched',          'color' => '#a78bfa', 'win_probability' => 60,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Negotiation',      'color' => '#f59e0b', 'win_probability' => 80,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Won (Signed)',     'color' => '#10b981', 'win_probability' => 100, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Lost',             'color' => '#ef4444', 'win_probability' => 0,   'is_won' => false, 'is_lost' => true],
                ['name' => 'Nurture',          'color' => '#6b7280', 'win_probability' => 10,  'is_won' => false, 'is_lost' => false],
            ],
        ],
        [
            'name'        => 'Account Management',
            'description' => 'Active retainer clients tracked through health stages. Move down when at-risk, up when expanding.',
            'is_default'  => false,
            'stages' => [
                ['name' => 'Onboarding',       'color' => '#3b82f6', 'win_probability' => 100, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Healthy',          'color' => '#10b981', 'win_probability' => 100, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Expansion',        'color' => '#22c55e', 'win_probability' => 100, 'is_won' => false, 'is_lost' => false],
                ['name' => 'At Risk',          'color' => '#f59e0b', 'win_probability' => 100, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Churned',          'color' => '#ef4444', 'win_probability' => 0,   'is_won' => false, 'is_lost' => true],
            ],
        ],
    ],

    'custom_fields' => [
        ['entity_type' => 'lead', 'name' => 'Lead Source',           'key' => 'agency_lead_source',     'field_type' => 'select',       'options' => ['Inbound — Form', 'Inbound — Referral', 'Cold Outbound', 'Paid Ads', 'Event / Conference', 'Other']],
        ['entity_type' => 'lead', 'name' => 'Monthly Retainer',      'key' => 'monthly_retainer',       'field_type' => 'number'],
        ['entity_type' => 'lead', 'name' => 'Services Interested In','key' => 'services_interested',    'field_type' => 'multi_select', 'options' => ['SEO', 'Paid Ads (PPC)', 'Social Media', 'Content / Blog', 'Web Design', 'Branding', 'Email Marketing', 'Analytics']],
        ['entity_type' => 'lead', 'name' => 'Decision Date',         'key' => 'decision_date',          'field_type' => 'date'],
        ['entity_type' => 'lead', 'name' => 'Decision Maker?',       'key' => 'is_decision_maker',      'field_type' => 'checkbox'],
        ['entity_type' => 'lead', 'name' => 'Current Agency',        'key' => 'current_agency',         'field_type' => 'text'],
        ['entity_type' => 'lead', 'name' => 'Annual Marketing Budget','key' => 'annual_marketing_budget','field_type' => 'select',       'options' => ['<$10k', '$10k-$50k', '$50k-$100k', '$100k-$500k', '$500k+']],
    ],

    'tags' => [
        ['name' => 'Hot',              'color' => '#dc2626'],
        ['name' => 'Warm',             'color' => '#f59e0b'],
        ['name' => 'Cold',             'color' => '#3b82f6'],
        ['name' => 'Referral',         'color' => '#10b981'],
        ['name' => 'Enterprise',       'color' => '#7c3aed'],
        ['name' => 'SMB',              'color' => '#6b7280'],
        ['name' => 'High Touch',       'color' => '#ec4899'],
        ['name' => 'Past Client',      'color' => '#0891b2'],
    ],

    'email_templates' => [
        [
            'name'      => 'Discovery Call Intro',
            'subject'   => 'Quick intro before our call, {{lead.first_name}}',
            'body_html' => "<p>Hi {{lead.first_name}},</p>"
                         . "<p>Looking forward to our chat. To make the call as useful as possible for you, I'll prepare around three things:</p>"
                         . "<ol><li>Where you are now (current marketing channels, what's working, what's not)</li>"
                         . "<li>Where you want to be in 6-12 months (the goal, not the tactics)</li>"
                         . "<li>What's blocking that today</li></ol>"
                         . "<p>If you can spend 30 seconds replying with a quick note on any of those, I'll have a sharper plan ready.</p>"
                         . "<p>Talk soon,<br>— {{user.first_name}}</p>",
        ],
        [
            'name'      => 'Post-Discovery Recap',
            'subject'   => 'Recap + next steps from our call',
            'body_html' => "<p>Hi {{lead.first_name}},</p>"
                         . "<p>Great call today. Quick recap of what we covered:</p>"
                         . "<ul><li><strong>Goal:</strong> [fill in]</li>"
                         . "<li><strong>Current state:</strong> [fill in]</li>"
                         . "<li><strong>Biggest blocker:</strong> [fill in]</li></ul>"
                         . "<p>Next: I'll put together a proposal addressing the blocker first.  You'll have it within 48 hours.</p>"
                         . "<p>If anything I've captured above is off, just reply with a correction.</p>"
                         . "<p>— {{user.first_name}}</p>",
        ],
        [
            'name'      => 'Proposal Sent',
            'subject'   => 'Proposal attached — {{lead.first_name}}',
            'body_html' => "<p>Hi {{lead.first_name}},</p>"
                         . "<p>The proposal is ready. Three things to flag:</p>"
                         . "<ol><li>Pricing is at the bottom — feel free to skip there if you're short on time</li>"
                         . "<li>I've included a 30-day pilot option if you'd rather start small</li>"
                         . "<li>I'll follow up Wednesday if I haven't heard back — happy to get on a quick call to walk through it</li></ol>"
                         . "<p>— {{user.first_name}}</p>",
        ],
        [
            'name'      => 'Win-Back After 30 Days Cold',
            'subject'   => 'Quick check-in, {{lead.first_name}}',
            'body_html' => "<p>Hi {{lead.first_name}},</p>"
                         . "<p>Haven't heard back so I wanted to give you an out — three options:</p>"
                         . "<ol><li>Timing's off, ping me in [X] months</li>"
                         . "<li>Going a different direction, no hard feelings</li>"
                         . "<li>Still interested, just buried — let's reschedule</li></ol>"
                         . "<p>One-line reply works.</p>"
                         . "<p>— {{user.first_name}}</p>",
        ],
        [
            'name'      => 'Onboarding Welcome (Won Deal)',
            'subject'   => 'Welcome to the team, {{lead.first_name}}',
            'body_html' => "<p>Hi {{lead.first_name}},</p>"
                         . "<p>Excited to have you on board. Here's what happens in the first 14 days:</p>"
                         . "<ul><li><strong>This week:</strong> Kickoff call to align on goals + timelines</li>"
                         . "<li><strong>Week 2:</strong> First strategy doc + access setup</li>"
                         . "<li><strong>Week 3+:</strong> Execution starts, weekly check-ins on Mondays</li></ul>"
                         . "<p>I'll send the kickoff invite within 24 hours.</p>"
                         . "<p>— {{user.first_name}}</p>",
        ],
    ],

    'automations' => [
        [
            'name'        => 'Auto-assign new lead',
            'description' => 'Round-robin assignment of fresh inbound leads to active sales reps + Slack notification.',
            'trigger'     => 'lead.created',
            'is_active'   => true,
            'steps' => [
                ['type' => 'assign_lead',     'config' => ['strategy' => 'round_robin']],
                ['type' => 'send_slack',      'config' => ['message' => 'New lead: {{lead.first_name}} {{lead.last_name}} from {{lead.agency_lead_source}}']],
                ['type' => 'create_task',     'config' => ['title' => 'Reach out to {{lead.first_name}}', 'due_in_hours' => 4]],
            ],
        ],
        [
            'name'        => 'Re-engage 7-day silent leads',
            'description' => 'When a lead has had no activity for 7 days, fire a soft check-in email + create owner task.',
            'trigger'     => 'lead.no_activity_7_days',
            'is_active'   => true,
            'steps' => [
                ['type' => 'send_email',  'config' => ['template_name' => 'Win-Back After 30 Days Cold']],
                ['type' => 'tag',         'config' => ['add_tags' => ['Cold']]],
                ['type' => 'create_task', 'config' => ['title' => 'Manually re-engage {{lead.first_name}}', 'due_in_hours' => 24]],
            ],
        ],
    ],

    'forms' => [
        [
            'name'        => 'Free Marketing Audit Request',
            'description' => 'Lead-magnet form — collects basic info + which channel they need help with most.',
            'fields' => [
                ['name' => 'first_name',       'label' => 'First name',                            'type' => 'text',     'required' => true],
                ['name' => 'last_name',        'label' => 'Last name',                             'type' => 'text',     'required' => true],
                ['name' => 'email',            'label' => 'Work email',                            'type' => 'email',    'required' => true],
                ['name' => 'company',          'label' => 'Company',                               'type' => 'text',     'required' => true],
                ['name' => 'website',          'label' => 'Company website',                       'type' => 'url',      'required' => false],
                ['name' => 'budget',           'label' => 'Annual marketing budget',               'type' => 'select',   'required' => true,
                    'options' => ['<$10k', '$10k-$50k', '$50k-$100k', '$100k-$500k', '$500k+']],
                ['name' => 'biggest_challenge','label' => 'Biggest marketing challenge right now', 'type' => 'textarea', 'required' => false],
            ],
            'success_message' => "Thanks! We'll review your site and send you a free 15-minute video audit within 48 hours.",
        ],
    ],
];
