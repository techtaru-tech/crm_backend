<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Updates page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_updates.<key>').
*/

return [
    'title'                            => 'Updates',
    'navigation_label'                 => 'Updates',

    // Form section
    'apply_section_description'        => 'Upload a .zip release package to upgrade this installation in place. A safety backup is taken first unless you explicitly skip it.',
    'package_label'                    => 'Update package (.zip)',
    'package_helper'                   => 'Supported: any zip whose top-level layout matches the LeadHub distribution.',
    'skip_backup_label'                => 'Skip pre-update backup',
    'skip_backup_helper'               => 'Not recommended. Leave off unless you have just taken a backup manually.',

    // Notifications - check
    'check_complete_title'             => 'Update check complete.',
    'check_complete_default_body'      => 'See the current/latest version banner above.',
    'check_failed_title'               => 'Update check failed.',

    // Notifications - apply
    'apply_summary_files_written'      => 'Wrote :count files',
    'apply_summary_version'            => ' · now v:version',
    'apply_summary_backup'             => ' · backup: :backup',
    'apply_success_title'              => 'Update applied successfully.',
    'apply_failed_title'               => 'Update failed.',

    // Header actions
    'action_check'                     => 'Check for updates',
    'action_apply'                     => 'Apply uploaded package',
    'action_apply_confirmation'        => 'This will overwrite application files with the contents of the uploaded zip, run pending migrations, and clear every cache. A pre-update backup is taken first. Continue?',

    // Section headings
    'update_history'                   => 'Update History',

    // ─── Blade view (resources/views/filament/super-admin/pages/updates.blade.php) ──
    'installed_version'                => 'Installed Version',
    'update_available'                 => 'Update Available',
    'view_changelog'                   => 'View changelog',
    'on_latest_version'                => "You're on the latest version.",
    'history_empty'                    => 'No updates applied yet.',
    'col_package'                      => 'Package',
    'col_backup'                       => 'Backup',
    'col_result'                       => 'Result',
    'last_checked'                     => 'Last checked :time',
    'col_when'                         => 'When',
    'col_from_to'                      => 'From → To',

    // ─── History badges ───
    'badge_failed'                     => 'failed',
    'badge_files_written'              => ':count files',
];
