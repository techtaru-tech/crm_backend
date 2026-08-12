<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — UserResource translation strings
|--------------------------------------------------------------------------
|
| Labels, action copy, modal text and notifications for the Team Members
| (Users) resource at /admin/users.
| Consumed via __('filament/users.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Team Members',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Team Member',
    'plural_model_label'                => 'Team Members',

    // ─── Form fields ───────────────────────────────────────────────────
    'name'                              => 'Name',
    'email'                             => 'Email',
    'password'                          => 'Password',
    'avatar'                            => 'Avatar',
    'created_at'                        => 'Created',

    // ─── Form ──────────────────────────────────────────────────────────
    'password_helper'                   => 'Leave blank to keep the current password.',
    'role'                              => 'Role',
    'two_factor_enabled'                => '2FA Enabled',

    // ─── Table columns ─────────────────────────────────────────────────
    'two_factor_short'                  => '2FA',
    'status'                            => 'Status',
    'status_suspended'                  => 'Suspended',
    'status_active'                     => 'Active',

    // ─── Meeting link action ───────────────────────────────────────────
    'action_meeting_link'               => 'Meeting Link',
    'booking_links_suffix'              => "'s booking links",
    'modal_close'                       => 'Close',

    // ─── Password reset action ─────────────────────────────────────────
    'action_send_password_reset'        => 'Send password reset',
    'reset_modal_heading_prefix'        => 'Send a reset link to ',
    'reset_modal_heading_suffix'        => '?',
    'reset_modal_description'           => 'The user will receive an email with a link to choose a new password. The link expires in 60 minutes.',
    'reset_sent_title'                  => 'Reset link sent',
    'reset_sent_body_prefix'            => 'Emailed ',
    'reset_sent_body_suffix'            => ' — valid for 60 minutes.',
    'reset_failed_title'                => 'Could not send reset link',

    // ─── Suspend action ────────────────────────────────────────────────
    'action_suspend'                    => 'Suspend',
    'suspend_modal_heading_prefix'      => 'Suspend ',
    'suspend_modal_heading_suffix'      => '?',
    'suspend_modal_description'         => 'They will lose access immediately. Re-activate any time with the Unsuspend action.',
    'suspend_notification_title'        => 'User suspended',
    'suspend_notification_body_suffix'  => ' can no longer sign in.',

    // ─── Unsuspend action ──────────────────────────────────────────────
    'action_unsuspend'                  => 'Unsuspend',
    'unsuspend_notification_title'      => 'User reactivated',
    'unsuspend_notification_body_suffix' => ' can sign in again.',

    // ─── Invite team member ────────────────────────────────────────────
    'invite_action_label'               => 'Invite Team Member',
    'invite_email_label'                => 'Email Address',
    'invite_failed_title'               => 'Could not send invitation',
    'invite_sent_title'                 => 'Invitation sent to :email',

    // ─── CreateUser notifications ──────────────────────────────────────
    'create_failed_title'               => 'Failed to create user',
    'create_seat_limit_title'           => 'Seat limit reached',
    'create_email_taken_title'          => 'Email address already in use',

    // ─── CreateUser (invitation flow) ──────────────────────────────────
    'create_invite_title'               => 'Invite Team Member',
    'create_invite_heading'             => 'Invite a new team member',
    'create_invite_subheading'          => "They'll receive an email with a link to set their name and password.",
    'create_no_workspace_title'         => 'No workspace context',
    'create_no_workspace_body'          => 'We could not resolve your workspace. Sign out and back in, then try again.',
    'create_invite_sent_title'          => 'Invitation sent',
    'create_invite_sent_body'           => 'An email with a setup link has been sent to :email.',

    // ─── Select options ────────────────────────────────────────────
    'option_role_manager'               => 'Manager',
    'option_role_member'                => 'Member',

    // ─── Booking-links modal (per-user list) ───────────────────────
    'booking_links_minutes_suffix'      => 'min',

    // ─── ListUsers subheading (role-permissions banner) ────────────
    // Rendered via resources/views/filament/resources/users/list-subheading.blade.php
    // — see App\Filament\Resources\UserResource\Pages\ListUsers::getSubheading().
    'subheading_role_permissions_title' => 'Role permissions',
    'subheading_intro'                  => 'Two tiers for team members. Both are tenant-scoped — they never see data outside this workspace.',
    'subheading_manager_title'          => 'Manager',
    'subheading_manager_desc'           => 'Full leads + pipelines + automations + forms. Can invite & suspend team. Cannot delete admins.',
    'subheading_member_title'           => 'Member',
    'subheading_member_desc'            => 'Standard user. Work with leads, view forms & reports. No team management, no settings.',
];
