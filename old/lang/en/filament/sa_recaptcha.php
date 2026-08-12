<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin RecaptchaSettingsPage — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_recaptcha.<key>').
*/

return [

    // ----- Page title / navigation -----
    'title'                       => 'reCAPTCHA Protection',
    'navigation_label'            => 'reCAPTCHA',

    // ----- Google reCAPTCHA v3 section -----
    'console_intro_prefix'        => 'Get your keys from',
    'console_link_label'          => 'Google reCAPTCHA admin console',
    'console_intro_suffix'        => 'Pick v3 when creating the site. Add :host to the domain list.',
    'master_switch_label'         => 'Master switch',
    'master_switch_helper'        => 'Turn reCAPTCHA on or off without losing your keys. When off every guard below is ignored.',
    'site_key_label'              => 'Site key',
    'site_key_helper'             => 'Public key. Shipped to the browser.',
    'secret_key_label'            => 'Secret key',
    'secret_key_helper'           => 'Server-only key. Used to verify submitted tokens against Google.',
    'min_score_label'             => 'Minimum score',
    'min_score_helper'            => 'Score between 0.0 (definite bot) and 1.0 (definite human). Submissions below this are rejected. Google recommends 0.5.',

    // ----- Protected surfaces section -----
    'protected_surfaces_description' => 'Per-page toggles. Disable individually if you see false-positive rejections; the master switch above overrides these.',
    'guard_register_label'        => 'Public /register form',
    'guard_register_helper'       => 'Protects the self-service workspace sign-up form.',
    'guard_admin_login_label'     => 'Tenant /admin login',
    'guard_admin_login_helper'    => 'Protects the tenant-facing sign-in page.',
    'guard_sa_login_label'        => 'Super-admin login',
    'guard_sa_login_helper'       => 'Protects the super-admin sign-in page.',

    // ----- Notifications & actions -----
    'settings_saved'              => 'reCAPTCHA settings saved',
    'action_save'                 => 'Save',

];
