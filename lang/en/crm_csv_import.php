<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | CRM CSV Importer translation strings
 |--------------------------------------------------------------------------
 |
 | Strings used by App\Services\Migration\CrmCsvImporter — vendor labels,
 | error notifications, and DB-written Lead.source seed values. Accessed
 | via __('crm_csv_import.<key>').
 |
 */

return [

    // ─── Vendor dropdown options ───
    'vendor_generic_csv'    => 'Generic CSV',

    // ─── Error notifications ───
    'file_not_readable'     => 'File not readable: :path',
    'could_not_open'        => 'Could not open file',
    'no_header_row'         => 'CSV has no header row',
    'batch_failed_prefix'   => 'Batch failed: ',

    // ─── Lead.source seed values (DB-written) ───
    'source_brand_import'   => ':brand Import',
    'source_csv_import'     => 'CSV Import',
    'followup_title' => 'Follow-up (imported)',
];
