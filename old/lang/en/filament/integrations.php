<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — IntegrationResource translation strings
|--------------------------------------------------------------------------
|
| Labels, placeholders, helper text, and action copy for the Integrations
| resource at /admin/integrations.
| Consumed via __('filament/integrations.<key>').
|
| Keys are snake_case; grouped by comment headers (Form, Config, Mapping,
| Filters, Table, Actions, Bulk actions, Notifications).
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Integrations',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Integration',
    'plural_model_label'                => 'Integrations',

    // ─── Table column labels ──────────────────────────────────────────
    'col_status'                        => 'Status',

    // ─── Form: integration setup ───────────────────────────────────────
    'integration_type'                  => 'Integration Type',
    'display_name'                      => 'Display Name',
    'enable_this_integration'           => 'Enable this integration',

    // ─── Form: connection configuration ────────────────────────────────
    'webhook_url'                       => 'Webhook URL',
    'webhook_url_placeholder'           => 'https://...',
    'api_key'                           => 'API Key',
    'access_token_oauth'                => 'Access Token / OAuth Token',
    'domain_subdomain'                  => 'Domain / Subdomain',
    'domain_placeholder'                => 'yourcompany',
    'instance_url'                      => 'Instance URL',
    'instance_url_placeholder'          => 'https://yourinstance.salesforce.com',
    'list_audience_id'                  => 'List / Audience ID',
    'board_id'                          => 'Board ID',
    'spreadsheet_id'                    => 'Spreadsheet ID',
    'sheet_name'                        => 'Sheet Name',
    'notion_database_id'                => 'Notion Database ID',
    'airtable_base_id'                  => 'Airtable Base ID',
    'airtable_table_name'               => 'Airtable Table Name',
    'account_sid'                       => 'Account SID',
    'auth_token'                        => 'Auth Token',
    'from_number'                       => 'From Number',
    'api_secret'                        => 'API Secret',
    'user_email'                        => 'User Email',
    'streak_pipeline_key'               => 'Streak Pipeline Key',
    'activecampaign_api_url'            => 'ActiveCampaign API URL',
    'activecampaign_api_url_placeholder' => 'https://youracccount.api-us1.com',
    'convertkit_form_id'                => 'ConvertKit Form / Sequence ID',
    'drip_account_id'                   => 'Drip Account ID',
    'api_token'                         => 'API Token',
    'mailerlite_group_id'               => 'MailerLite Group ID',
    'mailchimp_data_center'             => 'Mailchimp Data Center (e.g. us1)',
    'zendesk_subdomain'                 => 'Zendesk Subdomain',
    'zendesk_admin_email'               => 'Zendesk Admin Email',
    'zendesk_api_token'                 => 'Zendesk API Token',
    'salesforce_object_type'            => 'Salesforce Object Type',
    'salesforce_object_lead'            => 'Lead',
    'salesforce_object_contact'         => 'Contact',
    'sms_template'                      => 'SMS Template',
    'sms_template_placeholder'          => 'New lead: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    'slack_message_template'            => 'Slack Message Template',
    'slack_message_template_placeholder' => 'Use {{lead.first_name}}, {{lead.email}}, etc.',
    'auth_type'                         => 'Auth Type',
    'auth_type_none'                    => 'None',
    'auth_type_bearer'                  => 'Bearer Token',
    'auth_type_api_key'                 => 'API Key Header',
    'auth_type_basic'                   => 'Basic Auth',
    'auth_value'                        => 'Auth Value (Token/Key/Credentials)',
    'json_body_template'                => 'JSON Body Template',
    'json_body_template_placeholder'    => '{"name": "{{lead.first_name}} {{lead.last_name}}", "email": "{{lead.email}}"}',

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_label'               => 'Map LeadHub fields to target fields',
    'leadhub_field'                     => 'LeadHub Field',
    'target_field_name'                 => 'Target Field Name',
    'target_field_placeholder'          => 'e.g. email, FIRST_NAME, properties.email',
    'add_field_mapping'                 => 'Add field mapping',
    'lh_field_first_name'               => 'First Name',
    'lh_field_last_name'                => 'Last Name',
    'lh_field_email'                    => 'Email',
    'lh_field_phone'                    => 'Phone',
    'lh_field_company'                  => 'Company',
    'lh_field_source'                   => 'Lead Source',
    'lh_field_status'                   => 'Status',
    'lh_field_lead_score'               => 'Lead Score',
    'lh_field_address'                  => 'Address',
    'lh_field_city'                     => 'City',
    'lh_field_country'                  => 'Country',
    'lh_field_notes'                    => 'Notes',

    // ─── Form: filter leads ────────────────────────────────────────────
    'filter_sources'                    => 'Only sync from these sources (leave blank for all)',
    'filter_tags'                       => 'Only sync leads with these tags',

    // ─── Table columns ─────────────────────────────────────────────────
    'name'                              => 'Name',
    'category'                          => 'Category',
    'last_sync'                         => 'Last Sync',
    'last_sync_never'                   => 'Never',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_status'               => 'Status',
    'status_connected'                  => 'Connected',
    'status_disconnected'               => 'Disconnected',
    'status_error'                      => 'Error / Broken',
    'enabled'                           => 'Enabled',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_test'                       => 'Test',
    'action_sync_logs'                  => 'Sync Logs',
    'connection_successful'             => 'Connection successful!',
    'connection_failed'                 => 'Connection failed. Check your credentials.',
    'error_prefix'                      => 'Error: ',

    // ─── Bulk actions ──────────────────────────────────────────────────
    'bulk_enable'                       => 'Enable Selected',
    'bulk_disable'                      => 'Disable Selected',

    // ─── ListIntegrations notifications ────────────────────────────────
    'notify_saved'                      => 'Integration saved.',
    'notify_enabled'                    => 'Integration enabled.',
    'notify_disabled'                   => 'Integration disabled.',
    'notify_removed'                    => 'Integration removed.',

    // ─── Sync logs header actions ──────────────────────────────────────
    'retry_all_failed'                  => 'Retry All Failed',
    'back_to_integrations'              => 'Back to Integrations',
    'retrying_failed_syncs'             => 'Retrying :count failed sync(s).',

    // ─── List page: modal + cards ──────────────────────────────────────
    'connection_settings_heading'       => 'Connection Settings',
    'modal_cancel'                      => 'Cancel',
    'modal_save_integration'            => 'Save Integration',
    'confirm_remove_integration'        => 'Remove this integration?',

    // ─── Component: integration-config-notice ──────────────────────────
    'config_notice_note_label'          => 'Note:',
    'config_notice_body'                => 'Credentials are encrypted at rest. OAuth integrations (HubSpot, Salesforce, Zoho, Google Sheets) require an access token from your OAuth flow. Paste it in the Access Token field above.',

    // ─── List page: status pills ───────────────────────────────────────
    'status_connected_label'            => 'Connected',
    'status_error_label'                => 'Error',
    'status_inactive_label'             => 'Inactive',
    'action_test_connection'            => 'Test connection',
    'action_configure'                  => 'Configure',
    'action_sync_logs_title'            => 'Sync logs',
    'action_remove'                     => 'Remove',
    'action_disable'                    => 'Disable',
    'action_enable'                     => 'Enable',
    'last_sync_prefix'                  => 'Last sync: :time',
    'btn_connect'                       => '+ Connect',
    'setup_title_prefix'                => 'Setup: :type',

    // ─── List page: OAuth config ───────────────────────────────────────
    'oauth_connected_pill'              => 'Connected via OAuth',
    'oauth_reconnect'                   => 'Reconnect via OAuth',
    'oauth_connect'                     => 'Connect via OAuth',
    'oauth_hint'                        => 'Enter your Client ID & Secret below first, then click to authorize.',

    // ─── List page: field mapping ──────────────────────────────────────
    'field_mapping_heading'             => 'Field Mapping',
    'field_optional_suffix'             => '(optional)',
    'field_mapping_desc'                => 'Map :app fields to target field names in the connected app.',
    'select_source_field'               => ':app Field',
    'select_target_field'               => 'Select target field',
    'target_field_input_placeholder'    => 'Target field name',
    'btn_add_mapping'                   => '+ Add mapping',

    // ─── List page: source filter ──────────────────────────────────────
    'source_filter_heading'             => 'Source Filter',
    'source_filter_desc'                => 'Only sync leads from these sources (leave empty to sync all).',

    // ─── List page: tag filter ─────────────────────────────────────────
    'tag_filter_heading'                => 'Tag Filter',
    'tag_filter_desc'                   => 'Only sync leads that have at least one of these tags (leave empty to sync all).',
    'no_tags_created'                   => 'No tags created yet.',

    // ─── List page: pipeline filter ────────────────────────────────────
    'pipeline_filter_heading'           => 'Pipeline Filter',
    'pipeline_filter_desc'              => 'Only sync leads currently in these pipelines (leave empty to sync all).',
    'no_pipelines_created'              => 'No pipelines created yet.',

    // ─── Sync logs page ────────────────────────────────────────────────
    'sync_logs_heading'                 => 'Sync Logs — :name',
    'sync_logs_total'                   => ':count total',
    'no_sync_logs'                      => 'No sync logs yet. Leads will be synced here once the integration is triggered.',
    'col_lead'                          => 'Lead',
    'col_event'                         => 'Event',
    'col_status'                        => 'Status',
    'col_attempts'                      => 'Attempts',
    'col_time'                          => 'Time',
    'col_details'                       => 'Details',
    'btn_show'                          => 'Show',
    'btn_hide'                          => 'Hide',
    'detail_payload'                    => 'Payload',
    'detail_response'                   => 'Response',
];
