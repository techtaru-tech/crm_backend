<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| BillingPortal — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/billing_portal.<key>').
*/

return [
    'title'                          => 'Billing & Subscription',
    'heading'                        => 'Billing & Subscription',
    'navigation_label'               => 'Billing',

    // Subscription state subheadings
    'subheading_on_trial'            => 'You are on a free trial. Upgrade any time — your data carries over.',
    'subheading_active_paid'         => 'Your subscription is active.',
    'subheading_trial_expired'       => 'Your trial has ended. Pick a plan to continue.',
    'subheading_expired'             => 'Your subscription has lapsed. Renew to regain full access.',
    'subheading_cancelled'           => 'Your subscription is cancelled. Reactivate any time.',
    'subheading_suspended'           => 'This workspace is suspended. Contact support.',

    // View data defaults
    'state_unknown_label'            => 'Unknown',

    // Deletion flow
    'type_delete_title'              => 'Type DELETE to confirm.',
    'type_delete_body'               => 'Workspace deletion is destructive. Type the word DELETE in the confirmation field to proceed.',
    'no_workspace_title'             => 'No workspace context.',
    'auth_required_title'            => 'Authentication required.',
    'owner_only_title'               => 'Owner only.',
    'owner_only_body'                => 'Only the workspace owner can schedule deletion.',
    'password_mismatch_title'        => 'Password did not match.',
    'password_mismatch_body'         => 'Re-enter your account password to confirm workspace deletion.',
    'totp_mismatch_title'            => 'Two-factor code did not match.',
    'totp_mismatch_body'             => 'Enter a fresh 6-digit code from your authenticator app.',
    'deletion_scheduled_title'       => 'Workspace deletion scheduled',
    'deletion_scheduled_body'        => 'This workspace will be permanently deleted in :days days. You have until then to cancel from this page or the Privacy & Data page.',
    'deletion_cancelled_title'       => 'Deletion cancelled',
    'deletion_cancelled_body'        => 'Your workspace will remain active.',

    // Billing details save
    'review_details_title'           => 'Please review the billing details.',
    'review_details_default_body'    => 'Some fields are invalid.',
    'billing_country_regex'          => 'Country must be an ISO-3166-1 alpha-2 code (e.g. US, DE, GB).',
    'no_changes_title'               => 'No changes to save.',
    'billing_details_saved_title'    => 'Billing details saved.',
    'billing_details_saved_body'     => 'These will appear on every future receipt.',

    // Subscription event descriptors
    'event_trial_ends'               => 'Trial ends',
    'event_next_renewal'             => 'Next renewal',
    'event_trial_ended'              => 'Trial ended',
    'event_subscription_ended'       => 'Subscription ended',
    'event_access_ends'              => 'Access ends',

    // Gateway labels
    'gateway_stripe'                 => 'Stripe',
    'gateway_paypal'                 => 'PayPal',
    'gateway_razorpay'               => 'Razorpay',
    'gateway_paystack'               => 'Paystack',
    'gateway_manual'                 => 'Bank Transfer',

    // ─── Blade view — billing portal page ─────────────────────────────
    'error_no_workspace'             => "We couldn't resolve your workspace. Please log out and log back in.",
    'cta_choose_plan'                => 'Choose a plan',
    'section_current_plan'           => 'Current plan',
    'price_free'                     => 'Free',
    'seat_team_seats'                => 'Team seats',
    'seat_limit_reached'             => "You've reached your seat limit. Upgrade to invite more members.",
    'features_whats_included'        => "What's included",

    // ─── Feature labels (Pass 22) ─────────────────────────────────────
    // Used by billing-portal.blade.php to translate the feature keys
    // from config/plans.php into a localised "What's included" list.
    // Tenant-custom features fall back to a humanised key in the view.
    'feature_integrations'           => 'Integrations',
    'feature_automations'            => 'Automations',
    'feature_api_access'             => 'API access',
    'feature_custom_domain'          => 'Custom domain',
    'feature_white_label'            => 'White label',
    'feature_webhooks_outbound'      => 'Outbound webhooks',
    'feature_reports_advanced'       => 'Advanced reports',
    'feature_priority_support'       => 'Priority support',
    'feature_marketplace_install'    => 'Marketplace installs',
    'feature_team_collaboration'     => 'Team collaboration',
    'feature_unlimited_leads'        => 'Unlimited leads',
    'feature_sso'                    => 'Single sign-on',
    'no_plan_information'            => 'No plan information available.',
    'section_manage_subscription'    => 'Manage subscription',
    'gateway_paying_via_prefix'      => 'Paying via',
    'action_change_plan'             => 'Change plan',
    'action_update_payment_method'   => 'Update payment method & invoices',
    'action_cancel_subscription'     => 'Cancel subscription',
    'support_hint'                   => "Need help? Contact support — we'll handle billing changes for you within 24 hours.",
    'section_recent_activity'        => 'Recent activity',
    'event_subscription_activated'   => 'Subscription activated',
    'event_subscription_cancelled'   => 'Subscription cancelled',
    'event_payment_failed'           => 'Payment failed',
    'event_plan_changed'             => 'Plan changed',
    'event_notification_sent'        => 'Notification sent',
    'event_workspace_suspended'      => 'Workspace suspended',
    'event_workspace_reactivated'    => 'Workspace reactivated',
    'event_auto_suspended'           => 'Auto-suspended (post-expiry)',
    'section_available_plans'        => 'Available plans',
    'toggle_monthly'                 => 'Monthly',
    'toggle_annual'                  => 'Annual',
    'toggle_annual_save_badge'       => 'save up to 20%',
    'plan_tag_recommended'           => 'Recommended',
    'plan_tag_current'               => 'Current',
    'price_suffix_per_month'         => '/month',
    'price_suffix_per_year'          => '/year',
    'plan_save_vs_monthly'           => 'Save :pct% vs monthly',
    'preview_upgrade_strong'         => 'Switch today, pay only the diff:',
    'preview_charge_now_label'       => 'Charge now:',
    'preview_credit_applied_label'   => 'Credit applied:',
    'preview_prorated_days_one'      => ':count day of current plan',
    'preview_prorated_days_other'    => ':count days of current plan',
    'preview_downgrade_strong'       => 'Downgrade with credit:',
    'preview_account_credit_label'   => 'Account credit:',
    'preview_applied_next_invoice'   => 'Applied to your next invoice automatically.',
    'plan_action_switch'             => 'Switch',
    'plan_action_switch_annual'      => 'Switch (annual)',
    'plan_active_label'              => 'Active',
    'section_billing_details'        => 'Billing details',
    'billing_details_hint'           => 'Used on every receipt PDF. Required by AP / procurement teams in most jurisdictions.',
    'form_business_name_label'       => 'Registered business name',
    'form_vat_number_label'          => 'VAT / GST number',
    'form_country_label'             => 'Country (ISO-3166-1 alpha-2)',
    'form_country_placeholder'       => 'US',
    'form_billing_address_label'     => 'Billing address',
    'form_billing_email_label'       => 'Billing email (AP / accounting inbox)',
    'form_billing_email_placeholder' => 'ap@example.com',
    'form_save_button'               => 'Save billing details',

    // ─── Connector + interval fallback ───
    'of_connector'                   => 'of',
    'interval_month_fallback'        => 'month',

    // ─── Interval labels (slug→localized) ─────────────────────────────
    // Routed via `__('filament/billing_portal.interval_' . $slug)` so the
    // "/month" suffix and the status-banner header line render in tenant
    // locale instead of leaking the raw config/plans.php slug.
    'interval_month'                 => 'month',
    'interval_year'                  => 'year',
    'interval_week'                  => 'week',
    'interval_day'                   => 'day',
];
