<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| EmailSettingsPage strings — accessed via __('filament/email_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: SMTP (outbound email) configuration for the
| current workspace. Includes the "Send Test Email" header action.
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Email',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Email (SMTP) Settings',

    // --- SMTP fields ---------------------------------------------------
    'host'            => 'Host',
    'port'            => 'Port',
    'encryption'      => 'Encryption',
    'username'        => 'Username',
    'password'        => 'Password',
    'password_helper' => 'Leave blank to keep the current password. Type a new value to replace it.',
    'from_name'       => 'From Name',
    'from_email'      => 'From Email',

    // --- Test email action ---------------------------------------------
    'send_test_email'  => 'Send Test Email',
    'recipient'        => 'Send test email to',
    'recipient_helper' => 'Any address — useful for sending the test to yourself or to the account owner for verification.',
    'modal_submit'     => 'Send test',

    // --- Test notifications --------------------------------------------
    'no_recipient' => 'No recipient — add an email address first.',
    'test_sent_to' => 'Test email sent to :recipient',
    'send_failed'  => 'Send failed: :error',

    // --- Header actions ------------------------------------------------
    'save_settings' => 'Save Settings',

    // ─── Blade view — override notice banner ───────────────────────────
    'override_notice_title' => "This workspace's email is sent through the service by default",
    'override_notice_body_html' => "Leave this page empty and outbound email (password resets, automations, sequence mailers, test messages, notifications) is delivered using the service's own mail server — you don't have to configure anything.\n                Fill in an SMTP host below <strong>only if</strong> you want every email this workspace sends to go through <em>your own</em> mail server instead (e.g. for sender-domain authentication, stricter deliverability control, or compliance).\n                When saved, your SMTP settings override the service default for this workspace — other workspaces are unaffected, and if you later clear these fields, mail reverts to the service default automatically.",

    // ─── Select options ────────────────────────────────────────────
    'option_encryption_none' => 'None',

];
