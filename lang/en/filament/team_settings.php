<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| TeamSettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/team_settings.<key>').
*/

return [
    'title'                          => 'Team & Seat Management',
    'navigation_label'               => 'Team & Seats',

    // Notifications - member ops
    'member_removed'                 => ':name removed from this workspace.',
    'member_suspended_title'         => ':name suspended.',
    'member_suspended_body'          => 'They can no longer sign in.',
    'member_reactivated'             => ':name reactivated.',

    // Reset password
    'reset_link_sent_title'          => 'Reset link sent',
    'reset_link_sent_body'           => ':email will receive an email within a minute. Link valid for 60 minutes.',
    'reset_link_failed_title'        => 'Could not send reset link',

    // Invite action
    'action_invite'                  => 'Invite team member',
    'invite_email_label'             => 'Email',
    'invite_email_placeholder'       => 'teammate@company.com',
    'invite_role_label'              => 'Role',
    'invite_role_manager'            => 'Manager',
    'invite_role_member'             => 'Member',
    'invite_failed_title'            => 'Could not send invitation',
    'invite_sent_title'              => 'Invitation sent',
    'invite_sent_body'               => 'Emailed :email.',

    // Adjust seats action
    'action_adjust_seats'            => 'Adjust seat limit',
    'max_seats_label'                => 'Maximum seats',

    // ─── Blade view — role permissions banner ─────────────────────────
    'role_permissions_title'         => 'Role permissions',
    'role_permissions_lede'          => 'Two tiers for team members. Both are tenant-scoped — they never see data outside this workspace.',
    'role_card_manager_title'        => 'Manager',
    'role_card_manager_desc'         => 'Full leads + pipelines + automations + forms. Can invite & suspend team. Cannot delete admins.',
    'role_card_member_title'         => 'Member',
    'role_card_member_desc'          => 'Standard user. Work with leads, view forms & reports. No team management, no settings.',

    // ─── Blade view — seat usage card ─────────────────────────────────
    'seat_usage_title'               => 'Seat Usage',
    'seat_usage_subtitle'            => ':used of :max seats used',
    'seat_count_suffix'              => 'available',
    'seat_plan_prefix'               => 'Plan:',
    'seat_limit_msg'                 => 'Seat limit reached. Suspend or remove a member — or upgrade your plan — to invite new users.',

    // ─── Blade view — members table ───────────────────────────────────
    'members_title'                  => 'Team Members',
    'col_name'                       => 'Name',
    'col_email'                      => 'Email',
    'col_role'                       => 'Role',
    'col_status'                     => 'Status',
    'col_actions'                    => 'Actions',
    'row_self_suffix'                => '(you)',
    'status_suspended'               => 'Suspended',
    'status_active'                  => 'Active',
    'action_reset_password'          => 'Reset password',
    'action_reset_password_title'    => 'Send password reset email',
    'action_unsuspend'               => 'Unsuspend',
    'action_suspend'                 => 'Suspend',
    'action_remove'                  => 'Remove',
    'empty_no_members_html'          => 'No team members yet.  Use the <strong>Invite team member</strong> action above to get started.',

    // ─── Blade view — wire:confirm prompts ────────────────────────────
    'confirm_reset_password'         => 'Send a password reset link to :email?',
    'confirm_suspend'                => 'Suspend :name?  They will lose access until unsuspended.',
    'confirm_remove'                 => 'Remove :name from this workspace?',

    // ─── Role badges (Pass 22) ────────────────────────────────────────
    // Used by team-settings-page.blade.php to translate the role
    // displayed in the members table.  Covers the canonical app roles
    // that ship with the bundle.  Unknown future roles fall back to
    // ucfirst() in the view.
    'role_admin'                     => 'Admin',
    'role_owner'                     => 'Owner',
    'role_manager'                   => 'Manager',
    'role_member'                    => 'Member',
    'role_user'                      => 'User',

    // ─── Inline role editing on the member table ──────────────────────
    'change_role_label'              => 'Change role',
    'role_changed'                   => ":name's role changed to :role.",
    'role_invalid'                   => 'Invalid role.',
    'role_cannot_change_self'        => 'You cannot change your own role.',
    'role_admin_only'                => "Only an admin can change an admin's role.",
    'role_admin_not_editable'        => 'Admin roles are managed by the workspace owner, not from here.',
    'role_cannot_change_owner'       => 'The workspace owner is always an admin and cannot be changed.',

    // ─── Seat-card plan labels (Pass 22) ──────────────────────────────
    // Display name for the tenant's current plan slug in the seat-usage
    // card.  Known plan keys ship a localised label; tenant-custom
    // plans fall back to ucfirst() of the raw slug.
    'plan_free'                      => 'Free',
    'plan_starter'                   => 'Starter',
    'plan_pro'                       => 'Pro',
    'plan_business'                  => 'Business',
    'plan_enterprise'                => 'Enterprise',
    'plan_trial'                     => 'Trial',
];
