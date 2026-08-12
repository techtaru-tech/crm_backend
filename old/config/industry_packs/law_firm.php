<?php

/**
 * Law Firm industry pack.
 *
 * Intake + active-case pipelines, case-type custom fields,
 * conflict-check / retainer / statute automations, and a consult form
 * with a confidentiality notice.
 */

return [
    'key'         => 'law_firm',
    'name'        => 'Law Firm',
    'description' => 'Intake and case pipelines, conflict-check tasks, statute-of-limitations alerts, and confidential consult form.',
    'icon'        => 'heroicon-o-scale',

    'pipelines' => [
        [
            'name'        => 'Intake Pipeline',
            'description' => 'New client intake from inquiry to retainer.',
            'is_default'  => true,
            'stages' => [
                ['name' => 'Inquiry',           'color' => '#94a3b8', 'win_probability' => 5,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Conflict Check',    'color' => '#60a5fa', 'win_probability' => 15, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Consult Scheduled', 'color' => '#fbbf24', 'win_probability' => 35, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Consult Complete',  'color' => '#a78bfa', 'win_probability' => 60, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Retained',          'color' => '#10b981', 'win_probability' => 100, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Not a Fit',         'color' => '#ef4444', 'win_probability' => 0,  'is_won' => false, 'is_lost' => true],
            ],
        ],
        [
            'name'        => 'Active Case Pipeline',
            'description' => 'Retained matters from opening to closure.',
            'is_default'  => false,
            'stages' => [
                ['name' => 'Case Opened',       'color' => '#60a5fa', 'win_probability' => 20, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Discovery',         'color' => '#a78bfa', 'win_probability' => 40, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Negotiation',       'color' => '#fbbf24', 'win_probability' => 60, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Pre-Trial',         'color' => '#f59e0b', 'win_probability' => 75, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Resolved',          'color' => '#10b981', 'win_probability' => 100, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Withdrawn',         'color' => '#ef4444', 'win_probability' => 0,  'is_won' => false, 'is_lost' => true],
            ],
        ],
    ],

    'custom_fields' => [
        ['entity_type' => 'lead', 'name' => 'Case Type',                    'key' => 'case_type',        'field_type' => 'select', 'options' => ['Family', 'Criminal', 'Corporate', 'Real Estate', 'Immigration', 'Personal Injury', 'Estate', 'Employment']],
        ['entity_type' => 'lead', 'name' => 'Jurisdiction',                 'key' => 'jurisdiction',     'field_type' => 'text'],
        ['entity_type' => 'lead', 'name' => 'Statute of Limitations Date',  'key' => 'statute_date',     'field_type' => 'date'],
        ['entity_type' => 'lead', 'name' => 'Opposing Party',               'key' => 'opposing_party',   'field_type' => 'text'],
        ['entity_type' => 'lead', 'name' => 'Prior Counsel',                'key' => 'prior_counsel',    'field_type' => 'text'],
        ['entity_type' => 'lead', 'name' => 'Retainer Amount',              'key' => 'retainer_amount',  'field_type' => 'number'],
    ],

    'tags' => [
        ['name' => 'Complex',       'color' => '#8b5cf6'],
        ['name' => 'Simple',        'color' => '#3b82f6'],
        ['name' => 'Pro Bono',      'color' => '#10b981'],
        ['name' => 'High Priority', 'color' => '#ef4444'],
        ['name' => 'Referred',      'color' => '#f59e0b'],
    ],

    'email_templates' => [
        [
            'name'      => 'Consultation Confirmation',
            'subject'   => 'Your consultation is scheduled',
            'body_html' => '<p>Dear {{lead.first_name}},</p><p>Your consultation is confirmed. Please bring any relevant documents and a photo ID. Communications with our firm are confidential.</p>',
        ],
        [
            'name'      => 'Retainer Follow-up',
            'subject'   => 'Next steps on your engagement',
            'body_html' => '<p>Dear {{lead.first_name}},</p><p>Thank you for choosing our firm. Please review the attached retainer agreement and return it at your earliest convenience.</p>',
        ],
        [
            'name'      => 'Statute Alert Reminder',
            'subject'   => 'Time-sensitive: action required on your matter',
            'body_html' => '<p>Dear {{lead.first_name}},</p><p>There is a statutory deadline approaching on your matter. Please contact our office as soon as possible to discuss next steps.</p>',
        ],
    ],

    'automation_templates' => [
        ['key' => 'welcome_new_lead'],
        ['key' => 'hot_lead_alert'],
        [
            'name'           => 'Conflict check task on inquiry',
            'description'    => 'Creates a conflict-check task when a new lead is created.',
            'trigger_type'   => 'lead_created',
            'trigger_config' => [],
            'steps' => [
                [
                    'type'   => 'action',
                    'config' => [
                        'action'         => 'create_task',
                        'title'          => 'Run conflict check for {first_name} {last_name}',
                        'hours_from_now' => 2,
                        'priority'       => 'high',
                    ],
                ],
            ],
        ],
        [
            'name'           => 'Follow up on signed retainer',
            'description'    => 'Creates a task to follow up when lead is tagged Retained.',
            'trigger_type'   => 'tag_added',
            'trigger_config' => ['tag' => 'Retained'],
            'steps' => [
                [
                    'type'   => 'action',
                    'config' => [
                        'action'         => 'create_task',
                        'title'          => 'Confirm retainer payment for {first_name}',
                        'hours_from_now' => 48,
                        'priority'       => 'high',
                    ],
                ],
            ],
        ],
        [
            'name'           => 'Statute alert 30 days before',
            'description'    => 'Notifies assigned user when statute date is close (scaffold — needs date trigger config).',
            'trigger_type'   => 'no_activity',
            'trigger_config' => ['days' => 30],
            'steps' => [
                [
                    'type'   => 'action',
                    'config' => [
                        'action'  => 'notify_users',
                        'target'  => 'assigned',
                        'message' => 'Statute-of-limitations check due for {first_name} {last_name}',
                    ],
                ],
            ],
        ],
    ],

    'forms' => [
        [
            'name'              => 'Request a Consultation',
            'slug'              => 'request-consultation',
            'title'             => 'Request a confidential consultation',
            'description'       => 'All information submitted is confidential and protected by attorney-client privilege once representation is established.',
            'submit_label'      => 'Request Consultation',
            'thank_you_message' => 'Thanks. Our intake team will contact you confidentially within one business day.',
            'fields' => [
                ['type' => 'text',     'label' => 'Full Name',     'field_key' => 'name',     'required' => true,  'sort_order' => 0],
                ['type' => 'email',    'label' => 'Email',         'field_key' => 'email',    'required' => true,  'sort_order' => 1],
                ['type' => 'phone',    'label' => 'Phone',         'field_key' => 'phone',    'required' => true,  'sort_order' => 2],
                ['type' => 'select',   'label' => 'Case Type',     'field_key' => 'case_type', 'required' => true, 'sort_order' => 3, 'options' => ['Family', 'Criminal', 'Corporate', 'Real Estate', 'Immigration', 'Personal Injury', 'Other']],
                ['type' => 'textarea', 'label' => 'Brief Description', 'field_key' => 'description', 'required' => false, 'sort_order' => 4, 'placeholder' => 'Please do not include sensitive details until an engagement is established.'],
                ['type' => 'gdpr',     'label' => 'I understand that submitting this form does not create an attorney-client relationship and that privilege attaches only upon a signed engagement.', 'field_key' => 'consent', 'required' => true, 'sort_order' => 5],
            ],
        ],
    ],
];
