<?php

namespace App\Integrations;

use App\Integrations\Connectors\ActiveCampaignConnector;
use App\Integrations\Connectors\ActivepiecesConnector;
use App\Integrations\Connectors\AirtableConnector;
use App\Integrations\Connectors\BitrixConnector;
use App\Integrations\Connectors\BrevoConnector;
use App\Integrations\Connectors\CloseConnector;
use App\Integrations\Connectors\ConvertKitConnector;
use App\Integrations\Connectors\CopperConnector;
use App\Integrations\Connectors\DripConnector;
use App\Integrations\Connectors\FreshsalesConnector;
use App\Integrations\Connectors\GenericWebhookConnector;
use App\Integrations\Connectors\GetResponseConnector;
use App\Integrations\Connectors\GoogleSheetsConnector;
use App\Integrations\Connectors\HubSpotConnector;
use App\Integrations\Connectors\InsightlyConnector;
use App\Integrations\Connectors\IntercomConnector;
use App\Integrations\Connectors\KlaviyoConnector;
use App\Integrations\Connectors\MailchimpConnector;
use App\Integrations\Connectors\MailerLiteConnector;
use App\Integrations\Connectors\MakeConnector;
use App\Integrations\Connectors\MicrosoftTeamsConnector;
use App\Integrations\Connectors\MondayConnector;
use App\Integrations\Connectors\MoosendConnector;
use App\Integrations\Connectors\N8nConnector;
use App\Integrations\Connectors\NotionConnector;
use App\Integrations\Connectors\PabblyConnector;
use App\Integrations\Connectors\PipedriveConnector;
use App\Integrations\Connectors\RestApiPushConnector;
use App\Integrations\Connectors\SalesforceConnector;
use App\Integrations\Connectors\SlackConnector;
use App\Integrations\Connectors\StreakConnector;
use App\Integrations\Connectors\SugarCRMConnector;
use App\Integrations\Connectors\TwilioConnector;
use App\Integrations\Connectors\VoniageConnector;
use App\Integrations\Connectors\VtigerConnector;
use App\Integrations\Connectors\WorkatoConnector;
use App\Integrations\Connectors\ZapierConnector;
use App\Integrations\Connectors\ZendeskConnector;
use App\Integrations\Connectors\ZohoCRMConnector;

class IntegrationRegistry
{
    public const INTEGRATIONS = [
        'zapier'           => ZapierConnector::class,
        'n8n'              => N8nConnector::class,
        'make'             => MakeConnector::class,
        'pabbly'           => PabblyConnector::class,
        'activepieces'     => ActivepiecesConnector::class,
        'workato'          => WorkatoConnector::class,
        'hubspot'          => HubSpotConnector::class,
        'salesforce'       => SalesforceConnector::class,
        'pipedrive'        => PipedriveConnector::class,
        'zoho_crm'         => ZohoCRMConnector::class,
        'freshsales'       => FreshsalesConnector::class,
        'monday'           => MondayConnector::class,
        'copper'           => CopperConnector::class,
        'close'            => CloseConnector::class,
        'streak'           => StreakConnector::class,
        'insightly'        => InsightlyConnector::class,
        'bitrix24'         => BitrixConnector::class,
        'sugarcrm'         => SugarCRMConnector::class,
        'vtiger'           => VtigerConnector::class,
        'mailchimp'        => MailchimpConnector::class,
        'activecampaign'   => ActiveCampaignConnector::class,
        'klaviyo'          => KlaviyoConnector::class,
        'brevo'            => BrevoConnector::class,
        'convertkit'       => ConvertKitConnector::class,
        'drip'             => DripConnector::class,
        'getresponse'      => GetResponseConnector::class,
        'moosend'          => MoosendConnector::class,
        'mailerlite'       => MailerLiteConnector::class,
        'slack'            => SlackConnector::class,
        'microsoft_teams'  => MicrosoftTeamsConnector::class,
        'twilio'           => TwilioConnector::class,
        'vonage'           => VoniageConnector::class,
        'intercom'         => IntercomConnector::class,
        'zendesk'          => ZendeskConnector::class,
        'google_sheets'    => GoogleSheetsConnector::class,
        'notion'           => NotionConnector::class,
        'airtable'         => AirtableConnector::class,
        'generic_webhook'  => GenericWebhookConnector::class,
        'rest_api_push'    => RestApiPushConnector::class,
    ];

