<?php

declare(strict_types=1);

/**
 * Display strings for inbound lead-source connectors
 * (App\Services\LeadSources\*Connector).
 *
 * Pattern: translator-first with English fallback. Each connector's
 * getConnectionFormSchema() looks up its labels under
 * `lead_sources_connectors.<connector_slug>.<field_name>` via the shared
 * BaseConnector::tx() / BaseConnector::field() helpers. When the
 * translator returns the raw key (missing entry) the English literal
 * baked into the connector source is used as the fallback.
 *
 * Brand and product names (Calendly, IMAP, JotForm, LinkedIn, Meta,
 * Telegram, TikTok, Twitter, Typeform, Viber, WhatsApp, Pinterest,
 * Snapchat, Instagram, YouTube, etc.) intentionally remain literal
 * across locales -- these are international product names operators
 * recognize. Only the descriptive parts of labels are translated.
 *
 * Connector slugs mirror App\Enums\LeadSource and the keys used in
 * LeadSourceConnectionResource. See ConnectorRegistry for the
 * slug -> class mapping.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fallback labels for unmapped fields
    |--------------------------------------------------------------------------
    | Used by LeadSourceConnectionResource when a connector's schema string
    | omits the third pipe-segment (label). The previous fallback was an
    | inline ucwords(str_replace('_', ' ', $fieldName)) call; the resource
    | now goes through the translator first so even unfixed connectors
    | render translatable labels.
    */
    'fallback_label' => ':label',

    'calendly' => [
        'personal_access_token' => 'Personal Access Token',
        'webhook_signing_key'   => 'Webhook Signing Key',
        'organization_uri'      => 'Organization URI',
    ],

    'email_imap' => [
        'host'     => 'IMAP Host',
        'port'     => 'IMAP Port (993 for SSL)',
        'username' => 'Email Username',
        'password' => 'Email Password',
        'ssl'      => 'Use SSL',
    ],

    'google_ads' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'Customer ID',
        'webhook_secret'  => 'Webhook Secret',
    ],

    'instagram' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'page_access_token'      => 'Page Access Token',
        'qualification_keywords' => 'Qualification Keywords (comma-separated)',
    ],

    'jotform' => [
        'api_key'       => 'API Key',
        'form_id'       => 'Form ID',
        'field_mapping' => 'Field Mapping (field_name => lead_field)',
    ],

    'linkedin' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'access_token'    => 'Access Token',
        'organization_id' => 'Organization ID',
    ],

    'meta' => [
        'app_id'            => 'App ID',
        'app_secret'        => 'App Secret',
        'page_access_token' => 'Page Access Token (auto-filled via OAuth)',
        'page_id'           => 'Page ID',
    ],

    'microsoft_ads' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'developer_token' => 'Developer Token',
        'account_id'      => 'Account ID',
        'webhook_secret'  => 'Webhook Secret',
    ],

    'pinterest' => [
        'app_id'        => 'App ID',
        'app_secret'    => 'App Secret',
        'access_token'  => 'Access Token',
        'ad_account_id' => 'Ad Account ID',
    ],

    'snapchat' => [
        'client_id'     => 'Client ID',
        'client_secret' => 'Client Secret',
        'app_secret'    => 'App Secret (for signature verification)',
        'access_token'  => 'Access Token',
    ],

    'telegram' => [
        'bot_token'              => 'Bot Token',
        'webhook_secret'         => 'Webhook Secret',
        'qualification_keywords' => 'Qualification Keywords',
    ],

    'tiktok' => [
        'client_key'    => 'Client Key (App ID)',
        'client_secret' => 'Client Secret (App Secret)',
        'access_token'  => 'Access Token (auto-filled via OAuth)',
    ],

    'twitter' => [
        'consumer_key'    => 'Consumer Key (API Key)',
        'consumer_secret' => 'Consumer Secret',
        'access_token'    => 'Access Token',
        'access_secret'   => 'Access Token Secret',
    ],

    'typeform' => [
        'access_token'   => 'Access Token',
        'webhook_secret' => 'Webhook Secret',
        'field_mapping'  => 'Field Mapping (field_ref => lead_field)',
    ],

    'viber' => [
        'auth_token'             => 'Auth Token',
        'bot_name'               => 'Bot Name',
        'qualification_keywords' => 'Qualification Keywords',
    ],

    'whatsapp' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'phone_number_id'        => 'Phone Number ID',
        'access_token'           => 'Access Token',
        'qualification_keywords' => 'Qualification Keywords',
    ],

    'youtube' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'Customer ID',
        'channel_id'      => 'YouTube Channel ID',
        'webhook_secret'  => 'Webhook Secret',
    ],

];
