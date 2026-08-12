<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Data Privacy page — translation strings
|--------------------------------------------------------------------------
|
| Strings used by the tenant-side Data Privacy page (Filament).  Implements
| GDPR Article 15 (Right of Access) and Article 17 (Right to Erasure) UI.
|
| Buyers can adapt or localise the copy by editing this file or by copying
| it to lang/<locale>/data_privacy.php and translating.
|
| Placeholders use Laravel's :placeholder convention.
|
*/

return [

    'error_no_tenant'             => "We couldn't resolve your workspace. Please log out and log back in.",

    // --- Right of Access — Data Export ----------------------------------
    'export_title'                => 'Export my data',
    'export_description'          => "We'll build a ZIP of every record we hold for this workspace — leads, forms, automations, settings, the lot — in JSON format. Download link is valid for 48 hours.",

    'export_building_title'       => 'Building your export…',
    'export_building_body'        => 'Refresh the page in a few minutes. Big workspaces can take a while.',

    'export_failed_title'         => 'Last export failed.',
    'export_failed_body'          => 'Try requesting a new one — if it fails again, contact support.',
    'export_failed_btn'           => 'Try again',

    'export_ready_title'          => '✅ Your archive is ready',
    'export_ready_size'           => 'Size: :size',
    'export_ready_expires'        => 'Expires :when',
    'export_download_btn'         => 'Download ZIP',
    'export_rebuild_btn'          => 'Build a fresh export',
    'export_request_btn'          => 'Export my data',

    // --- Right to Erasure — Workspace Deletion --------------------------
    'deletion_title'              => 'Delete my workspace',
    'deletion_description'        => 'Permanently removes this workspace and every record we hold for it. Scheduled 30 days ahead so you can change your mind — until then, the workspace stays active.',

    'deletion_scheduled_title'    => '⏰ Deletion scheduled',
    'deletion_scheduled_on'       => 'On :date',
    'deletion_days_left'          => '(:count day left)|(:count days left)',
    'deletion_cancel_btn'         => 'Cancel deletion',

    'deletion_request_confirm'    => "Are you absolutely sure? This schedules permanent deletion of every record in this workspace. You'll have 30 days to cancel.",
    'deletion_request_btn'        => 'Schedule deletion',

    'deletion_not_owner'          => 'Only the workspace owner can schedule deletion. Please contact the owner if this workspace needs to be permanently removed.',

    // --- Footer ---------------------------------------------------------
    'gdpr_footer'                 => 'These controls implement GDPR Articles 15 (Right of Access) and 17 (Right to Erasure). For DPA / sub-processor info, contact support.',

    // --- README.txt bundled in the GDPR export ZIP ----------------------
    // Rendered by TenantDataExporter::buildReadme() at request time.
    // :app, :workspace, :timestamp are substituted in PHP, not the
    // translator — we keep them in-template for clarity.
    'readme_title'                => ':app — Personal Data Export',
    'readme_divider'              => '============================================================',
    'readme_workspace'            => 'Workspace: :workspace',
    'readme_generated'            => 'Generated: :timestamp',
    'readme_intro'                => "This archive contains every record we hold for your workspace,\nexported in JSON for portability (importable anywhere — CRM,\nspreadsheets, custom scripts).",
    'readme_file_map_header'      => 'File map',
    'readme_subdivider'           => '------------------------------------------------------------',
    'readme_row_readme'           => 'README.txt                         This file',
    'readme_row_tenant'           => 'tenant.json                        Workspace profile, settings, branding',
    'readme_row_members'          => 'members.json                       Team members + roles',
    'readme_row_leads'            => 'leads.json                         Every lead',
    'readme_row_companies'        => 'companies.json                     Linked company records',
    'readme_row_activities'       => 'lead_activities.json               Timeline events',
    'readme_row_notes'            => 'lead_notes.json                    Notes attached to leads',
    'readme_row_tasks'            => 'lead_tasks.json                    Tasks',
    'readme_row_messages'         => 'lead_messages.json                 Inbound / outbound messages',
    'readme_row_emails'           => 'lead_emails.json                   Emails',
    'readme_row_calls'            => 'lead_calls.json                    Call logs',
    'readme_row_pipelines'        => 'pipelines.json                     Pipelines',
    'readme_row_pipeline_stages'  => 'pipeline_stages.json               Stages',
    'readme_row_tags'             => 'tags.json                          Tags',
    'readme_row_custom_fields'    => 'custom_field_definitions.json      Custom field schema',
    'readme_row_forms'            => 'forms.json                         Forms',
    'readme_row_form_submissions' => 'form_submissions.json              Submissions',
    'readme_row_landing_pages'    => 'landing_pages.json                 Landing pages',
    'readme_row_automations'      => 'automations.json                   Automations',
    'readme_row_email_sequences'  => 'email_sequences.json               Drip campaigns',
    'readme_row_email_templates'  => 'email_templates.json               Templates',
    'readme_row_products'         => 'products.json                      Catalogue',
    'readme_row_quotes'           => 'quotes.json                        Quotes',
    'readme_row_invoices'         => 'invoices.json                      Invoices',
    'readme_row_meeting_types'    => 'meeting_types.json                 Bookable meeting types',
    'readme_row_meeting_bookings' => 'meeting_bookings.json              Booked meetings',
    'readme_row_integrations'     => 'integrations.json                  Connected integrations',
    'readme_row_api_keys'         => 'api_keys.json                      API keys (secrets redacted)',
    'readme_notes_header'         => 'Notes',
    'readme_note_iso8601'         => '- All datetime fields are ISO-8601.',
    'readme_note_redaction'       => "- API key secrets and any column flagged secret / token /\n  api_key / key are redacted.",
    'readme_note_attachments'     => "- File attachments are referenced by path only — to download\n  the actual files, contact support.",
    'readme_note_snapshot'        => '- This export is a snapshot at :timestamp.',

];
