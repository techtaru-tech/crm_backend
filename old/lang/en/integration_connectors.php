<?php

/**
 * Display strings for App\Integrations\Connectors\* (outbound CRM, email
 * marketing, helpdesk, and automation connectors) and the
 * App\Integrations\BaseConnector default field map.
 *
 * Pattern: translator-first with English fallback. Each connector's
 * `getAvailableFields()` method returns an associative array of API
 * field keys mapped to human-readable labels rendered in the
 * field-mapping UI at
 * resources/views/filament/resources/integration-resource/pages/list-integrations.blade.php
 * (`<option>{{ $fieldLabel }}</option>`). This file backs the DISPLAY
 * layer so the operator-facing label switches with the active locale
 * while the BaseConnector's English literals stay the canonical source
 * of truth for missing-translation fallback.
 *
 * Brand and product names (HubSpot, Salesforce, Zoho, Pipedrive,
 * Zendesk, Mailchimp, Freshsales, ActiveCampaign, Vtiger, Notion,
 * Microsoft Teams, etc.) intentionally remain literal across locales --
 * these are international product names operators recognize. Field key
 * suffixes that mirror an API type (e.g. Notion's "(title)", "(email)",
 * "(rich_text)") also stay literal because they identify the upstream
 * property type, not a translatable concept.
 *
 * Namespacing: keys are grouped by connector slug, matching the slugs
 * used in App\Integrations\IntegrationRegistry::INTEGRATIONS (e.g.
 * 'hubspot', 'zoho_crm', 'activecampaign'). The 'default' bucket backs
 * App\Integrations\BaseConnector::getAvailableFields() for any
 * connector that does not override it.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default field labels (App\Integrations\BaseConnector)
    |--------------------------------------------------------------------------
    | Used when a connector does not override getAvailableFields(). The
    | English values mirror the BaseConnector::getAvailableFields() return
    | array so a missing translation falls back to the literal English
    | string without surprising the operator.
    */
    'default' => [
        'first_name' => 'First Name',
        'last_name'  => 'Last Name',
        'email'      => 'Email',
        'phone'      => 'Phone',
        'company'    => 'Company',
        'source'     => 'Lead Source',
        'status'     => 'Status',
        'lead_score' => 'Lead Score',
        'address'    => 'Address',
        'city'       => 'City',
        'country'    => 'Country',
        'notes'      => 'Notes',
    ],

    /*
    |--------------------------------------------------------------------------
    | ActiveCampaign (App\Integrations\Connectors\ActiveCampaignConnector)
    |--------------------------------------------------------------------------
    | Contact-object fields used by the ActiveCampaign /api/3/contacts
    | endpoint. The brand name ActiveCampaign stays literal.
    */
    'activecampaign' => [
        'email'     => 'Email',
        'firstName' => 'First Name',
        'lastName'  => 'Last Name',
        'phone'     => 'Phone',
        'orgname'   => 'Organization Name',
    ],

    /*
    |--------------------------------------------------------------------------
    | Freshsales (App\Integrations\Connectors\FreshsalesConnector)
    |--------------------------------------------------------------------------
    | Contact-object fields used by the Freshsales /api/contacts endpoint.
    */
    'freshsales' => [
        'first_name'    => 'First Name',
        'last_name'     => 'Last Name',
        'email'         => 'Email',
        'mobile_number' => 'Mobile Number',
        'work_number'   => 'Work Number',
        'job_title'     => 'Job Title',
        'company'       => 'Company',
        'city'          => 'City',
        'country'       => 'Country',
    ],

    /*
    |--------------------------------------------------------------------------
    | HubSpot (App\Integrations\Connectors\HubSpotConnector)
    |--------------------------------------------------------------------------
    | Static fallback labels used by HubSpotConnector::staticFields() when
    | the live CRM property describe call is unavailable. HubSpot's own
    | API-supplied property labels override these for connected accounts.
    */
    'hubspot' => [
        'email'     => 'Email',
        'firstname' => 'First Name',
        'lastname'  => 'Last Name',
        'phone'     => 'Phone',
        'company'   => 'Company',
        'website'   => 'Website',
        'address'   => 'Address',
        'city'      => 'City',
        'country'   => 'Country',
        'jobtitle'  => 'Job Title',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mailchimp (App\Integrations\Connectors\MailchimpConnector)
    |--------------------------------------------------------------------------
    | Mailchimp merge-tag identifiers (FNAME, LNAME, PHONE, COMPANY) stay
    | literal as array keys; only the human-readable labels translate.
    */
    'mailchimp' => [
        'first_name' => 'First Name',
        'last_name'  => 'Last Name',
        'phone'      => 'Phone',
        'company'    => 'Company',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notion (App\Integrations\Connectors\NotionConnector)
    |--------------------------------------------------------------------------
    | Static fallback labels used by NotionConnector::staticFields(). The
    | type suffix in parentheses ("title", "email", "phone_number",
    | "rich_text") identifies the Notion property type and stays literal
    | across locales -- it is an API identifier, not a translatable noun.
    */
    'notion' => [
        'name'    => 'Name (title)',
        'email'   => 'Email (email)',
        'phone'   => 'Phone (phone_number)',
        'source'  => 'Source (rich_text)',
        'status'  => 'Status (rich_text)',
        'company' => 'Company (rich_text)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pipedrive (App\Integrations\Connectors\PipedriveConnector)
    |--------------------------------------------------------------------------
    | Person-object fields used by the Pipedrive v1 /persons endpoint.
    */
    'pipedrive' => [
        'name'     => 'Name',
        'email'    => 'Email',
        'phone'    => 'Phone',
        'org_name' => 'Organization',
        // Deal title prefix sent to Pipedrive when PipedriveConnector
        // auto-creates a Lead/Deal alongside the Person record. Visible
        // to the operator inside their Pipedrive workspace, so it
        // respects the operator's UI locale instead of leaking English.
        'deal_title' => 'Lead: :name',
    ],

    /*
    |--------------------------------------------------------------------------
    | Salesforce (App\Integrations\Connectors\SalesforceConnector)
    |--------------------------------------------------------------------------
    | Static fallback labels used by SalesforceConnector::staticFields()
    | when the live sObject describe call is unavailable. Salesforce's
    | own metadata supplies the rendered labels for connected accounts.
    | Translation keys use snake_case; the connector keeps Salesforce's
    | PascalCase API field names (FirstName, LastName, LeadSource, etc.)
    | as the array keys returned to the field-mapping UI.
    */
    'salesforce' => [
        'email'        => 'Email',
        'first_name'   => 'First Name',
        'last_name'    => 'Last Name',
        'phone'        => 'Phone',
        'company'      => 'Company',
        'lead_source'  => 'Lead Source',
        'status'       => 'Status',
        'mobile_phone' => 'Mobile Phone',
        'website'      => 'Website',
        'city'         => 'City',
        'country'      => 'Country',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vtiger (App\Integrations\Connectors\VtigerConnector)
    |--------------------------------------------------------------------------
    | Lead-module fields used by the Vtiger webservice.php endpoint.
    */
    'vtiger' => [
        'firstname'  => 'First Name',
        'lastname'   => 'Last Name',
        'email'      => 'Email',
        'phone'      => 'Phone',
        'company'    => 'Company',
        'leadsource' => 'Lead Source',
    ],

    /*
    |--------------------------------------------------------------------------
    | Zendesk (App\Integrations\Connectors\ZendeskConnector)
    |--------------------------------------------------------------------------
    | User-object fields used by the Zendesk /api/v2/users endpoint when
    | a lead is pushed as a Zendesk end-user. "External ID" refers to
    | the Zendesk external_id property used for cross-system reference.
    */
    'zendesk' => [
        'name'         => 'Full Name',
        'email'        => 'Email',
        'phone'        => 'Phone',
        'organization' => 'Organization',
        'external_id'  => 'External ID',
        'details'      => 'Details',
        'notes'        => 'Notes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Zoho CRM (App\Integrations\Connectors\ZohoCRMConnector)
    |--------------------------------------------------------------------------
    | Lead-module fields used by the Zoho CRM v3 /Leads endpoint. The
    | brand name Zoho stays literal. Translation keys use snake_case;
    | the connector keeps Zoho's Pascal_Snake_Case API field names
    | (First_Name, Last_Name, Lead_Source, etc.) as the array keys
    | returned to the field-mapping UI.
    */
    'zoho_crm' => [
        'first_name'  => 'First Name',
        'last_name'   => 'Last Name',
        'email'       => 'Email',
        'phone'       => 'Phone',
        'company'     => 'Company',
        'lead_source' => 'Lead Source',
        'city'        => 'City',
        'country'     => 'Country',
    ],

    /*
    |--------------------------------------------------------------------------
    | Connector "not configured" RuntimeException messages
    |--------------------------------------------------------------------------
    | Operator-facing exception messages thrown by App\Integrations\Connectors\*
    | when a required configuration value (webhook URL, list ID, board ID,
    | spreadsheet ID, etc.) is missing on a Lead push. These strings end up
    | in `integration_sync_logs.error_message` (rendered in the admin
    | sync-logs Blade view) and in IntegrationSyncFailedNotification email
    | bodies, so they must localize cleanly without leaking raw English.
    |
    | Brand and product names (ConvertKit, Activepieces, Google Sheets,
    | Airtable, GetResponse, Make, Monday.com, n8n, Microsoft Teams,
    | Pabbly, Notion, Streak, Slack, Mailchimp, Moosend, Workato, Zapier)
    | stay literal across locales -- they are international product names
    | operators recognize. Keys are namespaced by connector slug matching
    | App\Integrations\IntegrationRegistry::INTEGRATIONS.
    */
    'not_configured' => [
        'convertkit'      => 'ConvertKit form ID not configured.',
        'activepieces'    => 'Activepieces webhook URL not configured.',
        'google_sheets'   => 'Google Sheets spreadsheet ID not configured.',
        'airtable'        => 'Airtable base/table ID not configured.',
        'getresponse'     => 'GetResponse campaign ID not configured.',
        'generic_webhook' => 'Webhook URL not configured.',
        'make'            => 'Make webhook URL not configured.',
        'monday'          => 'Monday.com board ID not configured.',
        'n8n'             => 'n8n webhook URL not configured.',
        'microsoft_teams' => 'Microsoft Teams webhook URL not configured.',
        'pabbly'          => 'Pabbly webhook URL not configured.',
        'notion'          => 'Notion database ID not configured.',
        'streak'          => 'Streak pipeline key not configured.',
        'slack'           => 'Slack webhook URL not configured.',
        'mailchimp'       => 'Mailchimp list ID not configured.',
        'moosend'         => 'Moosend list ID not configured.',
        'workato'         => 'Workato webhook URL not configured.',
        'zapier'          => 'Zapier webhook URL is not configured.',
    ],
];
