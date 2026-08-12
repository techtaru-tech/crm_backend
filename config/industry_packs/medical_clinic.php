<?php

/**
 * Medical Clinic industry pack.
 *
 * Patient intake pipeline, appointment-focused custom fields,
 * HIPAA-style consent language, and consultation booking form.
 */

return [
    'key'         => 'medical_clinic',
    'name'        => 'Medical Clinic',
    'description' => 'Patient intake pipeline, appointment reminders, insurance verification, and a HIPAA-style consultation form.',
    'icon'        => 'heroicon-o-heart',

    'pipelines' => [
        [
            'name'        => 'Patient Intake',
            'description' => 'From first inquiry to completed treatment.',
            'is_default'  => true,
            'stages' => [
                ['name' => 'New Inquiry',         'color' => '#94a3b8', 'win_probability' => 5,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Intake Scheduled',    'color' => '#60a5fa', 'win_probability' => 20, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Consultation Booked', 'color' => '#a78bfa', 'win_probability' => 40, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Treatment Plan',      'color' => '#f59e0b', 'win_probability' => 65, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Treatment In Progress', 'color' => '#22c55e', 'win_probability' => 85, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Completed',           'color' => '#10b981', 'win_probability' => 100, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Lost',                'color' => '#ef4444', 'win_probability' => 0,  'is_won' => false, 'is_lost' => true],
            ],
        ],
    ],

    'custom_fields' => [
        ['entity_type' => 'lead', 'name' => 'Service Interest',         'key' => 'service_interest',     'field_type' => 'multi_select', 'options' => ['Dental', 'Orthodontics', 'Cosmetic', 'General', 'Pediatric', 'Emergency']],
        ['entity_type' => 'lead', 'name' => 'Insurance Provider',       'key' => 'insurance_provider',   'field_type' => 'text'],
        ['entity_type' => 'lead', 'name' => 'Referred By',              'key' => 'referred_by',          'field_type' => 'select', 'options' => ['Friend', 'Google', 'Insurance', 'Doctor', 'Walk-in', 'Social Media']],
        ['entity_type' => 'lead', 'name' => 'Preferred Appointment Time', 'key' => 'preferred_time',     'field_type' => 'select', 'options' => ['Morning', 'Afternoon', 'Evening', 'Weekend']],
        ['entity_type' => 'lead', 'name' => 'Date of Birth',            'key' => 'date_of_birth',        'field_type' => 'date'],
    ],

    'tags' => [
        ['name' => 'New Patient', 'color' => '#3b82f6'],
        ['name' => 'Returning',   'color' => '#10b981'],
        ['name' => 'VIP',         'color' => '#8b5cf6'],
        ['name' => 'Referral',    'color' => '#f59e0b'],
        ['name' => 'Insurance Pending', 'color' => '#f97316'],
    ],

    'email_templates' => [
        [
            'name'      => 'Appointment Reminder',
            'subject'   => 'Reminder: your appointment with the clinic',
            'body_html' => '<p>Hi {{lead.first_name}},</p><p>This is a friendly reminder of your upcoming appointment. Please arrive 10 minutes early and bring your insurance card and a photo ID.</p>',
        ],
        [
            'name'      => 'Post-Visit Follow-up',
            'subject'   => 'How are you feeling, {{lead.first_name}}?',
            'body_html' => '<p>Hi {{lead.first_name}},</p><p>Thanks for visiting us. Please reply if you have any questions about your treatment plan or recovery.</p>',
        ],
        [
            'name'      => 'Consultation Confirmation',
            'subject'   => 'Your consultation is confirmed',
            'body_html' => '<p>Hi {{lead.first_name}},</p><p>Your consultation is booked. Please complete your medical history form in advance — link will be sent separately.</p>',
        ],
    ],

    'automation_templates' => [
        ['key' => 'welcome_new_lead'],
        ['key' => 'followup_no_activity'],
        [
            'name'           => 'Appointment reminder 24h before',
            'description'    => 'Sends a reminder when lead is tagged Consultation Booked.',
            'trigger_type'   => 'tag_added',
            'trigger_config' => ['tag' => 'Consultation Booked'],
            'steps' => [
                [
                    'type'   => 'action',
                    'config' => [
                        'action'  => 'send_email',
                        'subject' => 'Reminder: your appointment tomorrow',
                        'body'    => '<p>Hi {first_name}, looking forward to seeing you tomorrow.</p>',
                    ],
                ],
            ],
        ],
        [
            'name'           => 'Insurance verification task',
            'description'    => 'Creates a task to verify insurance when a new inquiry arrives.',
            'trigger_type'   => 'lead_created',
            'trigger_config' => [],
            'steps' => [
                [
                    'type'   => 'action',
                    'config' => [
                        'action'         => 'create_task',
                        'title'          => 'Verify insurance for {first_name} {last_name}',
                        'hours_from_now' => 24,
                        'priority'       => 'medium',
                    ],
                ],
            ],
        ],
        [
            'name'           => 'Post-visit follow-up 3 days',
            'description'    => 'Sends a follow-up email when a lead reaches Completed stage.',
            'trigger_type'   => 'lead_stage_changed',
            'trigger_config' => [],
            'steps' => [
                [
                    'type'   => 'condition',
                    'config' => ['type' => 'field_contains', 'field' => 'pipeline_stage.name', 'value' => 'Completed'],
                ],
                [
                    'type'   => 'action',
                    'config' => [
                        'action'  => 'send_email',
                        'subject' => 'How are you feeling, {first_name}?',
                        'body'    => '<p>Hi {first_name}, just checking in after your visit.</p>',
                    ],
                ],
            ],
        ],
    ],

    'forms' => [
        [
            'name'              => 'Book a Consultation',
            'slug'              => 'book-consultation',
            'title'             => 'Request your consultation',
            'description'       => 'Tell us a little about yourself and we\'ll reach out to confirm.',
            'submit_label'      => 'Request Appointment',
            'thank_you_message' => 'Thanks — our staff will contact you shortly to confirm a time.',
            'fields' => [
                ['type' => 'text',     'label' => 'Full Name',              'field_key' => 'name',              'required' => true,  'sort_order' => 0],
                ['type' => 'email',    'label' => 'Email',                  'field_key' => 'email',             'required' => true,  'sort_order' => 1],
                ['type' => 'phone',    'label' => 'Phone',                  'field_key' => 'phone',             'required' => true,  'sort_order' => 2],
                ['type' => 'select',   'label' => 'Preferred Time',         'field_key' => 'preferred_time',    'required' => false, 'sort_order' => 3, 'options' => ['Morning', 'Afternoon', 'Evening', 'Weekend']],
                ['type' => 'text',     'label' => 'Insurance Provider',     'field_key' => 'insurance',         'required' => false, 'sort_order' => 4],
                ['type' => 'textarea', 'label' => 'Reason for Visit',       'field_key' => 'reason',            'required' => false, 'sort_order' => 5],
                ['type' => 'gdpr',     'label' => 'I understand my health information is protected under HIPAA and I consent to be contacted about my care.', 'field_key' => 'consent', 'required' => true, 'sort_order' => 6],
            ],
        ],
    ],
];
