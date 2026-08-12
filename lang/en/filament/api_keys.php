<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| ApiKeyResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/api_keys.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'         => 'API Keys',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'        => 'API Key',
    'plural_model_label' => 'API Keys',

    // ----- Table column labels -----
    'col_name'           => 'Name',
    'suffix_per_hour'    => '/hr',

    // ----- Form fields -----
    'key_name'          => 'Key Name',
    'key_name_placeholder' => 'e.g. Zapier integration',
    'key_name_help'     => 'A friendly label to identify this key.',
    'permissions'       => 'Permissions',
    'permissions_help'  => 'Leave empty to grant all permissions.',
    'rate_limit'        => 'Rate Limit (req/hour)',
    'expires_at'        => 'Expires At',
    'expires_at_help'   => 'Leave empty for no expiry.',

    // ----- Table -----
    'col_key_prefix'    => 'Key Prefix',
    'col_rate_limit'    => 'Rate Limit',
    'col_last_used'     => 'Last Used',
    'col_expires'       => 'Expires',
    'col_active'        => 'Active',
    'col_created'       => 'Created',
    'never'             => 'Never',

    // ----- Actions -----
    'revoke'            => 'Revoke',
    'new_api_key'       => 'New API Key',

    // ----- Empty state -----
    'empty_heading'     => 'No API keys yet',
    'empty_description' => 'Create an API key to integrate with external services via the REST API.',

    // ----- Blade view: new-key banner -----
    'banner_title'        => 'API Key Created Successfully',
    'banner_msg_lede'     => 'Copy your new API key below.',
    'banner_msg_only_once' => 'This is the only time it will be shown.',
    'banner_msg_store_safe' => "Store it somewhere safe — you won't be able to see it again.",
    'banner_copy_button'  => 'Copy Key',
    'banner_copied_label' => 'Copied!',

];
