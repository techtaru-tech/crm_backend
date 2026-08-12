<?php

declare(strict_types=1);

return [

    'teams' => [
        'summary_new_lead'      => 'LeadHub से नई लीड',
        'card_title_new_lead'   => 'नई लीड: :name',
        'field_email'           => 'ईमेल',
        'field_phone'           => 'फ़ोन',
        'field_source'          => 'स्रोत',
        'field_score'           => 'स्कोर',
        'field_pipeline'        => 'पाइपलाइन',
        'field_stage'           => 'चरण',
        'test_message'          => 'LeadHub कनेक्शन परीक्षण',
    ],

    'slack' => [
        'test_message'          => 'LeadHub कनेक्शन परीक्षण ✓',
        'default_template'      => "*नई लीड:* :name\n• ईमेल: :email\n• फ़ोन: :phone\n• स्रोत: :source\n• स्कोर: :score\n• पाइपलाइन: :pipeline / :stage",
    ],

    'sms' => [
        'voniage_default'       => 'नई लीड: {{lead.first_name}} {{lead.last_name}}',
        'twilio_default'        => 'नई लीड: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    ],
];
