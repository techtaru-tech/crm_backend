<?php

declare(strict_types=1);

return [

    'teams' => [
        'summary_new_lead'      => 'Nuevo lead desde LeadHub',
        'card_title_new_lead'   => 'Nuevo lead: :name',
        'field_email'           => 'Correo electrónico',
        'field_phone'           => 'Teléfono',
        'field_source'          => 'Fuente',
        'field_score'           => 'Puntuación',
        'field_pipeline'        => 'Pipeline',
        'field_stage'           => 'Etapa',
        'test_message'          => 'Prueba de conexión de LeadHub',
    ],

    'slack' => [
        'test_message'          => 'Prueba de conexión de LeadHub ✓',
        'default_template'      => "*Nuevo lead:* :name\n• Correo: :email\n• Teléfono: :phone\n• Fuente: :source\n• Puntuación: :score\n• Pipeline: :pipeline / :stage",
    ],

    'sms' => [
        'voniage_default'       => 'Nuevo lead: {{lead.first_name}} {{lead.last_name}}',
        'twilio_default'        => 'Nuevo lead: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    ],
];
