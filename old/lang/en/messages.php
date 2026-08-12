<?php

return [
    'app'       => 'Application',
    'dashboard' => 'Dashboard',
    'save'      => 'Save',
    'cancel'    => 'Cancel',
    'delete'    => 'Delete',
    'edit'      => 'Edit',
    'create'    => 'Create',
    'search'    => 'Search',
    'loading'   => 'Loading…',
    'yes'       => 'Yes',
    'no'        => 'No',
    'back'      => 'Back',
    'submit'    => 'Submit',
    'confirm'   => 'Confirm',
    'success'   => 'Success',
    'error'     => 'Error',
    'warning'   => 'Warning',

    'billing_unknown_plan'          => 'Unknown plan [:plan].',
    'calendar_unsupported_provider' => 'Unsupported provider: :provider',
    'registration_throttled'        => 'Too many sign-up attempts. Try again in :seconds s.',

    // ─── DemoMode guard / abort copy (live demo lockdown) ───
    'demo_mode_title'            => '🛡️ Demo Mode',
    'demo_action_disabled_body'  => 'This action is disabled in the live demo. Get your own copy to unlock everything.',
    'demo_get_leadhub'           => 'Get :app',
    'demo_action_disabled_short' => 'This action is disabled in the live demo.',

    // ─── License-required block screen (EnforceLicense middleware) ───
    'license_required_short'              => 'License required.',
    'license_required_title'              => ':app — License required',
    'license_required_heading'            => 'License required',
    'license_required_lead'               => 'Your :app license needs to be re-verified before the admin panel can be used again.',
    'license_required_reason_label'       => 'Reason',
    'license_required_step_codecanyon'    => 'Sign in to your CodeCanyon account and open Downloads.',
    'license_required_step_purchase_code' => 'Copy the purchase code from the License certificate.',
    'license_required_step_paste_settings' => 'Paste it into Super Admin → Settings → License and click Verify.',
    'license_required_cta_settings'       => 'Open License Settings',
    'license_required_cta_codecanyon'     => 'Go to CodeCanyon',
    'license_required_item_label'         => 'CodeCanyon Item',

    // ─── Enforce2Fa middleware (JSON 403 for mobile/API) ───
    'two_factor_required' => 'Two-factor authentication is required. Please enable 2FA in your account settings.',

    // ─── Billing controller errors ───
    'billing_checkout_failed'    => 'Checkout failed.',
    'billing_portal_stripe_only' => 'Customer Portal is only available for Stripe.',
    'billing_portal_unavailable' => 'Customer Portal is unavailable. Please complete a Stripe checkout first or contact support.',

    // ─── Calendar OAuth errors ───
    'calendar_connection_not_found' => 'Connection not found.',
    'calendar_oauth_no_session'     => 'Calendar OAuth callback requires an authenticated session.',
    'oauth_state_mismatch'          => 'OAuth state mismatch — possible CSRF attempt.',
    'calendar_disconnected_success' => 'Calendar disconnected.',

    // ─── Invitation errors ───
    'invitation_invalid_or_expired' => 'This invitation is invalid or has expired.',

    // ─── Tenant scope errors ───
    'no_tenant_assigned'      => 'Your account is not associated with any workspace.',
    'session_revoked'         => 'Your session has been revoked. Please log in again.',
    'no_workspace_resolved'   => 'No workspace resolved.',
    'no_workspace_found'      => 'No workspace found for :host.',

    // ─── Data export controller (GDPR Art. 20) ───
    'export_link_invalid'      => 'Download link expired or invalid.',
    'export_link_expired'      => 'Download link has expired.',
    'export_link_wrong_user'   => 'This download link belongs to a different user.',
    'export_file_unavailable'  => 'Export file is no longer available. Please request a new export.',

    // ─── Portal (customer dashboard) ───
    'file_type_not_allowed'     => 'File type not allowed.',
    'portal_magic_link_invalid' => 'This login link is invalid, expired, or has already been used. Please request a new one.',
    'portal_file_uploaded'      => 'File uploaded.',

    // ─── Impersonation & super admin ───
    'only_super_admins_impersonate'   => 'Only super admins can impersonate.',
    'impersonate_no_owner'            => 'This workspace has no owner to impersonate.',
    'impersonate_already_active'      => 'You are already impersonating. Stop the current session first.',
    'access_denied_super_admin_only'  => 'Access denied. Super Admin only.',
    'signed_in_as_super_admin_info'   => "You're signed in as a super admin. Use the Impersonate action on a tenant to access its workspace.",

    // ─── Security middleware ───
    'access_denied_ip_not_whitelisted' => 'Access denied: your IP address is not whitelisted for this workspace.',
    'forbidden_generic'                => 'Forbidden.',

    // ─── Lead attachment guard ───
    'attachment_disk_not_allowed' => 'Attachment disk is not on the allowlist.',

    // ─── Public quote (customer-facing) ───
    'quote_already_accepted'                  => 'This quote has already been accepted.',
    'quote_already_accepted_cannot_decline'   => 'This quote has already been accepted and cannot be declined.',
    'quote_response_recorded'                 => 'Your response has been recorded. Thank you.',

    // ─── Public invoice (customer-facing) ───
    'invoice_already_paid'             => 'This invoice has already been paid.',
    'invoice_pay_manual_instructions'  => 'Please transfer the amount using the bank details on this page. Your invoice will be marked as paid once the tenant reconciles the payment.',

    // ─── Integration OAuth (CRM/marketing) ───
    'integration_oauth_unavailable'    => 'OAuth not available for :type. Please configure client_id and client_secret first.',
    'integration_oauth_state_mismatch' => 'OAuth state mismatch. Please try again.',
    'integration_oauth_denied'         => 'OAuth denied: :reason',
    'integration_oauth_no_code'        => 'No authorization code received.',
    'integration_oauth_exchange_failed'=> 'Token exchange failed: :error',
    'integration_oauth_connected'      => ':label connected successfully via OAuth!',

    // ─── Lead-source OAuth connections ───
    'oauth_not_configured_for_source'  => 'OAuth is not configured for :source. Please add client_id and client_secret first.',
    'oauth_session_expired'            => 'OAuth session expired. Please try again.',
    'oauth_state_invalid'              => 'Invalid OAuth state. Please try again.',
    'oauth_authorization_denied'       => 'OAuth authorization denied: :reason',
    'oauth_connection_not_found'       => 'Connection not found or authorization code missing.',
    'oauth_token_exchange_failed'      => 'Token exchange failed: :error',
    'oauth_token_retrieval_failed'     => 'Failed to retrieve access tokens.',
    'oauth_connected_success'          => 'Successfully connected via OAuth!',

    // ─── Public widget submission ───
    'widget_not_found'         => 'Widget not found',
    'widget_submission_failed' => 'Submission failed',

    // ─── Public form (reCAPTCHA) ───
    'recaptcha_token_missing'      => 'reCAPTCHA token missing.',
    'recaptcha_spam_check_failed'  => 'Spam check failed.',

    // ─── Public booking endpoints ───
    'booking_invalid_datetime'             => 'Invalid datetime.',
    'booking_time_advance_notice_failed'   => 'That time no longer meets the advance-notice requirement.',
    'booking_time_too_far_future'          => 'That time is too far in the future.',
    'booking_slot_taken'                   => 'That slot was just booked. Please pick another time.',

    // ─── InvitationService: MAIL=log warning notification ───
    'email_logged_title' => 'Email logged, not sent',
    'email_logged_body'  => "Mail driver is set to 'log' so the invitation email won't reach :email. Copy this signed link and share it manually:\n\n:url",

    // ─── PasswordSetupController: post-setup welcome notification ───
    'password_set_title' => 'Password set',
    'password_set_body'  => 'Welcome! Your account is ready.',

    // ─── InvitationController: post-accept welcome notification ───
    'welcome_to_app'         => 'Welcome to :app',
    'joined_workspace_body'  => "You've joined :workspace. Here's your dashboard.",
    'workspace_fallback'     => 'your workspace',

    'auth' => [
        'email'     => 'Email',
        'password'  => 'Password',
        'sign_in'   => 'Sign in',
        'sign_out'  => 'Sign out',
        'register'  => 'Create account',
    ],

    'onboarding' => [
        'subjects' => [
            'day_1'    => 'Welcome to :workspace — start with one lead',
            'day_3'    => 'How are things going at :workspace?',
            'day_5'    => '3 automations every team turns on in week 1',
            'day_7'    => 'Quick check-in: is :workspace earning its keep?',
            'fallback' => 'A note from your CRM',
        ],
    ],
];
