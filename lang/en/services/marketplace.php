<?php

declare(strict_types=1);

return [
    'install_suffix'              => '(from marketplace)',
    'template_not_published'      => 'Template is not published',
    'plan_not_included'           => 'Your current plan does not include marketplace installs. Upgrade to unlock the DFY templates library.',
    'monthly_limit_reached'       => "You have reached your plan's marketplace install limit (:used/:limit) for this month. Upgrade to install more.",
    'unsupported_template_type'   => 'Unsupported template type: :type',
    'install_failed'              => 'Install failed: :error',

    // Defensive fallbacks for malformed marketplace template payloads.
    // Fire only when an uploaded template omits the corresponding field
    // (the buyer can always rename in the UI after install).
    'form_default_success_message' => 'Thanks!',
    'pipeline_stage_default_name'  => 'Stage :index',

    // Fallback when the installer tenant has no name set — surfaced in
    // the contributor's "your template was installed" notification body
    // via the `installer` placeholder.
    'installer_tenant_name_fallback' => 'A workspace',
];
