<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — RecurringInvoiceResource translation strings
|--------------------------------------------------------------------------
|
| Tenant "Recurring Invoices / Dues" CRUD. Consumed via
| __('filament/recurring_invoices.<key>').
|
*/

return [

    'nav_label'           => 'Recurring Invoices',
    'model_label'         => 'Recurring Invoice',
    'plural_model_label'  => 'Recurring Invoices',

    // Form
    'section_schedule'      => 'Recurring Schedule',
    'section_schedule_desc' => 'A standing monthly or annual charge. LeadHub creates a real invoice on each run date and reminds you when it falls due.',
    'field_lead'            => 'Member / Customer',
    'field_company'         => 'Company (optional)',
    'field_title'           => 'Description',
    'field_amount'          => 'Amount',
    'field_currency'        => 'Currency',
    'field_interval'        => 'Bills every',
    'interval_month'        => 'Month',
    'interval_year'         => 'Year',
    'field_anchor_day'      => 'Billing day of month',
    'field_anchor_day_help' => 'Optional. 1-28. The next run date snaps to this day each period.',
    'field_next_run_date'   => 'Next run date',
    'field_due_days'        => 'Due after (days)',
    'field_due_days_help'   => 'Days after each invoice is generated that it becomes due.',
    'field_auto_send'       => 'Auto-send each invoice',
    'field_auto_send_help'  => 'Mark each generated invoice as Sent instead of Draft.',
    'field_active'          => 'Active',
    'field_notes'           => 'Notes',

    // Table
    'col_title'    => 'Description',
    'col_member'   => 'Member',
    'col_amount'   => 'Amount',
    'col_interval' => 'Interval',
    'col_next_run' => 'Next run',
    'col_active'   => 'Active',
];
