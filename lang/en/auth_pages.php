<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Auth pages (resources/views/auth/*.blade.php, invitation/*.blade.php)
|------------------------------------------------------------
| Accessed via __('auth_pages.<key>').
*/

return [

    // Password setup
    'set_your_password'        => 'Set your password',
    'fix_the_following'        => 'Fix the following:',
    'email_label'              => 'Email',
    'new_password_label'       => 'New password',
    'confirm_password_label'   => 'Confirm password',
    'set_password_submit'      => 'Set password & sign in',

    // Invitation accept
    'create_your_account'      => 'Create your account',
    'your_name_label'          => 'Your name',
    'password_label'           => 'Password',
    'accept_invitation_submit' => 'Accept invitation & create account',

    // ─── Browser titles ──────────────────────────────────────────────
    'password_setup_title'     => 'Set your password — :app',
    'invitation_accept_title'  => 'Accept invitation — :app',

    // ─── Password setup lead + footer ────────────────────────────────
    'password_setup_lead'         => "You've been invited to :app. Set a password for :email to finish signing in.",
    'password_min_placeholder'    => 'At least 8 characters',
    'password_strength_hint'      => 'Use at least 8 characters, including a letter and a number.',
    'password_setup_link_expired' => 'If this link has expired, head back to :signin and request a new invite.',
    'signin_link'                 => 'sign in',

    // ─── Invitation pill + headings ──────────────────────────────────
    'invitation_pill'             => 'Team invitation',
    'invitation_youre_invited'    => "You're invited to join :workspace",
    'invitation_role_on_app'      => 'as :role on :app',
    'name_placeholder'            => 'Jane Doe',
    'invitation_expires_at'       => 'This invitation expires :when.',
];
