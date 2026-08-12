<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin TenantResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_tenants.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_tenants.php.
*/

return [

    // ----- Resource labels -----
    'workspace'                       => 'Workspace',
    'workspaces'                      => 'Workspaces',

    // ----- Workspace details form -----
    'reserved_slug_error'             => '":value" is reserved. Pick a different workspace slug.',
    'workspace_url_helper'            => 'Workspace URL: :url — also used for public landing pages. Auto-filled from name.',
    'max_seats_helper'                => 'Hard cap on team members.',
    'subscription_status_helper'      => 'Suspension is set via the "Suspend" button — not here. Trial expired and Expired are normally set automatically by the lifecycle cron.',
    'trial_ends_at_label'             => 'Trial ends at',
    'trial_ends_at_helper'            => 'When the free trial ends. Auto-filled from the plan\'s trial_days when you pick a plan.',
    'subscription_ends_at_label'      => 'Subscription ends at',
    'subscription_ends_at_helper'     => 'Next billing date for active subs, or the date access ends for cancelled/expired ones.',

    // ----- Subscription status options -----
    'status_trial'                    => 'Trial',
    'status_trial_expired'            => 'Trial expired',
    'status_active_paid'              => 'Active (paid)',
    'status_cancelled'                => 'Cancelled',
    'status_expired'                  => 'Expired',
    'status_active'                   => 'Active',
    'status_suspended'                => 'Suspended',
    'status_unknown'                  => 'Unknown',

    // ----- Tenant admin section -----
    'tenant_admin_description'        => 'The admin user who will own and manage this workspace.',
    'admin_name'                      => 'Admin Name',
    'admin_email'                     => 'Admin Email',
    'admin_password_mode'             => 'Password setup',
    'admin_password_mode_email_link'  => 'Email the admin a setup link (recommended)',
    'admin_password_mode_generate'    => 'Auto-generate + show here',
    'admin_password_mode_manual'      => 'Set password now',
    'admin_password'                  => 'Password',
    'admin_password_helper'           => 'At least 10 characters. Communicate securely to the admin.',

    // ----- Table columns -----
    'owner'                           => 'Owner',
    'owner_email'                     => 'Owner Email',
    'status_column'                   => 'Status',
    'seats'                           => 'Seats',
    'trial_ends'                      => 'Trial ends',
    'sub_ends'                        => 'Sub ends',

    // ----- Filters -----
    'filter_suspension'               => 'Suspension',

    // ----- Suspend action -----
    'suspend'                         => 'Suspend',
    'suspend_modal_heading'           => 'Suspend workspace',
    'suspend_modal_description'       => 'Suspend ":name"? All members lose access on their next request and see the subscription-required page. This is reversible.',
    'suspend_reason_label'            => 'Reason (optional, internal — for the audit log)',
    'suspend_demo_guard'              => 'Demo: cannot suspend tenants (would send a real notification email).',
    'suspend_notification_title'      => 'Workspace ":name" suspended',
    'suspend_notification_body_base'  => 'Members will be redirected on their next request.',
    'suspend_notification_body_owner_notified'  => ' Owner notified at :email.',
    'suspend_notification_body_owner_failed'    => ' Owner email failed to send (see logs).',
    'suspend_notification_body_no_owner'        => ' Tenant has no owner — no notification sent.',

    // ----- Reactivate action -----
    'reactivate'                      => 'Reactivate',
    'reactivate_modal_heading'        => 'Reactivate workspace',
    'reactivate_modal_description'    => 'Reactivate ":name"? Members regain access immediately on their next request.',
    'reactivate_demo_guard'           => 'Demo: cannot reactivate tenants.',
    'reactivate_notification_title'   => 'Workspace ":name" reactivated',

    // ----- Impersonate action -----
    'impersonate'                     => 'Impersonate',
    'impersonate_modal_heading'       => 'Impersonate Tenant Admin',
    'impersonate_modal_description'   => 'You will be signed in as ":owner_name" (:owner_email) in the ":tenant_name" workspace. All actions will be performed as this user. You can return to Super Admin at any time via the banner.',
    'impersonate_demo_guard'          => 'Demo: impersonation is disabled.',

    // ----- CreateTenant page notifications -----
    'workspace_created'               => 'Workspace created',
    'workspace_created_password_body' => "Password for :email: \n\n  :password\n\nCopy this now — it is not shown again.",
    'workspace_created_manual_body'   => 'The admin password you chose is active. Share it securely with :email.',
    'workspace_created_email_body'    => 'A setup link has been emailed to :email',
    'workspace_created_email_failed_title' => 'Workspace created but email failed',
    'workspace_created_email_failed_body'  => 'The workspace was created, but the setup email could not be sent to :email. The user can use the \'Forgot password\' link on the login page to gain access.',
    'workspace_created_existing_user' => 'Existing user :email has been assigned as the admin. No setup email was sent.',

    // ----- Field labels (form + table) -----
    'name'                            => 'Name',
    'slug'                            => 'Slug',
    'max_seats'                       => 'Max seats',
    'plan'                            => 'Plan',
    'subscription_status'             => 'Subscription status',
    'created_at'                      => 'Created at',

    // ----- Model labels -----
    'model_label'                     => 'Tenant',
    'plural_model_label'              => 'Tenants',

];
