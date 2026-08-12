<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Integration messaging templates (Slack / Microsoft Teams default copy)
|--------------------------------------------------------------------------
*/

return [

    // ─── Microsoft Teams ──────────────────────────────────────────────
    'teams' => [
        'summary_new_lead'      => 'عميل محتمل جديد من LeadHub',
        'card_title_new_lead'   => 'عميل محتمل جديد: :name',
        'field_email'           => 'البريد الإلكتروني',
        'field_phone'           => 'الهاتف',
        'field_source'          => 'المصدر',
        'field_score'           => 'النقاط',
        'field_pipeline'        => 'خط الأنابيب',
        'field_stage'           => 'المرحلة',
        'test_message'          => 'اختبار اتصال LeadHub',
    ],

    // ─── Slack ────────────────────────────────────────────────────────
    'slack' => [
        'test_message'          => 'اختبار اتصال LeadHub ✓',
        'default_template'      => "*عميل محتمل جديد:* :name\n• البريد الإلكتروني: :email\n• الهاتف: :phone\n• المصدر: :source\n• النقاط: :score\n• خط الأنابيب: :pipeline / :stage",
    ],

    // ─── SMS (Vonage + Twilio default templates pushed to lead phones) ─
    // Tokens `{{lead.*}}` are downstream-interpolated by Connector::interpolateTemplate()
    // and are NOT Laravel translator placeholders — keep them literal.
    'sms' => [
        'voniage_default'       => 'عميل محتمل جديد: {{lead.first_name}} {{lead.last_name}}',
        'twilio_default'        => 'عميل محتمل جديد: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    ],
];
