<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Backups page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_backups.<key>').
*/

return [
    'title'                            => 'Backups & Restore',
    'navigation_label'                 => 'Backups',

    // Notifications
    'backup_created_title'             => 'Backup created.',
    'backup_failed_title'              => 'Backup failed.',
    'backup_deleted_title'             => 'Backup deleted.',
    'backup_delete_failed_title'       => 'Unable to delete backup.',
    'restore_complete_title'           => 'Restore complete.',
    'restore_complete_body'            => 'Restored :files files and :rows SQL statements from :backup.',
    'restore_failed_title'             => 'Restore failed.',
    'backup_not_found_title'           => 'Backup not found.',
    'backup_healthy_title'             => 'Backup looks healthy.',
    'backup_healthy_body'              => ':name — :count SQL statements detected.',
    'backup_verify_failed_title'       => 'Backup verification failed.',

    // Header actions
    'action_create'                    => 'Create backup now',

    // Restore modal
    'restore_modal_heading'            => 'Restore this backup?',
    'restore_modal_description'        => 'Restoring overwrites every database table and uploaded file with the contents of the selected archive. The current state cannot be recovered without a separate backup — make a fresh backup first if you want a rollback option.',
    'restore_modal_submit'             => 'Yes, restore',

    // Delete modal
    'delete_modal_heading'             => 'Delete this backup?',
    'delete_modal_description'         => 'The archive will be permanently removed from disk. Your other backups are unaffected, but this specific snapshot cannot be recovered after deletion.',
    'delete_modal_submit'              => 'Delete',

    // Hero / page content
    'hero_eyebrow'                     => 'System',
    'hero_title'                       => 'Backups & Restore',
    'hero_sub_html'                    => 'Every backup bundles your database and uploaded files into a single timestamped zip under <code>storage/app/backups/</code>. Use the "Create backup now" button in the header before risky operations, and restore with one click when you need to roll back.',
    'empty_no_backups'                 => 'No backups yet. Click "Create backup now" to make your first one.',

    // Table columns
    'col_archive'                      => 'Archive',
    'col_created'                      => 'Created',
    'col_size'                         => 'Size',
    'col_actions'                      => 'Actions',

    // Row action buttons
    'btn_download'                     => 'Download',
    'btn_verify'                       => 'Verify',
    'btn_restore'                      => 'Restore',
    'btn_delete'                       => 'Delete',

    // Nightly toggle banner
    'nightly_status_strong'            => 'Nightly backups: :state.',
    'nightly_state_enabled'            => 'enabled',
    'nightly_state_disabled'           => 'disabled',
    'nightly_enabled_description'      => 'A fresh backup is created automatically every night at 02:00 UTC via the scheduled task runner.',
    'nightly_disabled_link_text'       => 'Settings → Script Settings',
    'nightly_disabled_prefix'          => 'Enable nightly backups in ',
    'nightly_disabled_suffix'          => ' for automatic protection.',
    'nightly_footer_note'              => 'The buttons above are for ad-hoc and pre-upgrade backups.',
];
