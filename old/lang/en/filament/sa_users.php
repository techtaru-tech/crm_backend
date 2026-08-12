<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin UserResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_users.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_users.php.
*/

return [

    // ----- User details form -----
    'primary_workspace'           => 'Primary Workspace',
    'super_admin'                 => 'Super Admin',
    'super_admin_helper'          => 'Grants access to the Super Admin panel.',

    // ----- Table columns -----
    'workspace'                   => 'Workspace',
    'sa'                          => 'SA',
    'status'                      => 'Status',
    'status_active'               => 'Active',
    'status_suspended'            => 'Suspended',

    // ----- Filters -----
    'filter_role'                 => 'Role',
    'filter_role_super_admin'     => 'Super Admin',
    'filter_role_regular_user'    => 'Regular User',

    // ----- Reset password action -----
    'reset_password'              => 'Send reset link',
    'reset_password_demo_guard'   => 'Demo: cannot reset super-admin passwords.',
    'reset_password_modal_heading'=> 'Email reset link to :email?',
    'reset_password_modal_description' => 'Valid for 60 minutes.',
    'reset_link_sent_title'       => 'Reset link sent',
    'reset_link_sent_body'        => 'Emailed :email.',
    'reset_link_failed_title'     => 'Could not send reset link',
    'password_broker_rejected'    => 'Password broker rejected (:status).',

    // ----- Suspend action -----
    'suspend'                     => 'Suspend',
    'suspend_demo_guard'          => 'Demo: cannot suspend users.',
    'suspend_modal_heading'       => 'Suspend :name?',
    'suspend_modal_description'   => 'They will lose access across every panel until unsuspended.',
    'user_suspended_title'        => 'User suspended',
    'user_suspended_body'         => ':email can no longer sign in.',

    // ----- Unsuspend action -----
    'unsuspend'                   => 'Unsuspend',
    'unsuspend_demo_guard'        => 'Demo: cannot unsuspend users.',
    'user_reactivated_title'      => 'User reactivated',
    'user_reactivated_body'       => ':email can sign in again.',

    // ----- Field labels (form + table) -----
    'name'                        => 'Name',
    'email'                       => 'Email',
    'created_at'                  => 'Created at',

    // ----- Model labels -----
    'model_label'                 => 'User',
    'plural_model_label'          => 'Users',

];
