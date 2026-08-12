<?php

/**
 * Real Estate industry pack.
 *
 * Installs buyer + seller pipelines, property-interest custom fields,
 * industry-specific tags / email templates / automations / forms
 * into the current tenant.
 */

return [
    'key'         => 'real_estate',
    'name'        => 'Real Estate',
    'description' => 'Buyer & seller pipelines, property interest fields, viewing automations, and lead-capture forms tuned for realtors.',
    'icon'        => 'heroicon-o-home',

    'pipelines' => [
        [
            'name'        => 'Buyer Pipeline',
            'description' => 'Track buyers from first inquiry through close.',
            'is_default'  => true,
            'stages' => [
                ['name' => 'New Lead',              'color' => '#94a3b8', 'win_probability' => 5,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Needs Qualification',   'color' => '#60a5fa', 'win_probability' => 15, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Viewing Scheduled',     'color' => '#fbbf24', 'win_probability' => 40, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Offer Made',            'color' => '#f59e0b', 'win_probability' => 70, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Under Contract',        'color' => '#22c55e', 'win_probability' => 90, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Closed',                'color' => '#10b981', 'win_probability' => 100, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Lost',                  'color' => '#ef4444', 'win_probability' => 0,  'is_won' => false, 'is_lost' => true],
            ],
        ],
        [
            'name'        => 'Seller Pipeline',
            'description' => 'Track listings from CMA to closing.',
            'is_default'  => false,
            'stages' => [
                ['name' => 'Inquiry',               'color' => '#94a3b8', 'win_probability' => 5,  'is_won' => false, 'is_lost' => false],
                ['name' => 'CMA Prepared',          'color' => '#60a5fa', 'win_probability' => 20, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Listing Agreement',     'color' => '#fbbf24', 'win_probability' => 50, 'is_won' => false, 'is_lost' => false],
                ['name' => 'On Market',             'color' => '#a78bfa', 'win_probability' => 60, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Offer Received',        'color' => '#f59e0b', 'win_probability' => 80, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Sold',                  'color' => '#10b981', 'win_probability' => 100, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Withdrawn',             'color' => '#ef4444', 'win_probability' => 0,  'is_won' => false, 'is_lost' => true],
            ],
        ],
    ],

    'custom_fields' => [
        ['entity_type' => 'lead', 'name' => 'Budget Range',     'key' => 'budget_range',     'field_type' => 'select',       'options' => ['Under $250k', '$250k-$500k', '$500k-$1M', '$1M-$2M', '$2M+']],
        ['entity_type' => 'lead', 'name' => 'Property Type',    'key' => 'property_type',    'field_type' => 'multi_select', 'options' => ['Single Family', 'Condo', 'Townhouse', 'Multi-Family', 'Land']],
        ['entity_type' => 'lead', 'name' => 'Bedrooms',         'key' => 'bedrooms',         'field_type' => 'number'],
        ['entity_type' => 'lead', 'name' => 'Pre-Approved',     'key' => 'pre_approved',     'field_type' => 'checkbox'],
        ['entity_type' => 'lead', 'name' => 'Timeline',         'key' => 'timeline',         'field_type' => 'select',       'options' => ['ASAP', '1-3 months', '3-6 months', '6-12 months', 'Just browsing']],
        ['entity_type' => 'lead', 'name' => 'Preferred Areas',  'key' => 'preferred_areas',  'field_type' => 'textarea'],
    ],

    'tags' => [
        ['name' => 'First-Time Buyer', 'color' => '#3b82f6'],
        ['name' => 'Investor',         'color' => '#8b5cf6'],
        ['name' => 'Cash Buyer',       'color' => '#10b981'],
        ['name' => 'Relocation',       'color' => '#f59e0b'],
        ['name' => 'Luxury',           'color' => '#d4af37'],
    ],

    'email_templates' => [
        [
            'name'      => 'Viewing Confirmation',
            'subject'   => 'Your viewing is scheduled, {{lead.first_name}}',
            'body_html' => '<p>Hi {{lead.first_name}},</p><p>Your showing is confirmed. Please arrive 5 minutes early and bring ID. Reply to this email if anything changes.</p><p>— Your agent</p>',
        ],
        [
            'name'      => 'Follow-up After Showing',
            'subject'   => 'Thoughts on the property, {{lead.first_name}}?',
            'body_html' => '<p>Hi {{lead.first_name}},</p><p>Thanks for viewing today. Any initial thoughts? Happy to answer questions or book a second showing.</p>',
        ],
        [
            'name'      => 'New Listing Alert',
            'subject'   => 'A new listing matches your search, {{lead.first_name}}',
            'body_html' => '<p>Hi {{lead.first_name}},</p><p>A new property just hit the market that fits your criteria. Let me know if you\'d like to tour it this week.</p>',
        ],
    ],

    'automation_templates' => [
        // Reuse generic templates from config/automation_templates.php by key:
        ['key' => 'welcome_new_lead'],
        ['key' => 'hot_lead_alert'],
        ['key' => 'assign_by_source'],
        // Industry-specific inline definitions:
        [
            'name'           => 'Auto-schedule viewing follow-up',
            'description'    => 'Creates a 72h follow-up task when a lead is tagged Viewing Scheduled.',
            'trigger_type'   => 'tag_added',
            'trigger_config' => ['tag' => 'Viewing Scheduled'],
            'steps' => [
                [
                    'type'   => 'action',
                    'config' => [
                        'action'         => 'create_task',
                        'title'          => 'Follow up with {first_name} after the viewing',
                        'hours_from_now' => 72,
                        'priority'       => 'high',
                    ],
                ],
            ],
        ],
        [
            'name'           => 'Send new-listing alert on interest',
            'description'    => 'Emails a property alert when lead enters Needs Qualification.',
            'trigger_type'   => 'lead_stage_changed',
            'trigger_config' => [],
            'steps' => [
                [
                    'type'   => 'condition',
                    'config' => ['type' => 'field_contains', 'field' => 'pipeline_stage.name', 'value' => 'Needs Qualification'],
                ],
                [
                    'type'   => 'action',
                    'config' => [
                        'action'  => 'send_email',
                        'subject' => 'Properties you might like, {first_name}',
                        'body'    => '<p>Hi {first_name}, here are fresh listings in your target area.</p>',
                    ],
                ],
            ],
        ],
    ],

    'forms' => [
        [
            'name'              => 'Property Interest Form',
            'slug'              => 'property-interest',
            'title'             => 'Tell us about your dream home',
            'description'       => 'A short form to match you with the right properties.',
            'submit_label'      => 'Find My Home',
            'thank_you_message' => 'Thanks! An agent will reach out within one business day.',
            'fields' => [
                ['type' => 'text',     'label' => 'Full Name',        'field_key' => 'name',         'required' => true,  'sort_order' => 0],
                ['type' => 'email',    'label' => 'Email',            'field_key' => 'email',        'required' => true,  'sort_order' => 1],
                ['type' => 'phone',    'label' => 'Phone',            'field_key' => 'phone',        'required' => false, 'sort_order' => 2],
                ['type' => 'select',   'label' => 'Budget',           'field_key' => 'budget',       'required' => false, 'sort_order' => 3, 'options' => ['Under $500k', '$500k-$1M', '$1M+']],
                ['type' => 'checkbox', 'label' => 'Pre-approved?',    'field_key' => 'pre_approved', 'required' => false, 'sort_order' => 4],
                ['type' => 'textarea', 'label' => 'Preferred Areas',  'field_key' => 'areas',        'required' => false, 'sort_order' => 5],
                ['type' => 'gdpr',     'label' => 'I consent to being contacted about properties.', 'field_key' => 'consent', 'required' => true, 'sort_order' => 6],
            ],
        ],
    ],
];
