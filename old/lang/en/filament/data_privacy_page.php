<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — DataPrivacyPage translation strings
|--------------------------------------------------------------------------
|
| Page header, subheading and notification copy for the tenant-side
| GDPR privacy page at /admin/privacy.
| Consumed via __('filament/data_privacy_page.<key>').
|
*/

return [

    // ─── Navigation / page header ──────────────────────────────────────
    'nav_label'                   => 'Privacy & Data',
    'title'                       => 'Privacy & Data',
    'heading'                     => 'Your data, your call',
    'subheading'                  => 'Download a complete copy of everything we hold for this workspace, or schedule its deletion. Required by GDPR Article 15 (access) and Article 17 (erasure).',

    // ─── Shared error notifications ────────────────────────────────────
    'no_workspace_context'        => 'No workspace context.',
    'auth_required'               => 'Authentication required.',

    // ─── Export flow ───────────────────────────────────────────────────
    'export_requested_title'      => 'Export requested',
    'export_requested_body'       => 'We are building your archive in the background. You will receive a notification with a 24-hour download link as soon as it is ready.',
    'export_unavailable_title'    => 'Export not available',
    'export_unavailable_body'     => 'Either it has not finished, has expired, or the file is missing. Request a new export.',

    // ─── Deletion flow ─────────────────────────────────────────────────
    'owner_only_title'            => 'Owner only.',
    'owner_only_body'             => 'Only the workspace owner can schedule deletion.',
    'deletion_scheduled_title'    => 'Deletion scheduled',
    'deletion_scheduled_body'     => 'Your workspace will be permanently deleted in :days days. You can cancel any time before then from this page.',
    'deletion_cancelled_title'    => 'Deletion cancelled',
    'deletion_cancelled_body'     => 'Your workspace will remain active.',
];
