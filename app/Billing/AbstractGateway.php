<?php

namespace App\Billing;

use App\Billing\Contracts\PaymentGateway;
use App\Models\Tenant;
use App\Services\CouponService;
use App\Services\SettingsService;
use App\Services\SubscriptionEventService;
use App\Services\TaxCalculator;
use App\Settings\BillingSettings;
use Illuminate\Support\Facades\Log;

/**
 * Shared plumbing for every payment driver: cached access to
 * BillingSettings, helpers for success/cancel URLs, and a single
 * place to log gateway errors so we never leak stack traces into
 * checkout responses.
 */
abstract class AbstractGateway implements PaymentGateway
{
    /**
     * Default affiliate commission percentage applied to recurring
     * payments.  Drivers can override via constructor or per-tenant
     * BillingSettings without touching the gateway code.
     */
    protected const DEFAULT_COMMISSION_PCT = 20.00;

    /**
     * Storage bucket for the per-subscription ordered-event guard
     * when the gateway didn't supply a subscription id (legacy
     * single-sub installs, manual gateway, etc.).
     */
    protected const ORDERED_EVENT_TENANT_BUCKET = '_tenant';

    protected BillingSettings $settings;

    public function __construct()
    {
        // BillingSettings throws SettingDoesNotExist if the Spatie
        // settings table hasn't been seeded yet (partial install,
        // migrations ran but DatabaseSeeder didn't, etc.).  Without
        // this guard every public route that resolves a gateway
        // (pricing page, public invoice pay link, SA billing settings
        // page) would 500 with an unfiltered exception.  Fall back to
        // a default-constructed BillingSettings so the page renders
        // and the operator can fix the seed from Script Settings.
        try {
            $this->settings = app(BillingSettings::class);
        } catch (\Throwable $e) {
            Log::warning('[gateway] BillingSettings unavailable — using defaults', [
                'gateway' => static::class,
                'error'   => $e->getMessage(),
            ]);
            $this->settings = new BillingSettings();
        }
    }

    /**
     * Resolve the affiliate commission rate (percent of each recurring
     * payment) every gateway books into AffiliateReferral rows.
     *
     * Reads the operator-editable BillingSettings value; falls back to
     * the DEFAULT_COMMISSION_PCT constant when the setting is missing
     * (partial install) or nonsensical (negative).
     */
    protected function commissionPercent(): float
    {
        $pct = $this->settings->affiliate_commission_percent ?? null;

        if ($pct === null || ! is_numeric($pct) || (float) $pct < 0) {
            return (float) self::DEFAULT_COMMISSION_PCT;
        }

        return (float) $pct;
    }

    protected function successUrl(Tenant $tenant): string
    {
        return \App\Support\AdminUrl::for() . '?checkout=success&gateway=' . $this->id();
    }

    protected function cancelUrl(Tenant $tenant): string
    {
        return \App\Support\AdminUrl::for() . '?checkout=cancelled&gateway=' . $this->id();
    }

    protected function logError(\Throwable $e, string $context = ''): void
    {
        Log::error('[gateway:' . $this->id() . '] ' . $context . ' ' . $e->getMessage(), [
            'exception' => $e,
        ]);
    }

