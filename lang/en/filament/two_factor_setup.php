<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| TwoFactorSetup page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/two_factor_setup.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label' => 'Two-Factor Auth',

    // ----- Page heading -----
    'page_title' => 'Two-Factor Authentication',

    // ----- Section headings & wire:confirm -----
    'recovery_codes_heading'   => 'Recovery Codes',
    'confirm_disable_2fa'      => 'Are you sure you want to disable 2FA? Your account will be less secure.',

    // ----- Enabled state -----
    'section_2fa_active'           => 'Two-Factor Authentication is Active',
    'account_protected'            => 'Your account is protected with 2FA',
    'codes_required_each_login'    => 'Authentication codes are required at each login.',
    'save_codes_safe_place'        => 'Save these codes in a safe place. Each code can only be used once.',
    'btn_regenerate_codes'         => 'Regenerate Recovery Codes',
    'btn_disable_2fa'              => 'Disable 2FA',

    // ----- Setup (QR code) state -----
    'section_scan_qr'              => 'Scan QR Code',
    'scan_with_authenticator'      => 'Scan this QR code with your authenticator app (Google Authenticator, Authy, 1Password, etc.)',
    'manual_code_label'            => 'Manual code:',
    'enter_verification_code'      => 'Enter Verification Code from Authenticator App',
    'btn_verify_and_enable'        => 'Verify & Enable',

    // ----- Initial state -----
    'section_enable_2fa'           => 'Enable Two-Factor Authentication',
    'initial_state_lede'           => "Add an extra layer of security to your account by enabling two-factor authentication. You'll need an authenticator app like Google Authenticator or Authy.",
    'btn_set_up_2fa'               => 'Set Up Two-Factor Authentication',
];
