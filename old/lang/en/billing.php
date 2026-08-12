<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Billing public-facing pages (resources/views/billing/*.blade.php)
|------------------------------------------------------------
| Accessed via __('billing.<key>').
*/

return [

    // Manual transfer instructions
    'transfer_instructions'    => 'Transfer instructions',

    // ─── Razorpay launch page (resources/views/billing/razorpay-launch.blade.php) ──
    'razorpay_page_title'      => 'Launching Razorpay…',
    'razorpay_launching_msg'   => 'Launching Razorpay checkout…',
    'razorpay_app_description' => 'Subscription payment',

    // ─── Manual-instructions page (resources/views/billing/manual-instructions.blade.php) ──
    'manual_page_title'        => 'Payment Instructions',
    'manual_amount_due_label'  => 'Amount due',
    'manual_back_to_workspace' => 'Back to workspace',

    // ─── Gateway labels (App\Billing\Gateways\*::label()) ──
    // Brand-only labels (PayPal, Paystack, Razorpay) are left literal
    // in their gateway classes; only Stripe + Manual contain
    // English words that need localisation.
    'gateway_stripe_label'     => 'Stripe (Card)',
    'gateway_manual_label'     => 'Bank Transfer (Manual)',

    // ─── TenantSubscriptionState::label() ──
    // Human-readable badges for the workspace's overall subscription
    // state (combines Tenant.active + subscription_status + trial /
    // sub end-date columns into one derived label).
    'state_suspended'          => 'Suspended',
    'state_trial'              => 'Trial',
    'state_trial_expired'      => 'Trial expired',
    'state_active'             => 'Active',
    'state_pending_payment'    => 'Awaiting payment',
    'state_cancelled'          => 'Cancelled',
    'state_expired'            => 'Expired',
    'state_pending_deletion'   => 'Scheduled for deletion',
    'state_unknown'            => 'Unknown',
];