    /**
     * Common post-payment tenant upgrade used by every driver once it
     * has verified a webhook event or a Checkout return.
     *
     * $actualAmount overrides the receipt amount when the caller knows
     * what the user was actually charged (e.g. annual checkout pays
     * 12× the monthly price; renewal invoice pays the cycle amount).
     * When null we fall back to the monthly $plan->price for
     * backward compatibility with callers that haven't been updated
     * to forward the gateway-reported amount yet.
     *
     * $extraMetadata is merged into the receipt metadata so callers
     * can stamp gateway-specific context (e.g. leadhub_interval =
     * year on annual Stripe checkouts) without re-issuing the receipt.
     */
    protected function markTenantActive(
        Tenant $tenant,
        string $planKey,
        ?string $externalId = null,
        ?float $actualAmount = null,
        array $extraMetadata = [],
    ): void {
        $tenant->update([
            'plan'                => $planKey,
            'subscription_status' => 'active',
        ]);

        if ($externalId) {
            app(\App\Services\SettingsService::class)
                ->forTenant($tenant)
                ->set($this->id() . '_subscription_id', $externalId);
        }

        $this->subscriptionEventService()->activated($tenant, $planKey);

        // Issue a sequentially-numbered tax receipt for the payment we
        // just confirmed.  Idempotent on (tenant, gateway, external_id)
        // so webhook retries never produce duplicates.  Best-effort: a
        // receipt-write failure does NOT roll back the activation above.
        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount = $actualAmount !== null ? (float) $actualAmount : (float) $plan->price;
            if ($amount <= 0) return;

            $metadata = array_merge(
                ['source' => $this->id() . '.checkout'],
                $extraMetadata,
            );

            $this->issueReceiptForPayment(
                tenant:       $tenant,
                planKey:      $planKey,
                externalId:   $externalId,
                actualAmount: $amount,
                currency:     (string) ($plan->currency ?? 'USD'),
                metadata:     $metadata,
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'receipt-creation');
        }
    }

    /**
     * Issue a tax receipt for a confirmed payment with the ACTUAL
     * amount charged.  Annual subscribers must see (e.g.) $948 — not
     * the monthly $79 price; recurring renewals must see the invoice
     * cycle amount, not the legacy first-cycle plan price.
     *
     * Called by markTenantActive() at activation and by per-gateway
     * renewal handlers (StripeGateway::onInvoicePaid,
     * PayPalGateway::onSubscriptionRenewal) so every successful
     * payment — first or recurring — produces exactly one receipt row.
     *
     * Idempotent on (tenant, gateway, external_id) via
     * TenantBillingReceipt::createForCheckout.  Best-effort: a
     * receipt-write failure does NOT roll back the activation/renewal.
     */
    protected function issueReceiptForPayment(
        Tenant $tenant,
        string $planKey,
        ?string $externalId,
        float $actualAmount,
        string $currency,
        array $metadata = [],
    ): void {
        try {
            if ($actualAmount <= 0) return;

            \App\Models\TenantBillingReceipt::createForCheckout(
                tenant:     $tenant,
                gateway:    $this->id(),
                externalId: $externalId,
                planKey:    $planKey,
                amount:     $actualAmount,
                currency:   $currency,
                metadata:   $metadata,
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'receipt-creation');
        }
    }

    /**
     * Settings-key prefix under `tenants.settings.billing.<key>` where
     * the per-subscription ordered-event high-water marks live.
     * Drivers that emit ordered subscription events (Stripe, PayPal)
     * override this; drivers that don't (Razorpay, Paystack, Manual)
     * leave it null and never call shouldApplyOrderedEvent / record.
     */
    protected function webhookTimestampSettingsKey(): ?string
    {
        return null;
    }

    /**
     * Returns true when the inbound event timestamp is at or after
     * the last-recorded high-water mark for this tenant + subscription.
     * Use this to drop out-of-order webhook deliveries that would
     * otherwise flip a tenant back into a stale state.  An
     * eventCreatedAt of zero (legacy callers without a timestamp)
     * bypasses the check.
     */
    protected function shouldApplyOrderedEvent(Tenant $tenant, ?string $subId, int $eventCreatedAt): bool
    {
        if ($eventCreatedAt <= 0) {
            return true;
        }

        $key = $this->webhookTimestampSettingsKey();
        if ($key === null) {
            return true;
        }

        $bucket = $this->orderedEventBucket($subId);
        $last   = (int) ($tenant->getSetting("billing.{$key}.{$bucket}") ?? 0);

        return $eventCreatedAt >= $last;
    }

    /**
     * Persist the latest event timestamp for tenant+subscription so a
     * subsequent shouldApplyOrderedEvent check can compare against it.
     * Backward-compat: a previous release stored a single int at
     * `billing.{key}` without per-sub keying — we promote that to the
     * tenant-wide bucket so old installs keep working.
     */
    protected function recordOrderedEvent(Tenant $tenant, ?string $subId, int $eventCreatedAt): void
    {
        if ($eventCreatedAt <= 0) {
            return;
        }

        $key = $this->webhookTimestampSettingsKey();
        if ($key === null) {
            return;
        }

        $bucket   = $this->orderedEventBucket($subId);
        $settings = (array) ($tenant->settings ?? []);
        $existing = $settings['billing'][$key] ?? null;

        if (is_int($existing) || (is_string($existing) && ctype_digit($existing))) {
            $settings['billing'][$key] = [
                self::ORDERED_EVENT_TENANT_BUCKET => (int) $existing,
            ];
        } elseif (! is_array($existing)) {
            $settings['billing'][$key] = [];
        }

        $settings['billing'][$key][$bucket] = $eventCreatedAt;
        $tenant->forceFill(['settings' => $settings])->save();
    }

    /**
     * Resolve the storage bucket for the per-subscription ordering
     * guard.  Real subscription ids get their own bucket; missing /
     * empty ids share the tenant-wide bucket so legacy single-sub
     * setups keep working.
     */
    protected function orderedEventBucket(?string $subId): string
    {
        $sid = $subId !== null ? trim($subId) : '';
        return $sid !== '' ? $sid : self::ORDERED_EVENT_TENANT_BUCKET;
    }

    /*
    |--------------------------------------------------------------------------
    | Lazy service accessors (M5 — testability seam)
    |--------------------------------------------------------------------------
    | Drivers historically resolved CouponService / TaxCalculator /
    | SettingsService / SubscriptionEventService inline via
    | `app(SomeService::class)` — fine in production but invisible at
    | the class boundary and impossible to substitute in unit tests
    | without booting Laravel's container.
    |
    | These four accessors lazy-resolve once and cache, giving tests a
    | clear seam (override in a subclass to inject fakes) and giving
    | static analysis a single point of dependency.  New code in
    | drivers should call `$this->couponService()` etc.; old call
    | sites can be migrated incrementally without breaking changes.
    */

    private ?CouponService $couponServiceInstance = null;
    private ?TaxCalculator $taxCalculatorInstance = null;
    private ?SettingsService $settingsServiceInstance = null;
    private ?SubscriptionEventService $subscriptionEventServiceInstance = null;

    protected function couponService(): CouponService
    {
        return $this->couponServiceInstance ??= app(CouponService::class);
    }

    protected function taxCalculator(): TaxCalculator
    {
        return $this->taxCalculatorInstance ??= app(TaxCalculator::class);
    }

    protected function settingsService(): SettingsService
    {
        return $this->settingsServiceInstance ??= app(SettingsService::class);
    }

    protected function subscriptionEventService(): SubscriptionEventService
    {
        return $this->subscriptionEventServiceInstance ??= app(SubscriptionEventService::class);
    }
}
