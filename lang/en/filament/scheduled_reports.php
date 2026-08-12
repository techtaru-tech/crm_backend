<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — ScheduledReportResource translation strings
|--------------------------------------------------------------------------
|
| Labels, placeholders, helper text and action copy for the Scheduled
| Reports resource at /admin/scheduled-reports.
| Consumed via __('filament/scheduled_reports.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Scheduled Reports',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Scheduled Report',
    'plural_model_label'                => 'Scheduled Reports',

    // ─── Form fields & columns ─────────────────────────────────────────
    'name'                              => 'Name',
    'email'                             => 'Email',
    'col_report_type'                   => 'Report Type',
    'col_frequency'                     => 'Frequency',

    // ─── Form: settings ────────────────────────────────────────────────
    'name_placeholder'                  => 'e.g. Weekly Lead Summary',
    'report_type'                       => 'Report Type',
    'delivery_frequency'                => 'Delivery Frequency',
    'file_format'                       => 'File Format',
    'csv_spreadsheet'                   => 'CSV Spreadsheet',
    'active'                            => 'Active',

    // ─── Form: recipients ──────────────────────────────────────────────
    'recipients_description'            => 'Enter one email address per row.',
    'email_addresses'                   => 'Email Addresses',
    'email_placeholder'                 => 'name@example.com',
    'add_recipient'                     => 'Add recipient',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_frequency'            => 'Frequency',

    // ─── Table columns ─────────────────────────────────────────────────
    'frequency_daily'                   => 'Daily',
    'frequency_weekly'                  => 'Weekly',
    'frequency_monthly'                 => 'Monthly',
    'recipients'                        => 'Recipients',
    'recipients_count_suffix'           => ' recipient(s)',
    'last_sent'                         => 'Last Sent',
    'next_due'                          => 'Next Due',
];