    /**
     * Translator-first lookup with English fallback. Used throughout this
     * registry so the CodeCanyon "no hardcoded text" rule is satisfied
     * while keeping the English constants below as the canonical source
     * of truth for grouping, match expressions, and missing-translation
     * fallback.
     */
    private static function tx(string $key, string $fallback): string
    {
        $translated = __($key);
        return $translated === $key ? $fallback : (string) $translated;
    }

    public static function getLabel(string $type): string
    {
        $fallback = self::LABELS[$type] ?? ucfirst(str_replace('_', ' ', $type));
        return self::tx('integrations_registry.labels.' . $type, $fallback);
    }

    public static function getDescription(string $type): string
    {
        $fallback = self::DESCRIPTIONS[$type] ?? '';
        // Empty descriptions stay empty — don't waste a translator lookup
        // for a connector we have no description for.
        if ($fallback === '') {
            return '';
        }
        return self::tx('integrations_registry.descriptions.' . $type, $fallback);
    }

    /**
     * Canonical English category name. IntegrationResource's badge colour
     * match expression and ListIntegrations' grouping/ordering logic both
     * key off the literal English strings here, so this method intentionally
     * does NOT route through the translator -- the colour mapping and tab
     * order would silently break for non-English locales if it did. For UI
     * display, callers should use getCategoryLabel().
     */
    public static function getCategory(string $type): string
    {
        return self::CATEGORIES[$type] ?? 'Other';
    }

    /**
     * Localised category name suitable for display in dropdowns and badges.
     */
    public static function getCategoryLabel(string $type): string
    {
        $english = self::getCategory($type);
        $slug    = self::CATEGORY_SLUGS[$english] ?? 'other';
        return self::tx('integrations_registry.categories.' . $slug, $english);
    }

