<?php

/**
 * Display strings for the integration sync-logs Blade view at
 * resources/views/filament/resources/integration-sync-logs.blade.php
 * and the HTTP-error prefix written by App\Jobs\SyncLeadToIntegration
 * into the `integration_sync_logs.error_message` column.
 *
 * Pattern: translator-first with English fallback. The Blade view uses
 * Lang::has() before __() so missing translation keys fall through to
 * a sensible English literal (e.g. ucfirst($log['status'])) without
 * surprising the operator.
 *
 * - `status.*` backs the colored status pill rendered in the sync-log
 *   table. Sub-keys mirror the IntegrationSyncLog status constants
 *   (App\Models\IntegrationSyncLog::STATUS_SUCCESS / STATUS_FAILED /
 *   STATUS_PENDING / STATUS_RETRYING). The Blade view passes the raw
 *   string status to Lang::has(), so any future status value falls
 *   back to PHP's ucfirst() until a translation is added.
 * - `error_default` is rendered when a sync-log row has a non-null
 *   `error_message` column but the value is empty/whitespace -- this
 *   gives operators a non-blank placeholder instead of an empty red
 *   error box.
 * - `http_error_prefix` is used by App\Jobs\SyncLeadToIntegration
 *   when a connector pushLead() succeeded at the transport layer but
 *   returned an HTTP >= 400 status code. The translated string is
 *   stored in `integration_sync_logs.error_message` and surfaces in
 *   IntegrationSyncFailedNotification email bodies.
 */

return [
    'status' => [
        'success'  => 'Success',
        'failed'   => 'Failed',
        'pending'  => 'Pending',
        'retrying' => 'Retrying',
    ],

    // Localised event labels for the sync-log table's "Event" column.
    // Keys mirror the slugs dispatched by App\Services\IntegrationDispatcher
    // (lead_created / lead_updated). The Blade view passes the raw slug to
    // Lang::has() before __() so any future event slug falls through to a
    // ucfirst() humanisation instead of crashing.
    'event' => [
        'lead_created' => 'Lead created',
        'lead_updated' => 'Lead updated',
    ],

    'error_default'     => '(no error details)',
    'http_error_prefix' => 'HTTP :status: :body',
];
