<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin / Super-Admin notification strings (Filament toasts)
|--------------------------------------------------------------------------
|
| These are the short feedback messages shown via
| Filament\Notifications\Notification::make()->title(__('notifications.X'))
| in tenant-admin and super-admin Filament pages.
|
| Buyers translate or adapt the copy by editing this file, or by copying
| it to lang/<locale>/notifications.php and translating the values.
|
| Placeholders use Laravel's :placeholder convention; pass values via
| __('notifications.key', ['placeholder' => $value]).
|
*/

return [

    // --- Generic permission / not-found ---------------------------------
    'no_tenant_found'                      => 'No tenant found.',
    'no_workspace_active'                  => 'No workspace is currently active.',
    'no_workspace_context'                 => 'No workspace context found.',
    'permission_denied'                    => 'Permission denied',
    'permission_required'                  => 'You do not have permission.',
    'no_remove_permission'                 => 'You do not have permission to remove team members.',
    'no_suspend_permission'                => 'You do not have permission to suspend team members.',
    'cannot_remove_self'                   => 'You cannot remove yourself.',
    'cannot_suspend_self'                  => 'You cannot suspend yourself.',
    'cannot_remove_owner'                  => 'The workspace owner cannot be removed.',
    'cannot_suspend_owner'                 => 'The workspace owner cannot be suspended.',
    'admin_only_suspend_admin'             => 'Only an admin can suspend another admin.',
    'user_not_in_workspace'                => 'User not found in this workspace.',
    'no_owner_for_workspace'               => 'No owner found for this workspace',

    // --- Settings saved -------------------------------------------------
    'profile_updated'                      => 'Profile updated successfully.',
    'password_changed'                     => 'Password changed successfully.',
    'branding_saved'                       => 'Branding settings saved.',
    'email_saved'                          => 'Email settings saved.',
    'general_saved'                        => 'General settings saved.',
    'messaging_saved'                      => 'Messaging settings saved.',
    'realtime_saved'                       => 'Realtime settings saved.',
    'security_saved'                       => 'Security settings saved for this workspace.',
    'storage_saved'                        => 'Storage settings saved.',
    'availability_saved'                   => 'Availability saved',

    // --- Generic save / connection failures with :error placeholder -----
    'save_failed'                          => 'Failed to save: :error',
    'test_failed_error'                    => 'Test failed: :error',
    'storage_test_failed'                  => 'Storage test failed: :error',
    'storage_test_ok'                      => 'Storage connection is working.',

    // --- Sessions / 2FA -------------------------------------------------
    'session_revoked'                      => 'Session revoked.',
    'all_other_sessions_revoked'           => 'All other sessions revoked.',
    'cannot_revoke_current_session'        => 'You cannot revoke your current session.',
    'twofa_enabled'                        => 'Two-factor authentication enabled!',
    'twofa_disabled'                       => 'Two-factor authentication disabled.',
    'twofa_invalid_code'                   => 'Invalid verification code. Please try again.',
    'twofa_recovery_regenerated'           => 'Recovery codes regenerated.',
    'twofa_generate_secret_first'          => 'Please generate a secret first.',

    // --- Onboarding / Misc admin ---------------------------------------
    'onboarding_revisit_hint'              => 'You can revisit onboarding any time from Settings → Team.',
    'preset_loaded'                        => 'Preset loaded. Click Save to apply.',

    // --- API keys -------------------------------------------------------
    'api_key_revoked'                      => 'API key revoked.',

    // --- Connections / Webhooks ----------------------------------------
    'connection_ok'                        => 'Connection successful!',
    'connection_failed'                    => 'Connection failed!',
    'test_delivered_http'                  => 'Test delivered — HTTP :status',
    'test_failed_http'                     => 'Test failed — HTTP :status',
    'delivery_requeued'                    => 'Delivery queued for retry.',
    'webhook_requeued'                     => 'Webhook re-queued for processing',
    'webhook_source_missing'               => 'Cannot retry: source connection not found',

    // --- Meeting / Booking ---------------------------------------------
    'booking_cancelled'                    => 'Booking cancelled',
    'booking_completed'                    => 'Marked completed',
    'booking_no_show'                      => 'Marked no-show',

    // --- License --------------------------------------------------------
    'license_required'                     => 'Please enter your license key.',

    // --- Domain ---------------------------------------------------------
    'domain_saved'                         => 'Domain saved. Verification is running in the background.',
    'domain_verification_retriggered'      => 'Domain verification re-triggered.',
    'domain_already_taken'                 => 'This domain is already registered to another workspace.',

    // --- Tenant / Seat limits / Impersonation ---------------------------
    'seat_limit_updated'                   => 'Seat limit updated to :limit.',
    'already_impersonating'                => 'Already impersonating — stop the current session first',

    // --- Modules (SA) ---------------------------------------------------
    'module_enabled'                       => 'Module enabled: :name',
    'module_disabled'                      => 'Module disabled: :name',
    'module_deleted'                       => 'Module deleted: :name',
    'module_enable_failed'                 => 'Enable failed',
    'module_disable_failed'                => 'Disable failed',
    'module_delete_failed'                 => 'Delete failed',
    'module_install_failed'                => 'Install failed',
    'module_caches_cleared'                => 'Module caches cleared.',
    'module_regenerate_failed'             => 'Regenerate failed',
    'module_upload_zip_first'              => 'Please upload a zip first.',

    // --- Generic fallback (SafeAction trait) ----------------------------
    'something_went_wrong'                 => 'Something went wrong',

    // --- Notification center (livewire/notification-center.blade.php) ---
    'panel_title'                          => 'Notifications',
    'mark_all_read'                        => 'Mark all read',
    'aria_label'                           => 'Notifications',
    'dismiss'                              => 'Dismiss',
    'empty_state'                          => 'No notifications yet',
    'load_more'                            => 'Load more',

    /*
    |--------------------------------------------------------------------------
    | User-facing notification copy (app/Notifications/*.php)
    |--------------------------------------------------------------------------
    |
    | Strings rendered to the user from queueable Notification classes —
    | toMail() subject lines, headlines, body lines, action button labels,
    | toDatabase() messages shown in the bell-icon dropdown, and broadcast
    | push titles. Placeholders use Laravel's :placeholder convention.
    |
    */

    // --- Shared button labels / fallbacks -------------------------------
    'btn_view_lead'                        => 'View Lead',
    'system_fallback'                      => 'System',

    // --- ExportFailedNotification ---------------------------------------
    'export_failed_mail_subject'           => 'Leads Export Failed',
    'export_failed_mail_headline'          => 'Export Failed',
    'export_failed_mail_line_intro'        => 'Unfortunately your leads export could not be completed.',
    'export_failed_mail_line_reason'       => '<strong>Reason:</strong> :reason',
    'export_failed_mail_line_retry'        => 'Please try again. If the problem persists, contact your administrator.',
    'export_failed_db_message'             => 'Your leads export failed: :error',

    // --- IntegrationSyncFailedNotification ------------------------------
    'integration_sync_failed_broadcast_title' => 'Sync failed for :integration on :lead',
    'integration_sync_failed_push_title'   => 'Integration Sync Failed',
    'integration_sync_failed_mail_subject' => 'Integration Sync Failed: :integration',
    'integration_sync_failed_mail_headline' => 'Integration Sync Failed',
    'integration_sync_failed_mail_line_intro' => 'An integration sync has failed for one of your leads.',
    'integration_sync_failed_mail_line_integration' => '<strong>Integration:</strong> :integration',
    'integration_sync_failed_mail_line_lead' => '<strong>Lead:</strong> :name',
    'integration_sync_failed_mail_line_error' => '<strong>Error:</strong> :error',

    // --- LeadAssignedNotification ---------------------------------------
    'lead_assigned_broadcast_title'        => 'Lead assigned: :name',
    'lead_assigned_push_title'             => 'Lead Assigned to You',
    'lead_assigned_mail_subject'           => 'Lead Assigned to You',
    'lead_assigned_mail_headline'          => 'Lead Assigned to You',
    'lead_assigned_mail_line_intro'        => 'A lead has been assigned to you.',
    'lead_assigned_mail_line_lead'         => '<strong>Lead:</strong> :name',
    'lead_assigned_mail_line_assigned_by'  => '<strong>Assigned by:</strong> :name',

    // --- LeadAutomationNotification -------------------------------------
    'automation_push_title'                => 'Automation Triggered',
    'automation_mail_subject'              => 'Automation Triggered: :message',
    'automation_mail_headline'             => 'Automation Triggered',
    'automation_mail_line_intro'           => 'An automation was triggered for a lead in your pipeline.',
    'automation_mail_line_lead'            => '<strong>Lead:</strong> :name',
    'automation_mail_line_message'         => '<strong>Message:</strong> :message',

    // --- LeadReceivedNotification ---------------------------------------
    'lead_received_broadcast_title'        => 'New lead: :name',
    'lead_received_push_title'             => 'New Lead Received',
    'lead_received_mail_subject'           => 'New Lead: :name',
    'lead_received_mail_headline'          => 'New Lead Received',
    'lead_received_mail_line_intro'        => 'A new lead has been received in your pipeline.',
    'lead_received_mail_line_name'         => '<strong>Name:</strong> :name',
    'lead_received_mail_line_email'        => '<strong>Email:</strong> :email',
    'lead_received_mail_line_phone'        => '<strong>Phone:</strong> :phone',
    'lead_received_mail_line_source'       => '<strong>Source:</strong> :source',

    // --- LeadStageChangedNotification -----------------------------------
    'lead_stage_changed_broadcast_title'   => 'Lead moved to :stage: :name',
    'lead_stage_changed_push_title'        => 'Lead Stage Changed',
    'lead_stage_changed_push_body'         => ':name moved to :stage',
    'lead_stage_changed_mail_subject'      => 'Lead Stage Updated',
    'lead_stage_changed_mail_headline'     => 'Lead Stage Updated',
    'lead_stage_changed_mail_line_intro'   => 'A lead has moved to a new pipeline stage.',
    'lead_stage_changed_mail_line_lead'    => '<strong>Lead:</strong> :name',
    'lead_stage_changed_mail_line_from'    => '<strong>From:</strong> :stage',
    'lead_stage_changed_mail_line_to'      => '<strong>To:</strong> :stage',

    // --- LeadTaskReminderNotification -----------------------------------
    'task_reminder_db_message'             => 'Task reminder: :title',
    'task_reminder_mail_subject'           => 'Task reminder: :title',
    'task_reminder_mail_greeting'          => 'Task Reminder',
    'task_reminder_mail_line_intro'        => 'You have an upcoming task:',
    'task_reminder_mail_line_lead'         => 'Related lead: :name',
    'task_reminder_mail_line_due'          => 'Due: :due',
    'task_reminder_mail_line_priority'     => 'Priority: :priority',
    'task_reminder_mail_line_description'  => 'Description: :description',
    'task_reminder_mail_line_outro'        => 'Thank you for using :app.',
    'task_reminder_due_no_date'            => 'no date',
    'task_reminder_lead_fallback'          => 'a lead',
    'task_reminder_priority_normal'        => 'normal',

    // --- LeadsExportReady -----------------------------------------------
    'export_ready_db_message'              => 'Your leads export is ready for download.',
    'export_ready_push_title'              => 'Export Ready',
    'export_ready_push_body'               => 'Your leads export (:filename) is ready for download.',
    'export_ready_mail_subject'            => 'Your Export is Ready: :filename',
    'export_ready_mail_headline'           => 'Export Ready for Download',
    'export_ready_mail_line_intro'         => 'Your leads export has been generated and is ready for download.',
    'export_ready_mail_line_file'          => '<strong>File:</strong> :filename',
    'export_ready_btn_download'            => 'Download Export',

    // --- MarketplaceTemplateInstalledNotification -----------------------
    'marketplace_template_installed_message' => ':installer installed your ":template" template (:type).',

    // --- TenantDataExportReady ------------------------------------------
    'tenant_data_export_ready_message'     => 'Your data export for ":name" is ready. The download link expires in 24 hours.',

    // --- InvoiceDueReminderNotification ---------------------------------
    'invoice_due_reminder_db_message'         => 'Invoice :number is due soon.',
    'invoice_due_reminder_customer_fallback'  => 'your customer',
    'invoice_due_reminder_due_no_date'        => 'no date',
    'invoice_due_reminder_mail_subject'       => 'Invoice :number is due soon',
    'invoice_due_reminder_mail_greeting'      => 'Invoice Due Reminder',
    'invoice_due_reminder_mail_line_intro'    => 'One of your invoices is approaching its due date:',
    'invoice_due_reminder_mail_line_number'   => 'Invoice: :number',
    'invoice_due_reminder_mail_line_customer' => 'Customer: :name',
    'invoice_due_reminder_mail_line_amount'   => 'Amount: :amount',
    'invoice_due_reminder_mail_line_due'      => 'Due: :due',
    'invoice_due_reminder_btn_view'           => 'View Invoice',
    'invoice_due_reminder_mail_line_outro'    => 'Thank you for using :app.',
];
