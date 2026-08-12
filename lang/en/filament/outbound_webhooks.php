<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — OutboundWebhookResource translation strings
|--------------------------------------------------------------------------
|
| Labels, helper text, placeholders, action copy and delivery log copy
| for the Outbound Webhooks resource at /admin/outbound-webhooks.
| Consumed via __('filament/outbound_webhooks.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Outbound Webhooks',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Outbound Webhook',
    'plural_model_label'                => 'Outbound Webhooks',

    // ─── Table columns ─────────────────────────────────────────────────
    'col_name'                          => 'Name',
    'col_url'                           => 'URL',
    'col_event'                         => 'Event',
    'col_events'                        => 'Events',
    'col_enabled'                       => 'Enabled',
    'col_created'                       => 'Created',
    'col_status'                        => 'Status',

    // ─── Form: webhook configuration ───────────────────────────────────
    'webhook_name'                      => 'Webhook Name',
    'webhook_name_placeholder'          => 'e.g. Notify Slack on new lead',
    'endpoint_url'                      => 'Endpoint URL',
    'endpoint_url_placeholder'          => 'https://your-endpoint.com/webhook',
    'trigger_events'                    => 'Trigger Events',
    'signing_secret'                    => 'Signing Secret',
    'signing_secret_helper'             => 'Used for HMAC-SHA256 signature. Auto-generated if left empty.',
    'payload_filters'                   => 'Payload Filters (optional)',
    'payload_filters_helper'            => 'JSON object to filter which events trigger this webhook. Only fires when ALL filters match. Leave empty to receive all events. Supported keys: source, status, pipeline_id, pipeline_stage_id, assigned_user_id, tags. Tags filter fires when the lead has at least one matching tag. Example: {"source":["facebook","api"],"status":["new"],"tags":["Hot Lead"]}',
    'payload_filters_placeholder'       => '{"source":["facebook"],"status":["new"]}',
    'enabled'                           => 'Enabled',

    // ─── Table columns ─────────────────────────────────────────────────
    'deliveries'                        => 'Deliveries',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_send_test'                  => 'Send Test',
    'action_view_deliveries'            => 'View Deliveries',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'No webhooks yet',
    'empty_description'                 => 'Add an outbound webhook to receive real-time event notifications.',

    // ─── Delivery Log page ─────────────────────────────────────────────
    'delivery_log_title_prefix'         => 'Delivery Log: ',
    'action_back_to_webhooks'           => 'Back to Webhooks',
    'col_http'                          => 'HTTP',
    'col_latency'                       => 'Latency',
    'col_attempts'                      => 'Attempts',
    'col_sent'                          => 'Sent',
    'action_payload'                    => 'Payload',
    'sent_payload_modal_prefix'         => 'Sent Payload — ',
    'modal_close'                       => 'Close',
    'action_response'                   => 'Response',
    'response_body_modal_prefix'        => 'Response Body — HTTP ',
    'no_response_body'                  => 'No response body recorded.',
    'action_retry'                      => 'Retry',
    'log_empty_heading'                 => 'No deliveries yet',
    'log_empty_description'             => 'Test deliveries and live events will appear here once triggered.',
    'new_webhook'                       => 'New Webhook',

    // ─── Delivery log: stats cards ─────────────────────────────────────
    'webhook_title_prefix'              => 'Webhook: :name',
    'stat_total_deliveries'             => 'Total Deliveries',
    'stat_successful'                   => 'Successful',
    'stat_failed'                       => 'Failed',

    // ─── Test delivery payload body ────────────────────────────────────
    'test_delivery_body'                => 'This is a test delivery from LeadHub.',

    // ─── Delivery status badges ────────────────────────────────────────
    'status_success'                    => 'Success',
    'status_failed'                     => 'Failed',
    'status_retrying'                   => 'Retrying',

    // ─── Latency suffix ────────────────────────────────────────────────
    'latency_ms_suffix'                 => 'ms',

    // ─── Event badge labels ────────────────────────────────────────────
    'event_test'                        => 'Test',
    'event_lead_created'                => 'Lead Created',
    'event_lead_updated'                => 'Lead Updated',
    'event_lead_deleted'                => 'Lead Deleted',
    'event_lead_stage_changed'          => 'Lead Stage Changed',
    'event_form_submitted'              => 'Form Submitted',
    'event_automation_triggered'        => 'Automation Triggered',
];
