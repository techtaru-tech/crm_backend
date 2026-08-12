<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Script-wide billing configuration. Credentials for every supported
 * gateway live here so the buyer can switch providers from the Super
 * Admin UI without editing .env or pushing code.
 *
 * Every secret is nullable — a gateway is only considered "available"
 * when its minimum required credentials are filled in (see each
 * driver's isConfigured() check).
 */
class BillingSettings extends Settings
{
    /**
     * Array of enabled gateway ids: ['stripe', 'paypal', 'razorpay', 'paystack', 'manual'].
     * The first entry is treated as the default on the pricing page.
     */
    public array $enabled_gateways = [];

    /**
     * Default trial length (in days) for newly-created workspaces.
     * Both the SA "Create Workspace" form and the self-service
     * /register flow stamp trial_ends_at = now() + this value when
     * subscription_status is 'trial' and trial_ends_at is unset
     * AND the chosen plan has trial_days = 0.  Per-plan trial_days
     * takes precedence — see PlanService::resolveTrialDays.
     */
    public int $trial_days = 14;

    /**
     * Days BEFORE trial_ends_at / subscription_ends_at on which the
     * lifecycle cron sends "ending soon" reminders.  Each value
     * triggers exactly one email per tenant per remaining-day count,
     * tracked via a flag in tenant.settings so the cron is idempotent
     * even when run multiple times per day.  Empty array = no
     * reminders.  Default [7, 3, 1] is the previous hardcoded
     * cadence.
     */
    public array $trial_reminder_days = [7, 3, 1];

    /**
     * Days AFTER trial / subscription expiry on which to send a
     * follow-up "drip" nudge.  Same idempotency guarantee as the
     * pre-expiry reminders.  Empty array = no drip.  Default
     * [3, 7, 14] sends three nudges spread over two weeks; for
     * aggressive recovery set [1, 3, 7, 14, 30] etc.
     */
    public array $post_expiry_reminder_days = [3, 7, 14];

    /**
     * Number of days after expiry on which the lifecycle cron
     * automatically suspends a tenant (sets active=false).  0 means
     * never auto-suspend — the SA suspends manually from the
     * Tenants list.  Suspended tenants are blocked by
     * EnforceSubscription middleware on their next request.
     */
    public int $auto_suspend_after_expiry_days = 0;

    /**
     * Affiliate-program commission rate, as a percentage of each
     * recurring payment made by a referred tenant.  Read by every
     * payment gateway when it books an AffiliateReferral row (see
     * AbstractGateway::commissionPercent()).  Operator-editable from
     * the Super Admin billing settings page.  20.0 mirrors the old
     * hard-coded AbstractGateway::DEFAULT_COMMISSION_PCT constant.
     */
    public float $affiliate_commission_percent = 20.0;

    /* ---------- Stripe ---------- */
    public ?string $stripe_public_key     = null;
    public ?string $stripe_secret_key     = null;
    public ?string $stripe_webhook_secret = null;
    public bool    $stripe_test_mode      = true;

    /* ---------- PayPal ---------- */
    public ?string $paypal_client_id     = null;
    public ?string $paypal_client_secret = null;
    public ?string $paypal_webhook_id    = null;
    public bool    $paypal_sandbox       = true;

    /* ---------- Razorpay ---------- */
    public ?string $razorpay_key_id        = null;
    public ?string $razorpay_key_secret    = null;
    public ?string $razorpay_webhook_secret = null;

    /* ---------- Paystack ---------- */
    public ?string $paystack_public_key  = null;
    public ?string $paystack_secret_key  = null;

    /* ---------- Manual bank transfer ---------- */
    public ?string $manual_bank_name        = null;
    public ?string $manual_account_name     = null;
    public ?string $manual_account_number   = null;
    public ?string $manual_iban             = null;
    public ?string $manual_swift            = null;
    public ?string $manual_extra_instructions = null;

    public static function group(): string
    {
        return 'billing';
    }
}
