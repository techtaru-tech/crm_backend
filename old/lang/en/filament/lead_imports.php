<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadImportResource translation strings
|--------------------------------------------------------------------------
|
| Column labels for the Lead Imports resource at /admin/lead-imports.
| Consumed via __('filament/lead_imports.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Imports',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Lead Import',
    'plural_model_label'                => 'Lead Imports',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'Status',
    'created_at'                        => 'Created',
    'file'                              => 'File',
    'total'                             => 'Total',
    'imported'                          => 'Imported',
    'dupes'                             => 'Dupes',
    'errors'                            => 'Errors',
    'imported_by'                       => 'Imported By',

    // ─── CreateLeadImport upload step ─────────────────────────────────
    'csv_or_excel'                      => 'CSV or Excel File',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_first_name'                  => 'First Name',
    'field_last_name'                   => 'Last Name',
    'field_email'                       => 'Email',
    'field_phone'                       => 'Phone',
    'field_source'                      => 'Source',
    'field_status'                      => 'Status',
    'field_notes'                       => 'Notes',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'Could not read file: ',
    'notif_queued_title'                => 'Import queued! Leads will be added shortly.',
    'import_csv_excel'                  => 'Import CSV/Excel',

    // ─── Job errors (persisted to lead_imports.errors JSON) ───────────
    'job_row_cap_exceeded'              => 'Import exceeds :max row cap (file has :rows rows). Please split the file into smaller imports.',

    // ─── CreateLeadImport wizard ──────────────────────────────────────
    'step_1_heading'                    => 'Step 1: Upload File',
    'upload_and_preview'                => 'Upload & Preview Columns',
    'step_2_heading'                    => 'Step 2: Preview & Map Columns',
    'preview_paragraph'                 => ':total rows detected. Preview below shows the first 10. Map each CSV column to a lead field (unmapped columns are skipped).',
    'recognized_count'                  => 'Recognized: :count',
    'unmapped_count'                    => 'Unmapped: :count',
    'option_skip'                       => '— Skip —',
    'reject_reupload'                   => 'Reject / Re-upload',
    'accept_start_import'               => 'Accept & Start Import',
    'step_3_heading'                    => 'Import Started!',
    'step_3_body_html'                  => 'Your import job has been queued. Leads will be added in the background. You can check the <a href=":url" class="text-primary-600 underline">Import History</a> page for progress.',
];
