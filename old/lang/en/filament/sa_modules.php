<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Modules page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_modules.<key>').
*/

return [
    'title'                          => 'Modules',
    'navigation_label'               => 'Modules',

    // Install section
    'install_section_description'    => 'Upload a zip that contains a module.json manifest. The module folder is copied into Modules/, the nwidart autoloader is regenerated, and the module starts in the disabled state until you enable it.',
    'module_zip_label'               => 'Module zip',
    'module_zip_helper'              => 'Any zip whose top-level contains a module.json file (directly or inside a single wrapping folder).',

    // Notifications
    'module_installed_title'         => 'Module installed successfully.',
    'module_installed_body'          => 'Installed :name. Enable it from the list below.',

    // Header actions
    'action_regenerate'              => 'Regenerate autoload',
    'action_install'                 => 'Install uploaded module',
    'action_install_confirmation'    => 'The module folder will be copied into Modules/. If a module with the same name already exists it will be replaced. Continue?',

    // Hero / page content
    'hero_eyebrow'                   => 'HMVC Extensions',
    'hero_title'                     => 'Modules',
    'unavailable_warning_strong'     => 'Module system unavailable.',
    'installed_section_title'        => 'Installed Modules',
    'empty_no_modules'               => 'No modules installed. Upload a zip above to get started.',

    // Table column headers
    'col_module'                     => 'Module',
    'col_version'                    => 'Version',
    'col_status'                     => 'Status',
    'col_actions'                    => 'Actions',

    // Action buttons + confirmations
    'btn_disable'                    => 'Disable',
    'btn_enable'                     => 'Enable',
    'btn_delete'                     => 'Delete',
    'confirm_permanently_delete'     => 'Permanently delete module :name? This cannot be undone.',

    // ─── Hero body + warning + status pills ───
    'hero_subtitle_html'             => 'Extend your LeadHub in 30 seconds. Upload a module zip, click Enable, and your new feature is live. No composer, no downtime, no developer required. Each module lives in a self-contained folder under <code class="mod-hero-code">Modules/&lt;Name&gt;</code> with its own routes, migrations, views, translations and admin resources.',
    'unavailable_warning_body_html'  => 'The module runtime did not load. This usually means the upload was incomplete &mdash; please re-upload the full LeadHub distribution zip and make sure the <code class="mod-warn-code">vendor/</code> folder is included. If the problem persists, contact support. The list below falls back to a filesystem scan so any manually copied modules are still visible.',
    'pill_enabled'                   => 'enabled',
    'pill_disabled'                  => 'disabled',
];
