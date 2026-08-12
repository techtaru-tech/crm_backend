<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Integration messaging templates (Slack / Microsoft Teams default copy)
|--------------------------------------------------------------------------
|
| Default templates rendered by App\Integrations\Connectors\SlackConnector
| and App\Integrations\Connectors\MicrosoftTeamsConnector when the tenant
| has not provided a custom message_template. Consumed via
| __('integration_messaging_templates.<channel>.<key>').
|
| Note: the literal "LeadHub" inside test/title strings is the canonical
| product name and is intentionally not extracted further.
|
*/

return [

    // ─── Microsoft Teams ──────────────────────────────────────────────
    'teams' => [
        'summary_new_lead'      => 'New Lead from LeadHub',
        'card_title_new_lead'   => 'New Lead: :name',
        'field_email'           => 'Email',
        'field_phone'           => 'Phone',
        'field_source'          => 'Source',
        'field_score'           => 'Score',
        'field_pipeline'        => 'Pipeline',
        'field_stage'           => 'Stage',
        'test_message'          => 'LeadHub connection test',
    ],

    // ─── Slack ────────────────────────────────────────────────────────
    'slack' => [
        'test_message'          => 'LeadHub connection test ✓',
        'default_template'      => "*New Lead:* :name\n• Email: :email\n• Phone: :phone\n• Source: :source\n• Score: :score\n• Pipeline: :pipeline / :stage",
    ],

    // ─── SMS (Vonage + Twilio default templates pushed to lead phones) ─
    // Tokens `{{lead.*}}` are downstream-interpolated by Connector::interpolateTemplate()
    // and are NOT Laravel translator placeholders — keep them literal.
    'sms' => [
        'voniage_default'       => 'New lead: {{lead.first_name}} {{lead.last_name}}',
        'twilio_default'        => 'New lead: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    ],
];
