<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin SystemHealth page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_system_health.<key>').
*/

return [
    'title'                            => 'System Health',

    // System info labels
    'label_leadhub_version'            => 'LeadHub Version',
    'label_laravel'                    => 'Laravel',
    'label_php'                        => 'PHP',
    'label_environment'                => 'Environment',
    'label_debug_mode'                 => 'Debug Mode',
    'label_queue_driver'               => 'Queue Driver',
    'label_cache_driver'               => 'Cache Driver',
    'label_session_driver'             => 'Session Driver',
    'label_mail_driver'                => 'Mail Driver',
    'label_database'                   => 'Database',
    'label_timezone'                   => 'Timezone',
    'label_billing'                    => 'Billing',

    // Value strings
    'value_on'                         => 'ON',
    'value_off'                        => 'OFF',
    'value_enabled'                    => 'Enabled',
    'value_disabled'                   => 'Disabled',
    'value_not_available'              => 'N/A',

    // Card headings
    'card_system_info'                 => 'System Information',
    'card_disk_usage'                  => 'Disk Usage',

    // ─── Disk-usage stat labels ──
    'stat_label_total'                 => 'Total',
    'stat_label_used'                  => 'Used',
    'stat_label_free'                  => 'Free',

    // ─── Disk usage summary (parameterized) ─
    'disk_used_of_total'               => ':used used of :total',

    // ─── Maintenance actions (shared-hosting friendly) ──
    // Surface artisan operations that CodeCanyon buyers on shared
    // hosting cannot run from SSH.  Hidden in demo mode.
    'card_maintenance'                     => 'Maintenance',

    'action_finalize_update'                 => 'Finalize update',
    'action_finalize_update_confirm'         => 'Runs the full post-update sequence in one click: delete stale bootstrap caches → apply pending database migrations → clear every Laravel cache (config, route, view, cache) → rebuild production caches (config, route, view, event, Filament components, blade icons). Use this right after you replace your install files (e.g. you re-uploaded the LeadHub zip via cPanel File Manager OR you uploaded a release zip via the Updates page). Safe to click any time — already-applied steps are no-ops.',
    'notif_finalize_update_success_title'    => 'Update finalized',
    'notif_finalize_update_success_body'     => 'All post-update steps completed. Your install is now fully synced with the new code.',
    'notif_finalize_update_partial_title'    => 'Update finalized with warnings',
    'notif_finalize_update_failures_label'   => 'Steps that did not complete:',
    'notif_finalize_update_failed_title'     => 'Could not finalize update',

    'action_clear_caches'                  => 'Clear all caches',
    'action_clear_caches_confirm'          => 'Runs `php artisan optimize:clear` to flush config, route, view, event and compiled caches in one shot. Safe to run any time — usually needed after deploying new code or updating settings on a shared-hosting plan where SSH is not available.',
    'notif_clear_caches_success_title'     => 'Caches cleared',
    'notif_clear_caches_success_body'      => 'All Laravel caches were flushed.',
    'notif_clear_caches_failed_title'      => 'Could not clear caches',

    'action_run_migrations'                  => 'Apply pending migrations',
    'action_run_migrations_confirm'          => 'Runs `php artisan migrate --force` to apply every pending database migration. Use this right after replacing your install files (e.g. you re-uploaded the LeadHub zip via cPanel File Manager) so new database columns and settings rows match the new code. Safe to click any time — already-applied migrations are skipped automatically.',
    'notif_run_migrations_success_title'     => 'Migrations applied',
    'notif_run_migrations_success_body'      => 'All pending database migrations were applied. You can now retry the action that previously failed.',
    'notif_run_migrations_failed_title'      => 'Could not apply migrations',

    'action_rebuild_caches'                => 'Rebuild caches',
    'action_rebuild_caches_confirm'        => 'Clears then re-warms config, route, and view caches in sequence — speeds up subsequent requests on slow shared hosts. Skip this if you are still iterating; clear-only is usually enough.',
    'notif_rebuild_caches_success_title'   => 'Caches rebuilt',
    'notif_rebuild_caches_success_body'    => 'Config, route, and view caches were cleared and rebuilt.',
    'notif_rebuild_caches_failed_title'    => 'Could not rebuild caches',

    'action_storage_link'                  => 'Create storage symlink',
    'action_storage_link_confirm'          => 'Creates the public/storage symlink that Laravel uses to expose uploaded files. Needed on fresh installs where the symlink was not created during setup (e.g. open_basedir blocked the installer).',
    'notif_storage_link_success_title'     => 'Storage symlink created',
    'notif_storage_link_already_title'     => 'Storage symlink already exists',
    'notif_storage_link_failed_title'      => 'Could not create storage symlink',

    'action_restart_queue'                 => 'Restart queue workers',
    'action_restart_queue_confirm'         => 'Signals running queue workers to gracefully restart so they pick up new code. No effect on the `sync` driver — only relevant if you run Horizon or `queue:work`.',
    'notif_restart_queue_success_title'    => 'Queue workers signalled to restart',
    'notif_restart_queue_skipped_title'    => 'Queue driver is `sync`',
    'notif_restart_queue_skipped_body'     => 'No background workers run on the `sync` driver — nothing to restart.',
    'notif_restart_queue_failed_title'     => 'Could not restart queue workers',
];
