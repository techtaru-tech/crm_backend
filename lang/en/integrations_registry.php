<?php

/**
 * Display strings for App\Integrations\IntegrationRegistry.
 *
 * Pattern: translator-first with English fallback. The registry class
 * keeps its English constants as the canonical source of truth (used for
 * internal grouping/match keys); this lang file only drives the DISPLAY
 * layer so the integration setup modal, dropdown, and badge column read
 * in the operator's locale.
 *
 * Brand and product names (HubSpot, Salesforce, Mailchimp, Slack, Zapier,
 * Make, Pipedrive, Notion, Airtable, Zoho, ActiveCampaign, ConvertKit,
 * Brevo, Sendinblue, Klaviyo, Webhook, Zoom, Microsoft Teams, Google
 * Sheets, Discord, Telegram, WhatsApp, OneSignal, Twilio, Vonage, Nexmo,
 * Intercom, Zendesk, Bitrix24, SugarCRM, Vtiger, Streak, Insightly,
 * Copper, Close, MailerLite, Moosend, GetResponse, Drip, Pabbly,
 * Activepieces, Workato, n8n, etc.) intentionally remain literal across
 * locales -- these are international product names operators recognize.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Category names
    |--------------------------------------------------------------------------
    | Rendered as a colon-prefix in the integration type dropdown and as a
    | badge in the integrations table. The English values here mirror the
    | strings used in IntegrationRegistry::CATEGORIES so a missing
    | translation falls back to the literal English string without
    | breaking the badge colour match expression in IntegrationResource.
    */
    'categories' => [
        'automation'            => 'Automation',
        'crm'                   => 'CRM',
        'email_marketing'       => 'Email Marketing',
        'communication'         => 'Communication',
        'data_and_productivity' => 'Data & Productivity',
        'other'                 => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration labels
    |--------------------------------------------------------------------------
    | Only the descriptive (non-brand) entries are translated here. Brand
    | names like 'HubSpot CRM' or 'Pipedrive' are left to fall back
    | through the LABELS const because they read identically across
    | locales.
    */
    'labels' => [
        'generic_webhook' => 'Generic Webhook',
        'rest_api_push'   => 'REST API Push',
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration descriptions
    |--------------------------------------------------------------------------
    | One-line marketing copy shown under each integration card in the
    | "Add Integration" picker.  Brand names stay literal (HubSpot,
    | Zapier, etc.) so non-English locales still recognise the product;
    | the surrounding verbs and prepositions translate.
    */
    'descriptions' => [
        'zapier'          => 'Send leads to 5,000+ apps via Zapier webhook triggers',
        'n8n'             => 'Automate workflows with n8n webhook nodes',
        'make'            => 'Connect to Make (Integromat) webhook modules',
        'pabbly'          => 'Send leads to Pabbly Connect workflows',
        'activepieces'    => 'Trigger Activepieces flows on new leads',
        'workato'         => 'Send leads to Workato automation recipes',
        'hubspot'         => 'Create/update Contacts and Deals in HubSpot CRM',
        'salesforce'      => 'Push leads as Salesforce Leads or Contacts',
        'pipedrive'       => 'Create Persons and Deals in Pipedrive',
        'zoho_crm'        => 'Create Leads in Zoho CRM',
        'freshsales'      => 'Create Contacts and Deals in Freshsales',
        'monday'          => 'Create Items in Monday.com boards',
        'copper'          => 'Create Leads/Persons in Copper CRM',
        'close'           => 'Create Leads and Contacts in Close CRM',
        'streak'          => 'Create Boxes in Streak pipelines',
        'insightly'       => 'Create Leads or Contacts in Insightly',
        'bitrix24'        => 'Create CRM Leads in Bitrix24',
        'sugarcrm'        => 'Create Lead records in SugarCRM',
        'vtiger'          => 'Create Lead records in Vtiger',
        'mailchimp'       => 'Subscribe leads to Mailchimp audiences',
        'activecampaign'  => 'Add contacts to ActiveCampaign lists',
        'klaviyo'         => 'Add profiles to Klaviyo lists',
        'brevo'           => 'Create/update Brevo (Sendinblue) contacts',
        'convertkit'      => 'Subscribe leads to ConvertKit sequences',
        'drip'            => 'Create/update subscribers in Drip',
        'getresponse'     => 'Add contacts to GetResponse lists',
        'moosend'         => 'Subscribe leads to Moosend mailing lists',
        'mailerlite'      => 'Add subscribers to MailerLite groups',
        'slack'           => 'Post lead cards to Slack channels',
        'microsoft_teams' => 'Post lead cards to Microsoft Teams channels',
        'twilio'          => 'Send SMS notifications via Twilio',
        'vonage'          => 'Send SMS notifications via Vonage/Nexmo',
        'intercom'        => 'Create/update Contacts in Intercom',
        'zendesk'         => 'Create Tickets or Contacts in Zendesk',
        'google_sheets'   => 'Append lead data as rows in Google Sheets',
        'notion'          => 'Create pages in Notion databases',
        'airtable'        => 'Create records in Airtable bases',
        'generic_webhook' => 'Send leads to any URL with a custom JSON payload',
        'rest_api_push'   => 'POST lead data to a REST API endpoint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-integration field labels, placeholders, and select-option text
    |--------------------------------------------------------------------------
    | Keyed as <integration_type>.<field_key>.<attribute>. Attribute is
    | one of: label, placeholder, or options.<option_key>. Anything that
    | references a brand-specific identifier (Audience ID, Mailing List
    | ID, etc.) keeps that brand context in the English string but is
    | still routed through the translator so localisations can adjust
    | the descriptive part.
    */
    'fields' => [
        // Generic webhook + REST API push (share the same field schema)
        'generic_webhook' => [
            'webhook_url' => [
                'label' => 'Webhook URL',
            ],
            'method' => [
                'label' => 'HTTP Method',
            ],
            'auth_type' => [
                'label'   => 'Auth Type',
                'options' => [
                    'none'    => 'None',
                    'bearer'  => 'Bearer Token',
                    'api_key' => 'API Key Header',
                    'basic'   => 'Basic Auth',
                ],
            ],
            'auth_value' => [
                'label' => 'Auth Token / Key Value',
            ],
            'auth_header' => [
                'label'       => 'API Key Header Name',
                'placeholder' => 'X-API-Key (used when Auth Type = API Key Header)',
            ],
            'body_template' => [
                'label' => 'JSON Body Template',
            ],
            'custom_headers' => [
                'label' => 'Custom Headers (JSON array of {key,value} objects)',
            ],
        ],

        // Microsoft Teams
        'microsoft_teams' => [
            'webhook_url' => [
                'label' => 'Webhook URL',
            ],
            'message_template' => [
                'label'       => 'Message Template',
                'placeholder' => 'Optional: plain text template using {{lead.email}}, {{lead.pipeline_stage}}, etc.',
            ],
        ],

        // Slack
        'slack' => [
            'webhook_url' => [
                'label' => 'Webhook URL',
            ],
            'message_template' => [
                'label' => 'Message Template',
            ],
        ],

        // HubSpot
        'hubspot' => [
            '_oauth_info' => [
                'label' => 'OAuth Connection',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Required for OAuth flow',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Required for OAuth flow',
            ],
            'access_token' => [
                'label' => 'Access Token (manual override)',
            ],
            'create_deal' => [
                'label'   => 'Create Deal on sync',
                'options' => [
                    'yes' => 'Yes',
                    'no'  => 'No',
                ],
            ],
            'deal_pipeline' => [
                'label' => 'Deal Pipeline ID',
            ],
            'deal_stage' => [
                'label' => 'Deal Stage ID',
            ],
        ],

        // Salesforce
        'salesforce' => [
            '_oauth_info' => [
                'label' => 'OAuth Connection',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Required for OAuth flow',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Required for OAuth flow',
            ],
            'instance_url' => [
                'label' => 'Instance URL',
            ],
            'access_token' => [
                'label' => 'Access Token (manual override)',
            ],
            'sf_object' => [
                'label' => 'Object Type',
            ],
        ],

        // Pipedrive
        'pipedrive' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        // Zoho CRM
        'zoho_crm' => [
            '_oauth_info' => [
                'label' => 'OAuth Connection',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Required for OAuth flow',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Required for OAuth flow',
            ],
            'access_token' => [
                'label' => 'Access Token (manual override)',
            ],
            'region' => [
                'label' => 'Region (com, eu, in, com.au)',
            ],
        ],

        // Freshsales
        'freshsales' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'domain' => [
                'label' => 'Subdomain (e.g. yourcompany)',
            ],
            'create_deal' => [
                'label'   => 'Create Deal on Contact Sync',
                'options' => [
                    'yes' => 'Yes',
                    'no'  => 'No',
                ],
            ],
            'deal_pipeline_id' => [
                'label' => 'Deal Pipeline ID (optional)',
            ],
            'deal_stage_id' => [
                'label' => 'Deal Stage ID (optional)',
            ],
        ],

        // Monday.com
        'monday' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'board_id' => [
                'label' => 'Board ID',
            ],
        ],

        // Copper
        'copper' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'user_email' => [
                'label' => 'User Email',
            ],
        ],

        // Close
        'close' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        // Insightly
        'insightly' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        // Streak
        'streak' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'pipeline_key' => [
                'label' => 'Pipeline Key',
            ],
        ],

        // SugarCRM
        'sugarcrm' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'Instance URL',
            ],
        ],

        // Vtiger
        'vtiger' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'Instance URL',
            ],
        ],

        // Mailchimp
        'mailchimp' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'Audience ID',
            ],
            'data_center' => [
                'label' => 'Data Center (e.g. us1)',
            ],
            'tags' => [
                'label' => 'Tags (comma-separated)',
            ],
        ],

        // ActiveCampaign
        'activecampaign' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_url' => [
                'label' => 'API URL',
            ],
            'list_id' => [
                'label' => 'List ID (optional)',
            ],
            'tags' => [
                'label' => 'Tags (comma-separated)',
            ],
        ],

        // Klaviyo
        'klaviyo' => [
            'api_key' => [
                'label' => 'Private API Key',
            ],
            'list_id' => [
                'label' => 'List ID',
            ],
        ],

        // Brevo
        'brevo' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'List ID',
            ],
        ],

        // ConvertKit
        'convertkit' => [
            'api_key' => [
                'label' => 'API Key (Public)',
            ],
            'form_id' => [
                'label' => 'Form / Sequence ID',
            ],
        ],

        // Drip
        'drip' => [
            'api_token' => [
                'label' => 'API Token',
            ],
            'account_id' => [
                'label' => 'Account ID',
            ],
        ],

        // GetResponse
        'getresponse' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'Campaign ID',
            ],
        ],

        // Moosend
        'moosend' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'Mailing List ID',
            ],
        ],

        // MailerLite
        'mailerlite' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'group_id' => [
                'label' => 'Group ID',
            ],
        ],

        // Twilio
        'twilio' => [
            'account_sid' => [
                'label' => 'Account SID',
            ],
            'auth_token' => [
                'label' => 'Auth Token',
            ],
            'from_number' => [
                'label' => 'From Number',
            ],
            'sms_template' => [
                'label'       => 'SMS Template',
                'placeholder' => 'New lead: {{lead.first_name}} ({{lead.email}})',
            ],
        ],

        // Vonage
        'vonage' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_secret' => [
                'label' => 'API Secret',
            ],
            'from_number' => [
                'label' => 'From Number',
            ],
            'sms_template' => [
                'label' => 'SMS Template',
            ],
        ],

        // Intercom
        'intercom' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
        ],

        // Zendesk
        'zendesk' => [
            'subdomain' => [
                'label'       => 'Subdomain',
                'placeholder' => 'yourcompany',
            ],
            'email' => [
                'label' => 'Admin Email',
            ],
            'api_token_zendesk' => [
                'label' => 'API Token',
            ],
            'create_ticket' => [
                'label'   => 'Create as Ticket (vs Contact)',
                'options' => [
                    'contact' => 'Contact (End User)',
                    'ticket'  => 'Ticket',
                ],
            ],
            'ticket_subject_template' => [
                'label'       => 'Ticket Subject Template',
                'placeholder' => 'New Lead: {{lead.first_name}} {{lead.last_name}}',
            ],
        ],

        // Google Sheets
        'google_sheets' => [
            '_oauth_info' => [
                'label' => 'OAuth Connection',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Required for OAuth flow',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Required for OAuth flow',
            ],
            'access_token' => [
                'label' => 'OAuth Access Token (auto-filled after OAuth)',
            ],
            'spreadsheet_id' => [
                'label' => 'Spreadsheet ID',
            ],
            'sheet_name' => [
                'label' => 'Sheet Name',
            ],
        ],

        // Notion
        'notion' => [
            'api_key' => [
                'label' => 'Integration Token',
            ],
            'database_id' => [
                'label' => 'Database ID',
            ],
            'property_mapping' => [
                'label' => 'Property Mapping (JSON array — leave blank for defaults)',
            ],
        ],

        // Airtable
        'airtable' => [
            'api_key' => [
                'label' => 'Personal Access Token',
            ],
            'base_id' => [
                'label' => 'Base ID',
            ],
            'table_id' => [
                'label' => 'Table Name',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exceptions surfaced to the UI
    |--------------------------------------------------------------------------
    | InvalidArgumentException::getMessage() return values that
    | bubble up through Filament Notification->title($e->getMessage())
    | in IntegrationResource and ListIntegrations. Routed through
    | __() so the operator sees the message in the active locale
    | when, for example, a DB row references an integration type
    | that was removed from IntegrationRegistry::INTEGRATIONS.
    */
    'exceptions' => [
        'unknown_type' => 'Unknown integration type: :type',
    ],
];
