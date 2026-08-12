<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SecuritySettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/security_settings.<key>').
*/

return [
    'title'                              => 'Security Settings',
    'navigation_label'                   => 'Security',

    // Auth section
    'auth_section_description'           => 'These settings apply to every user in your team and take effect immediately.',
    'enforce_2fa_label'                  => 'Enforce Two-Factor Authentication',
    'enforce_2fa_helper_prefix'          => 'When enabled, every user in your team is redirected to the QR-code setup on their next request and cannot use the panel until they complete enrollment.',
    'enforce_2fa_helper_link'            => 'Set up your own 2FA now →',
    'session_lifetime_label'             => 'Session Lifetime (minutes)',
    'minutes_suffix'                     => 'min',

    // Rate limit section
    'max_login_attempts_label'           => 'Max Failed Login Attempts',
    'lockout_duration_label'             => 'Lockout Duration (minutes)',

    // IP whitelist section
    'ip_whitelist_section_description'   => 'Only allow admin panel access from these IP addresses. Leave empty to allow all.',
    'ip_whitelist_label'                 => 'Allowed IP Addresses',
    'ip_whitelist_placeholder'           => 'e.g. 192.168.1.1',
    'ip_whitelist_helper'                => 'Enter IPs (IPv4 or IPv6) or CIDR ranges and press Enter to add each.',

    // Actions
    'action_save'                        => 'Save Settings',
];
