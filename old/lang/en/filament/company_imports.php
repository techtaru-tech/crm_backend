<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — CompanyImportResource translation strings
|--------------------------------------------------------------------------
|
| Column labels + wizard copy for the Company Imports resource at
| /admin/company-imports. Consumed via __('filament/company_imports.<key>').
| Mirrors filament/lead_imports.php with Company-appropriate wording.
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Company Imports',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Company Import',
    'plural_model_label'                => 'Company Imports',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'Status',
    'created_at'                        => 'Created',
    'file'                              => 'File',
    'total'                             => 'Total',
    'imported'                          => 'Imported',
    'dupes'                             => 'Dupes',
    'errors'                            => 'Errors',
    'imported_by'                       => 'Imported By',

    // ─── Upload step ──────────────────────────────────────────────────
    'csv_or_excel'                      => 'CSV or Excel File',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_name'                        => 'Company Name',
    'field_domain'                      => 'Domain',
    'field_industry'                    => 'Industry',
    'field_size'                        => 'Size',
    'field_website'                     => 'Website',
    'field_phone'                       => 'Phone',
    'field_address'                     => 'Address',
    'field_city'                        => 'City',
    'field_country'                     => 'Country',
    'field_notes'                       => 'Notes',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'Could not read file: ',
    'notif_queued_title'                => 'Import queued! Companies will be added shortly.',
    'import_csv_excel'                  => 'Import CSV/Excel',

    // ─── Job errors (persisted to company_imports.errors JSON) ────────
    'job_row_cap_exceeded'              => 'Import exceeds the :max row cap (file has :rows rows). Please split the file into smaller imports.',

    // ─── Wizard ───────────────────────────────────────────────────────
    'step_1_heading'                    => 'Step 1: Upload File',
    'upload_and_preview'                => 'Upload & Preview Columns',
    'step_2_heading'                    => 'Step 2: Preview & Map Columns',
    'preview_paragraph'                 => ':total rows detected. Preview below shows the first 10. Map each CSV column to a company field (unmapped columns are skipped).',
    'recognized_count'                  => 'Recognized: :count',
    'unmapped_count'                    => 'Unmapped: :count',
    'option_skip'                       => '— Skip —',
    'reject_reupload'                   => 'Reject / Re-upload',
    'accept_start_import'               => 'Accept & Start Import',
    'step_3_heading'                    => 'Import Started!',
    'step_3_body_html'                  => 'Your import job has been queued. Companies will be added in the background. Check the <a href=":url" class="text-primary-600 underline">Company Imports</a> list for progress.',
];
