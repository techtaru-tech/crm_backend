<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadSourceConnectionResource translation strings
|--------------------------------------------------------------------------
|
| Labels, descriptions, helper text, placeholders and action copy for the
| Lead Sources resource at /admin/lead-source-connections.
| Consumed via __('filament/lead_source_connections.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Lead Sources',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Lead Source',
    'plural_model_label'                => 'Lead Sources',

    // ─── Form fields & column labels ───────────────────────────────────
    'source'                            => 'Source',
    'active'                            => 'Active',
    'col_name'                          => 'Name',
    'col_source'                        => 'Source',
    'col_status'                        => 'Status',

    // ─── Form: connection details ──────────────────────────────────────
    'connection_name'                   => 'Connection Name',
    'connection_name_helper'            => 'e.g., "Facebook – Main Page"',

    // ─── Form: webhook URL section ─────────────────────────────────────
    'your_webhook_url'                  => 'Your Webhook URL',
    'webhook_url_placeholder_default'   => 'Save connection first to get URL',
    'webhook_url_helper'                => "Copy this URL and paste it into the source platform's webhook settings.",

    // ─── Form: OAuth authorization section ─────────────────────────────
    'oauth_description'                 => 'Save the connection first, then click "Connect via OAuth" from the list to authorize access. Credentials (access token, refresh token) will be stored automatically.',
    'oauth_instruction_text'            => 'After saving this connection, use the "Connect via OAuth" button in the connections list to authorize. Your App ID / Client ID and App Secret / Client Secret are required before redirecting.',

    // ─── Form: API credentials section ─────────────────────────────────
    'credentials_description'           => 'Fill in the credentials required by this source. Stored encrypted.',
    'credentials_select_source'         => 'Select a source above to see required credentials.',
    'credentials_none_required'         => 'No credentials required for this source.',

    // ─── Form: message source settings ─────────────────────────────────
    'message_source_description'        => 'Configure how messages are captured as leads.',
    'qualification_keywords'            => 'Qualification Keywords',
    'qualification_keywords_helper'     => 'A lead is created when someone sends a message containing one of these words. Leave empty to capture all messages.',
    'qualification_keywords_placeholder' => 'Add keyword…',

    // ─── Form: Meta page selection ─────────────────────────────────────
    'meta_page_description'             => 'After connecting via OAuth, select which Facebook/Instagram page to use for lead retrieval.',
    'active_page'                       => 'Active Page',
    'active_page_helper'                => "This page's access token will be used to retrieve leads from the Meta Lead Ads API.",

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_description'         => 'Map source form fields to LeadHub lead fields (first_name, last_name, email, phone).',
    'field_mapping'                     => 'Field Mapping',
    'source_field_name'                 => 'Source Field Name',
    'leadhub_field_value'               => 'LeadHub Field (first_name / last_name / email / phone)',

    // ─── Table columns ─────────────────────────────────────────────────
    'leads'                             => 'Leads',
    'last_lead'                         => 'Last Lead',
    'webhook_url'                       => 'Webhook URL',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_source'               => 'Source',
    'filter_label_status'               => 'Status',
    'status_connected'                  => 'Connected',
    'status_disconnected'               => 'Disconnected',
    'status_error'                      => 'Error',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_connect_oauth'              => 'Connect via OAuth',
    'action_connect_oauth_tooltip'      => 'Authorize access via OAuth to auto-fill credentials',
    'action_test'                       => 'Test',
    'test_failed_message'               => 'Test connection failed',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'No lead sources connected',
    'empty_description'                 => 'Connect a lead source to start capturing leads.',
];
