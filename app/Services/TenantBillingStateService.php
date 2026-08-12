<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;

/**
 * Single source of truth for tenant-level billing state stored on
 * `tenants.settings['billing']`.
 *
 * The bucket has many writers across the gateway codebase — this
 * service catalogues the keys with documented types so a new gateway
 * driver can see at a glance what's expected.  Existing writers
 * (StripeGateway, PayPalGateway, AbstractGateway::recordOrderedEvent)
 * are NOT yet migrated through this service because each writes a
 * non-trivial nested structure (e.g. last_webhook_at_<gateway>.<sub_id>);
 * those migrations should follow whichever driver-level refactor
 * surfaces them.
 *
 * Usage today: read-side helpers and the get/setKey atomic-update
 * pair, used wherever code needs to read or write a single billing
 * key without rebuilding the whole settings array.
 */
class TenantBillingStateService
{
    /** Documented keys for `tenants.settings['billing']` with defaults. */
    public const KEYS = [
        // Buyer's chosen billing cycle: 'month' or 'year'.  Stamped at
        // checkout-success time; receipts and renewal cron read it.
        'interval'           => null,
        // The gateway-side customer record id (Stripe customer_id,
        // PayPal payer_id, etc.).  Used by the customer-portal redirect
        // and by refund matching.
        'stripe_customer_id' => null,
        // Most recent gateway used to pay — drives "manage your
        // subscription" deep-links from the SA dashboard.
        'last_gateway'       => null,
        // Unix timestamp.  Set when Stripe reports cancel_at_period_end=true
        // so the SA dashboard can show "ends YYYY-MM-DD".
        'cancel_at'          => null,
        // ISO-3166-1 alpha-2 country code (e.g. 'GB', 'DE').  Drives
        // VAT computation in TaxCalculator.
        'tax_country'        => null,
        // Operator-supplied VAT id, optional.  Format varies by country.
        'tax_id'             => null,
    ];

    /**
     * Full billing state with every documented key resolved.  Keys not
     * in the documented set are still returned untouched so the
     * gateway's nested structures (last_webhook_at_*) remain visible
     * to callers that read them directly.
     */
    public function all(?Tenant $tenant): array
    {
        $stored = (array) ($tenant?->getSetting('billing') ?? []);
        return array_merge(self::KEYS, $stored);
    }

    public function get(?Tenant $tenant, string $key, mixed $default = null): mixed
    {
        return $this->all($tenant)[$key] ?? $default ?? (self::KEYS[$key] ?? null);
    }

    /**
     * Atomic single-key write — preserves any other keys (including
     * the nested webhook-timestamp structures that AbstractGateway
     * manages separately).  Saves the tenant immediately so the caller
     * doesn't need to.
     */
    public function setKey(Tenant $tenant, string $key, mixed $value): void
    {
        $settings = (array) ($tenant->settings ?? []);
        $billing  = (array) ($settings['billing'] ?? []);
        $billing[$key] = $value;
        $settings['billing'] = $billing;
        $tenant->settings = $settings;
        $tenant->save();
    }

    /**
     * Merge a partial billing-state patch onto the bucket.  Only keys
     * listed in self::KEYS are accepted from $values; nested gateway
     * structures must use setKey() directly to avoid the documented-
     * keys filter.
     */
    public function merge(Tenant $tenant, array $values): array
    {
        $clean = [];
        foreach (self::KEYS as $key => $_default) {
            if (array_key_exists($key, $values)) {
                $clean[$key] = $values[$key];
            }
        }

        $settings = (array) ($tenant->settings ?? []);
        $settings['billing'] = array_merge(
            (array) ($settings['billing'] ?? []),
            $clean,
        );

        $tenant->settings = $settings;
        $tenant->save();

        return $clean;
    }
}
