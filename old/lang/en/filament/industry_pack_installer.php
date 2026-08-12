<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — IndustryPackInstaller translation strings
|--------------------------------------------------------------------------
|
| Notification copy for the Industry Packs installer page.
| Consumed via __('filament/industry_pack_installer.<key>').
|
*/

return [

    // ----- Navigation -----
    'nav_label' => 'Industry Packs',

    // ----- Page title (rendered in <h1> via getTitle()) -----
    'title'     => 'Industry Packs',

    'notif_no_tenant'            => 'No tenant context.',
    'notif_install_failed_title' => 'Could not install industry pack',
    'notif_installed_title'      => 'Industry pack installed',
    'notif_summary_body'         => ':pipelines pipelines, :stages stages, :custom_fields custom fields, :tags tags, :email_templates email templates, :automations automations, :forms forms.',

    // wire:confirm
    'confirm_install_pack'       => 'Install the :name pack? This will create pipelines, custom fields, tags, automations, and forms in your workspace.',

    // ─── Page body ───
    'intro'                      => 'Industry packs bootstrap your workspace with a ready-made set of pipelines, custom fields, tags, email templates, automations, and forms tailored for a specific vertical. Automations install disabled so you can review them before turning them on. Running a pack twice is safe — existing items are detected by name/slug/key and skipped.',
    'stat_pipelines'             => 'Pipelines',
    'stat_custom_fields'         => 'Custom fields',
    'stat_tags'                  => 'Tags',
    'stat_email_templates'       => 'Email templates',
    'stat_automations'           => 'Automations',
    'stat_forms'                 => 'Forms',
    'install_pack'               => 'Install Pack',
];
