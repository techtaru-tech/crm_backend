<?php

namespace App\Billing\Gateways;

use App\Billing\AbstractGateway;
use App\Billing\CheckoutResult;
use App\Models\Plan;
use App\Models\Tenant;
use App\Support\Currency;
use Illuminate\Http\Request;

/**
 * Bank-transfer / offline driver. Instead of redirecting to a hosted
 * checkout it marks the tenant as "pending_payment", flashes the
 * script owner's bank details to the user, and emails the Super
 * Admin so they can confirm the wire manually from the Tenants page.
 *
 * Designed for CodeCanyon buyers in regions where card gateways are
 * unreliable or where enterprise deals routinely pay by invoice.
 */
class ManualGateway extends AbstractGateway
{
    public function id(): string
    {
        return 'manual';
    }

    public function label(): string
    {
        // Fully translatable — no brand component.
        return (string) __('billing.gateway_manual_label');
    }

    public function isConfigured(): bool
    {
        // Minimum: bank name + account number OR IBAN.
        return filled($this->settings->manual_bank_name)
            && (filled($this->settings->manual_account_number) || filled($this->settings->manual_iban));
    }

    public function checkout(Tenant $tenant, Plan $plan): CheckoutResult
    {
        if (! $this->isConfigured()) {
            return CheckoutResult::error((string) __('billing_gateways.manual.not_configured'));
        }

        // Put the tenant in pending_payment so the dashboard knows not to
        // unlock paid features until a Super Admin confirms the transfer.
        $tenant->update([
            'plan'                => $plan->key,
            'subscription_status' => 'pending_payment',
        ]);

        app(\App\Services\SettingsService::class)
            ->forTenant($tenant)
            ->set('manual_pending_plan', $plan->key);

        $amount = Currency::format((float) $plan->price, $plan->currency ?? 'USD');

        // Label keys are translated at the source so the blade view can
        // render `$label` as-is.  Translating at the gateway (not the
        // view) keeps the buyer-facing dictionary collapsed to a single
        // namespace (`billing_gateways.manual.labels.*`) and means the
        // SA's emailed copy of these instructions sees the same locale
        // as whatever the browser sees.
        $instructions = collect([
            (string) __('billing_gateways.manual.labels.bank')           => $this->settings->manual_bank_name,
            (string) __('billing_gateways.manual.labels.account_name')   => $this->settings->manual_account_name,
            (string) __('billing_gateways.manual.labels.account_number') => $this->settings->manual_account_number,
            (string) __('billing_gateways.manual.labels.iban')           => $this->settings->manual_iban,
            (string) __('billing_gateways.manual.labels.swift_bic')      => $this->settings->manual_swift,
            (string) __('billing_gateways.manual.labels.amount')         => $amount,
            (string) __('billing_gateways.manual.labels.reference')      => 'LH-' . $tenant->id . '-' . strtoupper($plan->key),
        ])->filter()->all();

        $notes = $this->settings->manual_extra_instructions;

        return CheckoutResult::instructions(
            (string) __('billing_gateways.manual.instructions_intro'),
            [
                'instructions' => $instructions,
                'notes'        => $notes,
                'plan'         => $plan->name,
                'amount'       => $amount,
            ],
        );
    }

    public function handleWebhook(Request $request): bool
    {
        // No webhook — manual gateway is confirmed by a Super Admin action
        // through {@see confirmManualPayment()}, which is a single
        // synchronous in-app call and is NOT retried by any external
        // delivery system.  Inbound-event idempotency via
        // ProcessedBillingEvent is therefore not applicable here:
        // there is no gateway-assigned event id to dedup on, and a
        // re-confirmation by the operator is an explicit human decision
        // rather than an automated retry.  Each public confirmation
        // path already has its own domain-specific guards (e.g. the
        // year-month period-key in bookAffiliateCommission) so a
        // duplicate operator click does not double-pay anyone.
        return true;
    }

