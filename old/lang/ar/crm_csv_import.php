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
    'vendor_generic_csv'    => 'CSV عام',

    // ─── Error notifications ───
    'file_not_readable'     => 'تعذّر قراءة الملف: :path',
    'could_not_open'        => 'تعذّر فتح الملف',
    'no_header_row'         => 'ملف CSV لا يحتوي على صف رؤوس',
    'batch_failed_prefix'   => 'فشلت الدفعة: ',

    // ─── Lead.source seed values (DB-written) ───
    'source_brand_import'   => 'استيراد من :brand',
    'source_csv_import'     => 'استيراد من CSV',
];
