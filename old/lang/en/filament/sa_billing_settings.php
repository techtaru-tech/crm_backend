<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin BillingSettingsPage — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_billing_settings.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_billing_settings.php.
*/

return [

    // ----- Page title -----
    'title'                       => 'Payment Gateway & Lifecycle Settings',
    'navigation_label'            => 'Payment & Lifecycle',
    'tabs_outer'                  => 'Gateways',

    // ----- One-time cron setup -----
    'cron_section_heading'        => '⚙️ One-time server setup',
    'cron_section_description'    => 'For trial reminders, expiry emails, and the daily backup to actually fire, your server needs a single cron entry. Add it once and every scheduled task in the script becomes active automatically.',
    'cron_setup_step_label'       => 'Add this line to your hosting panel\'s Cron Jobs (cPanel / Plesk / DirectAdmin), or to your server\'s crontab:',
    'cron_setup_step_thats_it'    => 'That\'s it — set it once and forget it. The script handles everything else internally (when to send each email, when to expire trials, when to back up, etc.).',
    'cron_setup_step_support'     => 'If you\'re not sure how to add a cron entry, your hosting provider\'s support can do this for you in under a minute — just send them the line above.',

    // ----- Trial & lifecycle section -----
    'trial_lifecycle_description' => 'Controls how long trials last, when reminders are sent before / after expiry, and whether expired tenants are auto-suspended after a grace period. ProcessSubscriptionLifecycle reads these every cron run — no app reboot needed.',
    'trial_days_label'            => 'Default trial length (days)',
    'trial_days_suffix'           => 'days',
    'trial_days_helper'           => 'Used when the chosen plan has no per-plan trial_days. Each plan can still override this via the Plans page.',
    'trial_reminder_days_label'   => 'Trial reminder cadence (days BEFORE trial ends)',
    'reminder_placeholder'        => 'Type a day count and press Enter',
    'trial_reminder_days_helper'  => 'e.g. 7, 3, 1 → emails sent 7, 3, and 1 day before trial_ends_at. Each integer is one reminder. Empty = no reminders.',
    'post_expiry_reminder_days_label' => 'Post-expiry drip cadence (days AFTER expiry)',
    'post_expiry_reminder_days_helper' => 'e.g. 3, 7, 14 → drip emails sent 3, 7, and 14 days after expiry to recover lapsed tenants. Empty = no drip.',
    'auto_suspend_after_label'    => 'Auto-suspend after (days post-expiry)',
    'auto_suspend_after_helper'   => '0 = never auto-suspend. Otherwise, expired tenants are flipped to active=false this many days after their expiry date and emailed a final notice.',

    // ----- Enabled gateways section -----
    'enabled_gateways_description'=> 'Only the gateways you check here (and that have credentials filled in below) will be offered to tenants on the pricing page.',
    'field_enabled_gateways_label'=> 'Enabled gateways',
    'gateway_stripe'              => 'Stripe (Card)',
    'gateway_paypal'              => 'PayPal',
    'gateway_razorpay'            => 'Razorpay',
    'gateway_paystack'            => 'Paystack',
    'gateway_manual'              => 'Bank Transfer (Manual)',

    // ----- Stripe tab -----
    'tab_stripe'                  => 'Stripe',
    'test_mode'                   => 'Test mode',
    'stripe_publishable_key'      => 'Publishable Key',
    'stripe_secret_key'           => 'Secret Key',
    'webhook_signing_secret'      => 'Webhook Signing Secret',
    'stripe_webhook_helper'       => 'Optional but recommended. Point your Stripe webhook to :url',

    // ----- PayPal tab -----
    'tab_paypal'                  => 'PayPal',
    'sandbox_mode'                => 'Sandbox mode',
    'paypal_client_id'            => 'Client ID',
    'paypal_client_secret'        => 'Client Secret',
    'paypal_webhook_id'           => 'Webhook ID',
    'paypal_webhook_helper'       => 'Webhook endpoint: :url',

    // ----- Razorpay tab -----
    'tab_razorpay'                => 'Razorpay',
    'razorpay_key_id'             => 'Key ID',
    'razorpay_key_secret'         => 'Key Secret',
    'razorpay_webhook_secret'     => 'Webhook Secret',
    'razorpay_webhook_helper'     => 'Webhook endpoint: :url',

    // ----- Paystack tab -----
    'tab_paystack'                => 'Paystack',
    'paystack_public_key'         => 'Public Key',
    'paystack_secret_key'         => 'Secret Key',

    // ----- Manual bank transfer tab -----
    'tab_manual_bank'             => 'Manual Bank Transfer',
    'manual_bank_name'            => 'Bank Name',
    'manual_account_name'         => 'Account Name',
    'manual_account_number'       => 'Account Number',
    'manual_iban'                 => 'IBAN',
    'manual_swift'                => 'SWIFT / BIC',
    'manual_extra_instructions'   => 'Extra Instructions',
    'manual_extra_helper'         => 'Shown on the transfer-instructions page after a tenant picks this gateway.',

    // ----- Notifications -----
    'settings_saved'              => 'Settings saved.',
    'no_gateway_configured'       => 'No gateway is both enabled and fully configured yet.',
    'active_gateways'             => 'Active gateways: :labels',
    'stripe_mismatch_title'       => 'Stripe test-mode mismatch',
    'stripe_mismatch_body'        => 'The "Test mode" toggle is :toggle but the secret key prefix says :prefix. Stripe routes by key prefix, not the toggle — flip one of them so the two agree.',
    'toggle_on'                   => 'ON',
    'toggle_off'                  => 'OFF',

    // ----- Header actions -----
    'save_settings'               => 'Save Settings',

    // ----- Hero -----
    'hero_eyebrow'                => 'Billing',
    'hero_title'                  => 'Configure payment gateways',
    'body_intro'                  => 'Ship one or more gateways at once. Each tenant picks their preferred method on the pricing page. Stripe and PayPal cover most global traffic; Razorpay and Paystack are excellent for India and Africa respectively; Manual bank transfer works anywhere and is ideal for enterprise invoicing.',

    // Affiliate program
    'affiliate_section_heading'     => 'Affiliate Program',
    'affiliate_section_description' => 'Commission paid to tenants who refer new paying workspaces. Applied to every recurring payment a referred workspace makes; review and pay out commissions under Billing → Affiliate Commissions.',
    'affiliate_commission_label'    => 'Commission rate',
    'affiliate_commission_helper'   => 'Percentage of each referred payment booked as affiliate commission. Set to 0 to disable the affiliate program.',
];
