<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — WebhookLogResource translation strings
|--------------------------------------------------------------------------
|
| Labels, filter copy and action copy for the Webhook Logs resource at
| /admin/webhook-logs.
| Consumed via __('filament/webhook_logs.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Webhook Logs',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Webhook Log',
    'plural_model_label'                => 'Webhook Logs',

    // ─── Table column labels ──────────────────────────────────────────
    'col_source'                        => 'Source',
    'col_status'                        => 'Status',

    // ─── Form ──────────────────────────────────────────────────────────
    'raw_payload'                       => 'Raw Payload',
    'error_message'                     => 'Error Message',

    // ─── Table columns ─────────────────────────────────────────────────
    'leads'                             => 'Leads',
    'ip'                                => 'IP',
    'error'                             => 'Error',
    'received_at'                       => 'Received At',
    'processed_at'                      => 'Processed At',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_source'               => 'Source',
    'filter_label_status'               => 'Status',

    // ─── Status filters ────────────────────────────────────────────────
    'status_success'                    => 'Success',
    'status_failed'                     => 'Failed',
    'status_pending'                    => 'Pending',
    'status_invalid_signature'          => 'Invalid Signature',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_retry'                      => 'Retry',
];
