<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Mailable subject lines and shared email copy
|------------------------------------------------------------
| Accessed via __('mail.<key>', [...]).
|
| Snake_case keys, grouped by Mailable / Blade file.  All
| reviewer-facing English strings in resources/views/emails/*
| and app/Mail/*::envelope() route through here so CodeCanyon
| Item 1 (no hardcoded user-visible text) holds.
*/

return [

    // ─── Shared layout (resources/views/emails/layout.blade.php) ──
    'layout_default_title'        => 'LeadHub',
    'layout_preheader_fallback'   => ':app notification',
    'layout_footer_default'       => 'You received this email because you are a user of :app.',

    // ─── Meeting booked (resources/views/emails/meeting/booked.blade.php) ──
    'meeting_booked_subject_host'        => 'New booking: :name with :guest on :when',
    'meeting_booked_subject_guest'       => 'Your meeting is confirmed: :name on :when',
    'meeting_booked_default_name'        => 'Meeting',
    'meeting_booked_title'               => 'Meeting confirmed',
    'meeting_booked_heading_host'        => 'New booking received',
    'meeting_booked_heading_guest'       => 'Your meeting is confirmed',
    'meeting_booked_label_when'          => 'When',
    'meeting_booked_label_guest'         => 'Guest',
    'meeting_booked_label_phone'         => 'Phone',
    'meeting_booked_label_host'          => 'Host',
    'meeting_booked_label_location'      => 'Location',
    'meeting_booked_label_notes'         => 'Notes',
    'meeting_booked_location_google_meet' => 'Google Meet (link will follow)',
    'meeting_booked_location_zoom'       => 'Zoom (link will follow)',
    'meeting_booked_location_phone'      => 'Phone call',
    'meeting_booked_location_in_person'  => 'In person',
    'meeting_booked_location_default'    => 'Details below',
    'meeting_booked_btn_reschedule'      => 'Reschedule',
    'meeting_booked_btn_cancel'          => 'Cancel',
    'meeting_booked_ics_note'            => 'Calendar invite (.ics) attached — open it to add to your calendar.',

    // ─── Meeting cancelled (resources/views/emails/meeting/cancelled.blade.php) ──
    'meeting_cancelled_subject'   => 'Meeting cancelled: :name on :when',
    'meeting_cancelled_default_name' => 'Meeting',
    'meeting_cancelled_title'     => 'Meeting cancelled',
    'meeting_cancelled_body'      => 'The meeting originally scheduled for :when (:tz) has been cancelled.',
    'meeting_cancelled_reason'    => 'Reason:',
    'meeting_cancelled_book_again_intro' => 'Need another time?',
    'meeting_cancelled_book_again_link'  => 'Book again',

    // ─── Portal magic link (resources/views/emails/portal-magic-link.blade.php) ──
    'portal_magic_link_subject'   => 'Your :app portal login link',
    'portal_magic_link_greeting'  => 'Hi :name,',
    'portal_magic_link_default_name' => 'there',
    'portal_magic_link_body'      => "Here's your secure sign-in link. Click the button below to access your account. This link is valid for 30 minutes and can only be used once.",
    'portal_magic_link_button'    => 'Sign in',
    'portal_magic_link_ignore'    => "If you didn't request this link, you can safely ignore this email.",
    'portal_magic_link_fallback'  => 'Link not working? Paste into your browser:',

    // ─── Tenant welcome (resources/views/emails/tenant-welcome.blade.php) ──
    'tenant_welcome_subject'      => 'Your :workspace workspace is ready',
    'tenant_welcome_hello'        => 'Hello,',
    'tenant_welcome_intro'        => 'Your workspace :workspace is ready on :app.',
    'tenant_welcome_user_set_password' => 'You can sign in any time with the email and password you chose during sign-up.',
    'tenant_welcome_admin_created'     => 'An administrator created this workspace for you. Use the button below to set a password and log in for the first time.',
    'tenant_welcome_workspace_label'   => 'Workspace:',
    'tenant_welcome_email_label'       => 'Email:',
    'tenant_welcome_button_set_password' => 'Set Your Password & Log In',
    'tenant_welcome_button_login'        => 'Log In to Your Workspace',
    'tenant_welcome_setup_expires'       => 'This setup link is valid for 60 minutes. If it expires, just use the',
    'tenant_welcome_forgot_password'     => '"Forgot password"',
    'tenant_welcome_setup_expires_suffix' => 'link on the login page.',
    'tenant_welcome_ignore'              => 'If you did not expect this email, you can safely ignore it.',

    // ─── Invitation (resources/views/emails/invitation.blade.php) ──
    'invitation_subject'          => "You've been invited to :workspace on :app",
    'invitation_default_inviter' => 'A team member',
    'invitation_hello'           => 'Hello,',
    'invitation_body'            => ':inviter has invited you to join :workspace on :app as a :role.',
    'invitation_button'          => 'Accept Invitation',
    'invitation_expiry'          => 'This invitation will expire in 7 days.',
    'invitation_ignore'          => 'If you did not expect this invitation, you can safely ignore this email.',

    // ─── Password reset (resources/views/emails/password-reset.blade.php) ──
    'password_reset_subject'     => 'Reset your :app password',
    'password_reset_default_name' => 'there',
    'password_reset_greeting'    => 'Hi :name,',
    'password_reset_intro'       => 'We received a request to reset the password for your :app account. Click the button below to choose a new one.',
    'password_reset_button'      => 'Reset my password',
    'password_reset_expires'     => "This link expires in :minutes minutes.  If you didn't request a password reset, you can safely ignore this email — your password will stay the same.",
    'password_reset_fallback'    => "If the button above doesn't work, paste this URL into your browser:",

    // ─── Payment failed (resources/views/emails/payment-failed.blade.php) ──
    'payment_failed_subject'     => 'Action needed: payment failed for :workspace',
    'payment_failed_heading'     => 'Payment failed',
    'payment_failed_attempt'     => 'Attempt :attempt — please update your payment method.',
    'payment_failed_greeting'    => 'Hi there,',
    'payment_failed_body'        => "We tried to charge the card on file for your :workspace subscription and the payment didn't go through.",
    'payment_failed_amount_label'      => 'Amount due',
    'payment_failed_next_retry_label'  => 'Next automatic retry',
    'payment_failed_cta_body'    => "To avoid interruption, please update your payment method as soon as possible. We'll automatically retry the charge after you update the card.",
    'payment_failed_button'      => 'Update Payment Method',
    'payment_failed_help'        => 'Common reasons a charge fails: expired card, insufficient funds, or a bank-level fraud hold. If you need help, reply to this email.',

    // ─── Plan changed (resources/views/emails/plan-changed.blade.php) ──
    'plan_changed_subject_upgrade'   => "You've been upgraded to :plan",
    'plan_changed_subject_downgrade' => 'Your plan has been changed to :plan',
    'plan_changed_subject_default'   => 'Your plan has been updated to :plan',
    'plan_changed_heading_upgrade'   => "You're now on :plan",
    'plan_changed_heading_downgrade' => 'Plan updated to :plan',
    'plan_changed_heading_default'   => 'Plan updated to :plan',
    'plan_changed_greeting'      => 'Hi there,',
    'plan_changed_body'          => 'Your plan for :workspace on :app has been updated.',
    'plan_changed_previous_label' => 'Previous plan',
    'plan_changed_new_label'     => 'New plan',
    'plan_changed_upgrade_note'  => 'New features and higher limits are already unlocked across your workspace. Log in any time to take advantage of them.',
    'plan_changed_downgrade_note' => 'Your new plan is active immediately. Some features from your previous plan may no longer be available — check the billing page for details.',
    'plan_changed_button'        => 'View Billing Dashboard',

    // ─── Plan slug labels (Pass 22) ────────────────────────────────────
    // Used by plan-changed.blade.php to translate the old/new plan slug
    // shown in the previous-plan / new-plan rows. Unknown future plans
    // fall back to ucfirst() in the view.
    'plan_value_free'            => 'Free',
    'plan_value_starter'         => 'Starter',
    'plan_value_pro'             => 'Pro',
    'plan_value_business'        => 'Business',
    'plan_value_enterprise'      => 'Enterprise',
    'plan_value_trial'           => 'Trial',

    // ─── Billing cycle labels (Pass 22) ────────────────────────────────
    // Used by subscription-activated.blade.php to translate the cycle
    // slug (monthly|yearly) interpolated into subscription_activated_billing_cycle.
    'billing_cycle_monthly'      => 'Monthly',
    'billing_cycle_yearly'       => 'Yearly',
    'billing_cycle_quarterly'    => 'Quarterly',

    // ─── Subscription activated (resources/views/emails/subscription-activated.blade.php) ──
    'subscription_activated_subject' => "Welcome to :plan — you're all set",
    'subscription_activated_heading' => 'Welcome to :plan 🎉',
    'subscription_activated_greeting' => 'Hi there,',
    'subscription_activated_body' => 'Your subscription for :workspace on :app is now active. Everything you built during your trial carries over — leads, pipelines, automations, integrations.',
    'subscription_activated_billing_cycle' => 'Billing cycle: :cycle.',
    'subscription_activated_button' => 'View Billing Dashboard',
    'subscription_activated_footer' => "If you have any questions about your plan, just reply to this email and we'll take care of it.",

    // ─── Subscription cancelled (resources/views/emails/subscription-cancelled.blade.php) ──
    'subscription_cancelled_subject' => 'Your :workspace subscription has been cancelled',
    'subscription_cancelled_heading' => 'Your subscription has been cancelled',
    'subscription_cancelled_greeting' => 'Hi there,',
    'subscription_cancelled_intro'   => "We've cancelled your subscription for :workspace on :app.",
    'subscription_cancelled_ends_at' => "You'll continue to have full access until :date. After that, the workspace will be paused and you'll need to reactivate to keep using it.",
    'subscription_cancelled_immediate' => 'Access has been paused effective immediately.',
    'subscription_cancelled_data_safe' => 'Your data — leads, pipelines, automations — stays safe on our servers. If you change your mind within 90 days, you can reactivate with a single click and pick up right where you left off.',
    'subscription_cancelled_reason'   => 'Reason on file: :reason',
    'subscription_cancelled_button'   => 'Reactivate Subscription',
    'subscription_cancelled_footer'   => 'Sorry to see you go. If there was something we could have done better, reply to this email and let us know.',

    // ─── Subscription expired (resources/views/emails/subscription-expired.blade.php) ──
    'subscription_expired_subject' => 'Your :workspace subscription has expired',
    'subscription_expired_heading' => 'Your subscription has expired',
    'subscription_expired_greeting' => 'Hi there,',
    'subscription_expired_body'    => 'Your subscription for :workspace on :app has expired. Admin panel access has been paused, but your data is still here and waiting.',
    'subscription_expired_reactivate' => "Reactivate whenever you're ready to continue where you left off.",
    'subscription_expired_button'  => 'Reactivate Subscription',
    'subscription_expired_footer'  => 'Questions about billing? Just reply to this email.',

    // ─── Trial ending soon (resources/views/emails/trial-ending-soon.blade.php) ──
    'trial_ending_soon_subject_tomorrow' => 'Your :workspace trial ends tomorrow',
    'trial_ending_soon_subject_days'     => 'Your :workspace trial ends in :days days',
    'trial_ending_soon_heading_one'  => 'Your trial ends in :days day',
    'trial_ending_soon_heading_other' => 'Your trial ends in :days days',
    'trial_ending_soon_greeting'    => 'Hi there,',
    'trial_ending_soon_body'        => 'Just a friendly heads-up — your free trial of :workspace on :app ends on :ends_at. Upgrade now to keep all your leads, pipelines, and automations running without interruption.',
    'trial_ending_soon_after'       => 'After your trial ends, access to the admin panel will be paused until you choose a plan. None of your data will be deleted.',
    'trial_ending_soon_button'      => 'Choose Your Plan',
    'trial_ending_soon_footer'      => "Questions? Just reply to this email and we'll help you pick the right plan.",

    // ─── Trial expired (resources/views/emails/trial-expired.blade.php) ──
    'trial_expired_subject' => 'Your :workspace trial has ended',
    'trial_expired_heading' => 'Your trial has ended',
    'trial_expired_greeting' => 'Hi there,',
    'trial_expired_body'   => "Your free trial of :workspace on :app has now ended. Access to the admin panel is paused until you choose a plan — but don't worry, all your leads, forms, and settings are safe.",
    'trial_expired_pick_plan' => "Pick a plan whenever you're ready and you'll be back to full access in seconds.",
    'trial_expired_button' => 'Reactivate Your Workspace',
    'trial_expired_footer' => "Need help choosing? Reply to this email — we're happy to help.",

    // ─── Workspace suspended (resources/views/emails/workspace-suspended.blade.php) ──
    'workspace_suspended_subject' => 'Your :workspace workspace has been suspended',
    'workspace_suspended_heading' => 'Your workspace has been suspended',
    'workspace_suspended_greeting' => 'Hi there,',
    'workspace_suspended_body'    => 'Your :workspace workspace on :app has been suspended after extended inactivity following the end of your subscription. All members have been signed out of the admin panel.',
    'workspace_suspended_data_safe' => 'Your data is safe — leads, forms, automations, and settings are all preserved. Reactivating is one click away: pick a plan and your team is back in within seconds.',
    'workspace_suspended_button'  => 'Reactivate Your Workspace',
    'workspace_suspended_footer'  => "If this looks like a mistake or you need help getting back in, just reply to this email and we'll sort it out.",

    // ─── Tenant erasure requested (resources/views/emails/tenant-erasure-requested.blade.php) ──
    'tenant_erasure_requested_subject' => 'Your :workspace workspace will be deleted in :days days',
    'tenant_erasure_requested_heading' => 'Workspace deletion scheduled',
    'tenant_erasure_requested_greeting' => 'Hi :name,',
    'tenant_erasure_requested_intro'   => "We've received your request to delete the :workspace workspace on :app. Your data — every lead, form, automation, integration, and setting — will be permanently erased in :days days. This action cannot be undone once the cool-off window closes.",
    'tenant_erasure_requested_window'  => 'During the :days-day window your workspace is suspended — sign-in is blocked, but every record is held intact in case you change your mind. You can cancel the deletion any time before the window closes from the Privacy & Data page.',
    'tenant_erasure_requested_button'  => 'Cancel Deletion',
    'tenant_erasure_requested_footer'  => 'Didn\'t request this? Click "Cancel Deletion" above immediately and contact support — we\'ll lock down the workspace and investigate. This message satisfies our notice obligations under GDPR Article 17 (Right to Erasure).',

    // ─── Test email (resources/views/emails/test.blade.php) ──
    'test_subject'    => 'Test Email — :app',
    'test_heading'    => 'Email Configuration Test',
    'test_greeting'   => 'Hi :name,',
    'test_body'       => 'This is a test email from :app. If you received this, your email settings are configured correctly.',
    'test_continued'  => 'You can now send branded emails from your workspace.',
    'test_button'     => 'Open Dashboard',

    // ─── Invoice send (app/Filament/Resources/InvoiceResource.php) ──
    'invoice_send_subject' => 'Invoice :number',
    'invoice_send_body'    => "Hi :name,\n\nInvoice :number is ready: :url\n\nThank you.",

    // ─── Quote send (app/Filament/Resources/QuoteResource.php) ──
    'quote_send_subject'   => 'Quote :number',
    'quote_send_body'      => "Hi :name,\n\nYour quote is ready: :url\n\nRegards,",

    // ─── Quote send for signature (app/Filament/Resources/QuoteResource/Pages/ViewQuote.php) ──
    'quote_send_review_subject' => 'Quote :number — please review',
    'quote_send_review_body'    => "Hi :name,\n\nYour quote is ready for review and signature:\n:url\n\nThank you.",

    // ─── Notification digest (app/Console/Commands/SendNotificationDigest.php) ──
    'digest_subject'                  => 'Your :app Notification Digest — :datetime',
    'digest_heading'                  => ':app Notification Digest',
    'digest_intro_lede'               => "Hi :name, here's what you missed in the last hour",
    'digest_col_type'                 => 'Type',
    'digest_col_details'              => 'Details',
    'digest_col_when'                 => 'When',
    'digest_view_button'              => 'View in :app',
    'digest_footer_explainer'         => "You're receiving this because you set notifications to hourly digest.",
    'digest_manage_preferences_link'  => 'Manage preferences',
    'digest_fallback_message'         => 'Notification',

    // ─── Meeting ICS fallbacks (app/Mail/MeetingBookedMail.php, MeetingCancelledMail.php) ──
    'meeting_default_name'   => 'Meeting',
    'host_default_name'      => 'Host',
    'meeting_description'    => 'Meeting with :host. Reschedule or cancel: :url',
    // Filename of the .ics attachment buyer sees in their email client.  Use a
    // safe-slug form (no spaces or punctuation other than dash) so all email
    // clients accept the filename unmodified.  Pass-33 i18n fix — without this
    // the English literal "meeting-" prefix leaked into non-EN buyer inboxes.
    'meeting_ics_filename'   => 'meeting',

    // ─── Onboarding drip series (app/Mail/OnboardingDripMail.php) ──
    'drip_day_1_heading'  => 'Welcome aboard',
    'drip_day_1_body'     => "We're glad you're here. The fastest way to see if this CRM fits your workflow is to add a single lead and walk it from inbox to won.\n\nTakes about 90 seconds. Click below and you're off.",
    'drip_day_1_cta'      => 'Add my first lead',

    'drip_day_3_heading'  => 'How are you finding it?',
    'drip_day_3_body'     => "Two days in.  Most teams get stuck on one of these:\n\n• Setting up the right pipeline stages → Settings → Pipelines\n• Connecting their existing email → Settings → Email\n• Importing leads from a spreadsheet → Leads → Import\n\nIf you've hit any of these (or something else), reply to this email — we read every reply.",
    'drip_day_3_cta'      => 'Open my dashboard',

    'drip_day_5_heading'  => 'The 3 automations every team turns on in week 1',
    'drip_day_5_body'     => "Most CRMs are passive — leads sit there until someone notices.  These three automations do the noticing for you:\n\n1. Auto-assign new leads round-robin so nothing falls through cracks\n2. Notify Slack on hot leads so reps don't have to refresh\n3. Re-engage cold leads after 7 days with a soft check-in email\n\nAll three take under 5 minutes to set up.",
    'drip_day_5_cta'      => 'Browse automations',

    'drip_day_7_heading'  => 'A week in — quick check',
    'drip_day_7_body'     => "How's it going?\n\nIf the CRM is paying for itself already (you've added leads, your team is using it, you're closing deals you'd otherwise have lost): perfect — your trial converts to a paid plan automatically when it ends, no action needed.\n\nIf you're still on the fence: reply to this email with what's missing.  We've shipped 14 features from cancellation feedback in the past 6 months.",
    'drip_day_7_cta'      => 'See plans',

    'drip_default_heading' => 'A note from your CRM',
    'drip_default_body'    => 'Hope you\'re finding everything useful so far.',
    'drip_default_cta'     => 'Open my dashboard',

];