    public static function getConfigFields(string $type): array
    {
        // The webhook-only types share the generic_webhook field schema in
        // lang/en/integrations_registry.php (single source of truth for
        // the "Webhook URL" label, etc.).
        $schemaSlug = match ($type) {
            'rest_api_push' => 'generic_webhook',
            default         => $type,
        };

        // Local helper: translator-first for <schemaSlug>.<field>.<attr>
        // with the existing English literal as fallback. Keeps the inline
        // arrays below readable while still satisfying CodeCanyon's
        // "no hardcoded text" rule.
        $t = static fn(string $field, string $attr, string $fallback): string =>
            self::tx('integrations_registry.fields.' . $schemaSlug . '.' . $field . '.' . $attr, $fallback);

        return match (true) {
            in_array($type, ['zapier', 'n8n', 'make', 'pabbly', 'activepieces', 'workato', 'bitrix24']) => [
                ['key' => 'webhook_url', 'label' => self::tx('integrations_registry.fields.generic_webhook.webhook_url.label', 'Webhook URL'), 'type' => 'url', 'required' => true],
            ],
            $type === 'microsoft_teams' => [
                ['key' => 'webhook_url',      'label' => $t('webhook_url', 'label', 'Webhook URL'),                  'type' => 'url',      'required' => true],
                ['key' => 'message_template', 'label' => $t('message_template', 'label', 'Message Template'),         'type' => 'textarea', 'placeholder' => $t('message_template', 'placeholder', 'Optional: plain text template using {{lead.email}}, {{lead.pipeline_stage}}, etc.')],
            ],
            $type === 'generic_webhook' || $type === 'rest_api_push' => [
                ['key' => 'webhook_url',    'label' => $t('webhook_url', 'label', 'Webhook URL'),                     'type' => 'url',      'required' => true],
                ['key' => 'method',         'label' => $t('method', 'label', 'HTTP Method'),                          'type' => 'select',   'options' => ['post' => 'POST', 'put' => 'PUT', 'patch' => 'PATCH'], 'default' => 'post'],
                ['key' => 'auth_type',      'label' => $t('auth_type', 'label', 'Auth Type'),                         'type' => 'select',   'options' => [
                    'none'    => $t('auth_type', 'options.none', 'None'),
                    'bearer'  => $t('auth_type', 'options.bearer', 'Bearer Token'),
                    'api_key' => $t('auth_type', 'options.api_key', 'API Key Header'),
                    'basic'   => $t('auth_type', 'options.basic', 'Basic Auth'),
                ], 'default' => 'none'],
                ['key' => 'auth_value',     'label' => $t('auth_value', 'label', 'Auth Token / Key Value'),           'type' => 'password'],
                ['key' => 'auth_header',    'label' => $t('auth_header', 'label', 'API Key Header Name'),             'type' => 'text',     'placeholder' => $t('auth_header', 'placeholder', 'X-API-Key (used when Auth Type = API Key Header)')],
                ['key' => 'body_template',  'label' => $t('body_template', 'label', 'JSON Body Template'),            'type' => 'textarea', 'placeholder' => '{"email":"{{lead.email}}","name":"{{lead.first_name}} {{lead.last_name}}"}'],
                ['key' => 'custom_headers', 'label' => $t('custom_headers', 'label', 'Custom Headers (JSON array of {key,value} objects)'), 'type' => 'textarea', 'placeholder' => '[{"key":"X-Custom-Header","value":"my-value"},{"key":"X-Source","value":"leadhub"}]'],
            ],
            $type === 'slack' => [
                ['key' => 'webhook_url',      'label' => $t('webhook_url', 'label', 'Webhook URL'),               'type' => 'url',      'required' => true],
                ['key' => 'message_template', 'label' => $t('message_template', 'label', 'Message Template'),     'type' => 'textarea'],
            ],
            $type === 'hubspot' => [
                ['key' => '_oauth_info',   'label' => $t('_oauth_info', 'label', 'OAuth Connection'),                'type' => 'oauth_button', 'provider' => 'hubspot'],
                ['key' => 'client_id',     'label' => $t('client_id', 'label', 'OAuth Client ID'),                   'type' => 'text',     'placeholder' => $t('client_id', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'client_secret', 'label' => $t('client_secret', 'label', 'OAuth Client Secret'),           'type' => 'password', 'placeholder' => $t('client_secret', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'access_token',  'label' => $t('access_token', 'label', 'Access Token (manual override)'), 'type' => 'password'],
                ['key' => 'create_deal',   'label' => $t('create_deal', 'label', 'Create Deal on sync'),             'type' => 'select',   'options' => [
                    '1' => $t('create_deal', 'options.yes', 'Yes'),
                    '0' => $t('create_deal', 'options.no', 'No'),
                ], 'default' => '1'],
                ['key' => 'deal_pipeline', 'label' => $t('deal_pipeline', 'label', 'Deal Pipeline ID'),              'type' => 'text'],
                ['key' => 'deal_stage',    'label' => $t('deal_stage', 'label', 'Deal Stage ID'),                    'type' => 'text'],
            ],
            $type === 'salesforce' => [
                ['key' => '_oauth_info',   'label' => $t('_oauth_info', 'label', 'OAuth Connection'),                'type' => 'oauth_button', 'provider' => 'salesforce'],
                ['key' => 'client_id',     'label' => $t('client_id', 'label', 'OAuth Client ID'),                   'type' => 'text',     'placeholder' => $t('client_id', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'client_secret', 'label' => $t('client_secret', 'label', 'OAuth Client Secret'),           'type' => 'password', 'placeholder' => $t('client_secret', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'instance_url',  'label' => $t('instance_url', 'label', 'Instance URL'),                   'type' => 'url',      'required' => true, 'placeholder' => 'https://yourorg.salesforce.com'],
                ['key' => 'access_token',  'label' => $t('access_token', 'label', 'Access Token (manual override)'), 'type' => 'password'],
                ['key' => 'sf_object',     'label' => $t('sf_object', 'label', 'Object Type'),                       'type' => 'select',   'options' => ['Lead' => 'Lead', 'Contact' => 'Contact']],
            ],
            $type === 'pipedrive' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key'), 'type' => 'password', 'required' => true],
            ],
            $type === 'zoho_crm' => [
                ['key' => '_oauth_info',   'label' => $t('_oauth_info', 'label', 'OAuth Connection'),                'type' => 'oauth_button', 'provider' => 'zohocrm'],
                ['key' => 'client_id',     'label' => $t('client_id', 'label', 'OAuth Client ID'),                   'type' => 'text',     'placeholder' => $t('client_id', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'client_secret', 'label' => $t('client_secret', 'label', 'OAuth Client Secret'),           'type' => 'password', 'placeholder' => $t('client_secret', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'access_token',  'label' => $t('access_token', 'label', 'Access Token (manual override)'), 'type' => 'password'],
                ['key' => 'region',        'label' => $t('region', 'label', 'Region (com, eu, in, com.au)'),         'type' => 'text', 'default' => 'com'],
            ],
            $type === 'freshsales' => [
                ['key' => 'api_key',          'label' => $t('api_key', 'label', 'API Key'),                          'type' => 'password', 'required' => true],
                ['key' => 'domain',           'label' => $t('domain', 'label', 'Subdomain (e.g. yourcompany)'),      'type' => 'text',     'required' => true],
                ['key' => 'create_deal',      'label' => $t('create_deal', 'label', 'Create Deal on Contact Sync'),  'type' => 'select',   'options' => [
                    '1' => $t('create_deal', 'options.yes', 'Yes'),
                    '0' => $t('create_deal', 'options.no', 'No'),
                ], 'default' => '1'],
                ['key' => 'deal_pipeline_id', 'label' => $t('deal_pipeline_id', 'label', 'Deal Pipeline ID (optional)'), 'type' => 'text'],
                ['key' => 'deal_stage_id',    'label' => $t('deal_stage_id', 'label', 'Deal Stage ID (optional)'),      'type' => 'text'],
            ],
            $type === 'monday' => [
                ['key' => 'api_key',  'label' => $t('api_key', 'label', 'API Key'),   'type' => 'password', 'required' => true],
                ['key' => 'board_id', 'label' => $t('board_id', 'label', 'Board ID'), 'type' => 'text',     'required' => true],
            ],
            $type === 'copper' => [
                ['key' => 'api_key',    'label' => $t('api_key', 'label', 'API Key'),       'type' => 'password', 'required' => true],
                ['key' => 'user_email', 'label' => $t('user_email', 'label', 'User Email'), 'type' => 'email',    'required' => true],
            ],
            in_array($type, ['close', 'insightly']) => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key'), 'type' => 'password', 'required' => true],
            ],
            $type === 'streak' => [
                ['key' => 'api_key',      'label' => $t('api_key', 'label', 'API Key'),           'type' => 'password', 'required' => true],
                ['key' => 'pipeline_key', 'label' => $t('pipeline_key', 'label', 'Pipeline Key'), 'type' => 'text'],
            ],
            in_array($type, ['sugarcrm', 'vtiger']) => [
                ['key' => 'access_token', 'label' => $t('access_token', 'label', 'Access Token'), 'type' => 'password', 'required' => true],
                ['key' => 'instance_url', 'label' => $t('instance_url', 'label', 'Instance URL'), 'type' => 'url',      'required' => true],
            ],
            $type === 'mailchimp' => [
                ['key' => 'api_key',     'label' => $t('api_key', 'label', 'API Key'),                       'type' => 'password', 'required' => true],
                ['key' => 'list_id',     'label' => $t('list_id', 'label', 'Audience ID'),                   'type' => 'text',     'required' => true],
                ['key' => 'data_center', 'label' => $t('data_center', 'label', 'Data Center (e.g. us1)'),    'type' => 'text',     'default' => 'us1'],
                ['key' => 'tags',        'label' => $t('tags', 'label', 'Tags (comma-separated)'),          'type' => 'text',     'placeholder' => 'leadhub, inbound, 2024'],
            ],
            $type === 'activecampaign' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key'),                            'type' => 'password', 'required' => true],
                ['key' => 'api_url', 'label' => $t('api_url', 'label', 'API URL'),                            'type' => 'url',      'required' => true, 'placeholder' => 'https://youraccount.api-us1.com'],
                ['key' => 'list_id', 'label' => $t('list_id', 'label', 'List ID (optional)'),                 'type' => 'text'],
                ['key' => 'tags',    'label' => $t('tags', 'label', 'Tags (comma-separated)'),                'type' => 'text',     'placeholder' => 'leadhub, inbound'],
            ],
            $type === 'klaviyo' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'Private API Key'), 'type' => 'password', 'required' => true],
                ['key' => 'list_id', 'label' => $t('list_id', 'label', 'List ID'),         'type' => 'text'],
            ],
            $type === 'brevo' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key'), 'type' => 'password', 'required' => true],
                ['key' => 'list_id', 'label' => $t('list_id', 'label', 'List ID'), 'type' => 'text'],
            ],
            $type === 'convertkit' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key (Public)'),    'type' => 'password', 'required' => true],
                ['key' => 'form_id', 'label' => $t('form_id', 'label', 'Form / Sequence ID'), 'type' => 'text',     'required' => true],
            ],
            $type === 'drip' => [
                ['key' => 'api_token',  'label' => $t('api_token', 'label', 'API Token'),    'type' => 'password', 'required' => true],
                ['key' => 'account_id', 'label' => $t('account_id', 'label', 'Account ID'), 'type' => 'text',     'required' => true],
            ],
            $type === 'getresponse' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key'),     'type' => 'password', 'required' => true],
                ['key' => 'list_id', 'label' => $t('list_id', 'label', 'Campaign ID'), 'type' => 'text'],
            ],
            $type === 'moosend' => [
                ['key' => 'api_key', 'label' => $t('api_key', 'label', 'API Key'),               'type' => 'password', 'required' => true],
                ['key' => 'list_id', 'label' => $t('list_id', 'label', 'Mailing List ID'),       'type' => 'text'],
            ],
            $type === 'mailerlite' => [
                ['key' => 'api_key',  'label' => $t('api_key', 'label', 'API Key'),    'type' => 'password', 'required' => true],
                ['key' => 'group_id', 'label' => $t('group_id', 'label', 'Group ID'), 'type' => 'text'],
            ],
            $type === 'twilio' => [
                ['key' => 'account_sid',  'label' => $t('account_sid', 'label', 'Account SID'),    'type' => 'text',     'required' => true],
                ['key' => 'auth_token',   'label' => $t('auth_token', 'label', 'Auth Token'),       'type' => 'password', 'required' => true],
                ['key' => 'from_number',  'label' => $t('from_number', 'label', 'From Number'),     'type' => 'text',     'required' => true],
                ['key' => 'sms_template', 'label' => $t('sms_template', 'label', 'SMS Template'),   'type' => 'textarea', 'placeholder' => $t('sms_template', 'placeholder', 'New lead: {{lead.first_name}} ({{lead.email}})')],
            ],
            $type === 'vonage' => [
                ['key' => 'api_key',      'label' => $t('api_key', 'label', 'API Key'),         'type' => 'password', 'required' => true],
                ['key' => 'api_secret',   'label' => $t('api_secret', 'label', 'API Secret'),   'type' => 'password', 'required' => true],
                ['key' => 'from_number',  'label' => $t('from_number', 'label', 'From Number'), 'type' => 'text'],
                ['key' => 'sms_template', 'label' => $t('sms_template', 'label', 'SMS Template'), 'type' => 'textarea'],
            ],
            $type === 'intercom' => [
                ['key' => 'access_token', 'label' => $t('access_token', 'label', 'Access Token'), 'type' => 'password', 'required' => true],
            ],
            $type === 'zendesk' => [
                ['key' => 'subdomain',               'label' => $t('subdomain', 'label', 'Subdomain'),                            'type' => 'text',     'required' => true, 'placeholder' => $t('subdomain', 'placeholder', 'yourcompany')],
                ['key' => 'email',                   'label' => $t('email', 'label', 'Admin Email'),                              'type' => 'email',    'required' => true],
                ['key' => 'api_token_zendesk',       'label' => $t('api_token_zendesk', 'label', 'API Token'),                    'type' => 'password', 'required' => true],
                ['key' => 'create_ticket',           'label' => $t('create_ticket', 'label', 'Create as Ticket (vs Contact)'),    'type' => 'select',   'options' => [
                    '0' => $t('create_ticket', 'options.contact', 'Contact (End User)'),
                    '1' => $t('create_ticket', 'options.ticket', 'Ticket'),
                ], 'default' => '0'],
                ['key' => 'ticket_subject_template', 'label' => $t('ticket_subject_template', 'label', 'Ticket Subject Template'), 'type' => 'text',    'placeholder' => $t('ticket_subject_template', 'placeholder', 'New Lead: {{lead.first_name}} {{lead.last_name}}')],
            ],
            $type === 'google_sheets' => [
                ['key' => '_oauth_info',    'label' => $t('_oauth_info', 'label', 'OAuth Connection'),                                          'type' => 'oauth_button', 'provider' => 'google_sheets'],
                ['key' => 'client_id',      'label' => $t('client_id', 'label', 'OAuth Client ID'),                                             'type' => 'text',     'placeholder' => $t('client_id', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'client_secret',  'label' => $t('client_secret', 'label', 'OAuth Client Secret'),                                     'type' => 'password', 'placeholder' => $t('client_secret', 'placeholder', 'Required for OAuth flow')],
                ['key' => 'access_token',   'label' => $t('access_token', 'label', 'OAuth Access Token (auto-filled after OAuth)'),             'type' => 'password'],
                ['key' => 'spreadsheet_id', 'label' => $t('spreadsheet_id', 'label', 'Spreadsheet ID'),                                         'type' => 'text',     'required' => true],
                ['key' => 'sheet_name',     'label' => $t('sheet_name', 'label', 'Sheet Name'),                                                 'type' => 'text',     'default'  => 'Sheet1'],
            ],
            $type === 'notion' => [
                ['key' => 'api_key',          'label' => $t('api_key', 'label', 'Integration Token'),                                                  'type' => 'password', 'required' => true],
                ['key' => 'database_id',      'label' => $t('database_id', 'label', 'Database ID'),                                                    'type' => 'text',     'required' => true],
                ['key' => 'property_mapping', 'label' => $t('property_mapping', 'label', 'Property Mapping (JSON array — leave blank for defaults)'), 'type' => 'textarea',
                    'placeholder' => '[{"notion_prop":"Name","prop_type":"title","source_field":"full_name"},{"notion_prop":"Email","prop_type":"email","source_field":"email"},{"notion_prop":"Phone","prop_type":"phone_number","source_field":"phone"}]'],
            ],
            $type === 'airtable' => [
                ['key' => 'api_key',  'label' => $t('api_key', 'label', 'Personal Access Token'), 'type' => 'password', 'required' => true],
                ['key' => 'base_id',  'label' => $t('base_id', 'label', 'Base ID'),               'type' => 'text',     'required' => true],
                ['key' => 'table_id', 'label' => $t('table_id', 'label', 'Table Name'),           'type' => 'text',     'required' => true],
            ],
            default => [],
        };
    }

    public static function make(string $type, array $config = []): \App\Integrations\Contracts\IntegrationContract
    {
        $class = self::INTEGRATIONS[$type] ?? null;
        if (! $class) {
            // Translator-first: the message bubbles up through
            // Notification::title($e->getMessage()) in
            // IntegrationResource + ListIntegrations test/sync actions,
            // so a tenant-removed type surfaces in the active locale.
            throw new \InvalidArgumentException(
                (string) __('integrations_registry.exceptions.unknown_type', ['type' => $type])
            );
        }
        $connector = new $class();
        if ($config) {
            $connector->connect($config);
        }
        return $connector;
    }

    public const LABELS = [
        'zapier'          => 'Zapier',
        'n8n'             => 'n8n',
        'make'            => 'Make (Integromat)',
        'pabbly'          => 'Pabbly Connect',
        'activepieces'    => 'Activepieces',
        'workato'         => 'Workato',
        'hubspot'         => 'HubSpot CRM',
        'salesforce'      => 'Salesforce',
        'pipedrive'       => 'Pipedrive',
        'zoho_crm'        => 'Zoho CRM',
        'freshsales'      => 'Freshsales',
        'monday'          => 'Monday.com',
        'copper'          => 'Copper CRM',
        'close'           => 'Close CRM',
        'streak'          => 'Streak',
        'insightly'       => 'Insightly',
        'bitrix24'        => 'Bitrix24',
        'sugarcrm'        => 'SugarCRM',
        'vtiger'          => 'Vtiger',
        'mailchimp'       => 'Mailchimp',
        'activecampaign'  => 'ActiveCampaign',
        'klaviyo'         => 'Klaviyo',
        'brevo'           => 'Brevo (Sendinblue)',
        'convertkit'      => 'ConvertKit',
        'drip'            => 'Drip',
        'getresponse'     => 'GetResponse',
        'moosend'         => 'Moosend',
        'mailerlite'      => 'MailerLite',
        'slack'           => 'Slack',
        'microsoft_teams' => 'Microsoft Teams',
        'twilio'          => 'Twilio',
        'vonage'          => 'Vonage/Nexmo',
        'intercom'        => 'Intercom',
        'zendesk'         => 'Zendesk',
        'google_sheets'   => 'Google Sheets',
        'notion'          => 'Notion',
        'airtable'        => 'Airtable',
        'generic_webhook' => 'Generic Webhook',
        'rest_api_push'   => 'REST API Push',
    ];

    public const DESCRIPTIONS = [
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
    ];

    public const CATEGORIES = [
        'zapier'          => 'Automation',
        'n8n'             => 'Automation',
        'make'            => 'Automation',
        'pabbly'          => 'Automation',
        'activepieces'    => 'Automation',
        'workato'         => 'Automation',
        'hubspot'         => 'CRM',
        'salesforce'      => 'CRM',
        'pipedrive'       => 'CRM',
        'zoho_crm'        => 'CRM',
        'freshsales'      => 'CRM',
        'monday'          => 'CRM',
        'copper'          => 'CRM',
        'close'           => 'CRM',
        'streak'          => 'CRM',
        'insightly'       => 'CRM',
        'bitrix24'        => 'CRM',
        'sugarcrm'        => 'CRM',
        'vtiger'          => 'CRM',
        'mailchimp'       => 'Email Marketing',
        'activecampaign'  => 'Email Marketing',
        'klaviyo'         => 'Email Marketing',
        'brevo'           => 'Email Marketing',
        'convertkit'      => 'Email Marketing',
        'drip'            => 'Email Marketing',
        'getresponse'     => 'Email Marketing',
        'moosend'         => 'Email Marketing',
        'mailerlite'      => 'Email Marketing',
        'slack'           => 'Communication',
        'microsoft_teams' => 'Communication',
        'twilio'          => 'Communication',
        'vonage'          => 'Communication',
        'intercom'        => 'Communication',
        'zendesk'         => 'Communication',
        'google_sheets'   => 'Data & Productivity',
        'notion'          => 'Data & Productivity',
        'airtable'        => 'Data & Productivity',
        'generic_webhook' => 'Data & Productivity',
        'rest_api_push'   => 'Data & Productivity',
    ];

    /**
     * Reverse map from canonical English category name to the snake_case
     * translation key. Used by getCategoryLabel() so the display layer
     * can resolve a translation key from the existing English const
     * without forcing CATEGORIES to change shape.
     */
    public const CATEGORY_SLUGS = [
        'Automation'          => 'automation',
        'CRM'                 => 'crm',
        'Email Marketing'     => 'email_marketing',
        'Communication'       => 'communication',
        'Data & Productivity' => 'data_and_productivity',
        'Other'               => 'other',
    ];
}
