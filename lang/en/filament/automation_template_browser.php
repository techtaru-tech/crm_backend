<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — AutomationTemplateBrowser translation strings
|--------------------------------------------------------------------------
|
| Notification copy for the automation-templates browser page.
| Consumed via __('filament/automation_template_browser.<key>').
|
*/

return [

    // ----- Page title (rendered in <h1> via getTitle()) -----
    'title'                        => 'Automation Templates',

    'notif_no_tenant'              => 'No tenant context.',
    'notif_install_failed_title'   => 'Could not install template',
    'notif_installed_title'        => 'Template installed',
    'notif_installed_body'         => 'Review and enable the automation to start using it.',

    // ─── Page body ───
    'intro'                        => 'Pick a pre-built automation to install into your workspace. Installed templates are saved as disabled automations so you can review the steps and enable them when ready.',
    'use_template'                 => 'Use Template',

    // ─── Step counter (pluralized) ───
    'step_count'                   => '{1} :count step|[2,*] :count steps',
];