    /**
     * Explicit hook the SA calls when they receive the wire transfer
     * for a tenant that's in pending_payment.  Activates the
     * subscription + books the affiliate commission (if the tenant
     * was referred).
     *
     * Wire-up: TenantResource has a "Mark payment received" action
     * that calls this method.  Operators can also invoke it from
     * Tinker or a custom support flow.
     */
    public function confirmManualPayment(Tenant $tenant, Plan $plan, ?string $note = null, ?string $couponCode = null): void
    {
        // Manual payments have no session-stored interval (the SA
        // picks the plan directly), so honor whichever cycle the
        // operator chose on the Plan itself.  basePriceFor() returns
        // the configured annual price (or 12× monthly fallback) for
        // year, plain price for month — annual subscribers now see a
        // receipt for the full year instead of a single month.
        $interval = (string) ($plan->interval ?? 'month');
        if (! in_array($interval, ['month', 'year'], true)) {
            $interval = 'month';
        }
        $actualAmount = $plan->basePriceFor($interval);

        $this->markTenantActive(
            $tenant,
            $plan->key,
            $note,
            $actualAmount,
            ['leadhub_interval' => $interval],
        );
        $this->bookAffiliateCommission($tenant, $plan->key, $note);
        $this->redeemCouponIfPresent($tenant, $plan, $couponCode, $note);
    }

    /**
     * Operator-supplied coupon code (e.g. typed into the
     * "Mark payment received" SA action) gets redeemed here so
     * the engine's bookkeeping stays consistent across gateways.
     */
    private function redeemCouponIfPresent(Tenant $tenant, Plan $plan, ?string $couponCode, ?string $note): void
    {
        if (empty($couponCode)) return;

        try {
            $coupon = \App\Models\Coupon::query()->where('code', $couponCode)->first();
            if (! $coupon) return;

            app(\App\Services\CouponService::class)->redeem(
                coupon: $coupon,
                tenant: $tenant,
                planKey: $plan->key,
                basePrice: (float) $plan->price,
                currency: (string) ($plan->currency ?? 'USD'),
                metadata: ['source' => 'manual.payment_confirmed', 'note' => $note],
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'coupon-redemption');
        }
    }

    /**
     * Mirror of StripeGateway::bookAffiliateCommission for Manual.
     * Same 20% rate.  Idempotency is per-confirmation-period: the
     * SA can confirm renewal payments manually month after month and
     * each confirmation books a new commission row.  We key on
     * year-month + tenant + plan so two confirmations in the same
     * month don't double-book (the SA can adjust manually if needed).
     *
     * Commission policy (intentional v1 choice):
     *   amount_attributed is the monthly $plan->price regardless of
     *   the buyer's billing cycle.  Annual manual confirmations don't
     *   12× the referrer's payout.  Operators who want commission on
     *   the actual charged amount should override this method.  See
     *   StripeGateway::bookAffiliateCommission docblock for the full
     *   rationale.
     */
    private function bookAffiliateCommission(Tenant $tenant, string $planKey, ?string $note): void
    {
        $referrerId = $tenant->referred_by_tenant_id;
        if (! $referrerId) return;

        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount   = (float) $plan->price;
            $currency = strtoupper((string) ($plan->currency ?? 'USD'));
            if ($amount <= 0) return;

            // Y-m alone collides when a tenant downgrades to free and
            // re-confirms the same plan within the same month.  Mixing
            // in an md5 prefix of $note distinguishes "first wire of
            // April" from "second wire of April with a different memo".
            // Plus a per-call random suffix so two operator
            // confirmations using the SAME memo (copy/paste, batched
            // verification) still book distinct commissions instead of
            // silently colliding on the dedup check.
            $periodKey = now()->format('Y-m')
                . ':' . substr(md5((string) ($note ?? '')), 0, 8)
                . ':' . \Illuminate\Support\Str::random(6);

            try {
                $exists = \App\Models\AffiliateReferral::query()
                    ->where('referred_tenant_id', $tenant->id)
                    ->where('plan_key', $planKey)
                    ->whereJsonContains('metadata->manual_period_key', $periodKey)
                    ->exists();
                if ($exists) return;
            } catch (\Throwable) {
                // older MySQL JSON support — fall through.
            }

            $commissionPct = $this->commissionPercent();
            $commission    = round($amount * $commissionPct / 100, 2);

            \App\Models\AffiliateReferral::create([
                'referrer_tenant_id'  => $referrerId,
                'referred_tenant_id'  => $tenant->id,
                'plan_key'            => $planKey,
                'amount_attributed'   => $amount,
                'commission_percent'  => $commissionPct,
                'commission_amount'   => $commission,
                'currency'            => $currency,
                'commission_status'   => \App\Models\AffiliateReferral::STATUS_PENDING,
                'metadata'            => [
                    'source'             => 'manual.payment_confirmed',
                    'note'               => $note,
                    'manual_period_key'  => $periodKey,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logError($e, 'affiliate-commission-booking');
        }
    }
}
