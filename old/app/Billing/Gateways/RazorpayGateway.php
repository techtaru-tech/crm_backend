<?php

namespace App\Billing\Gateways;

use App\Billing\AbstractGateway;
use App\Billing\CheckoutResult;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Razorpay driver using the Orders API. When the plan has a
 * razorpay_plan_id mapped we kick off a subscription; otherwise
 * we create a one-off order. Either way we return a hosted-checkout
 * URL via a tiny intermediate view that boots the Razorpay JS SDK
 * on the client — keeps server-side zero-dependency.
 */
class RazorpayGateway extends AbstractGateway
{
    public function id(): string
    {
        return 'razorpay';
    }

    public function label(): string
    {
        return 'Razorpay';
    }

    public function isConfigured(): bool
    {
        return filled($this->settings->razorpay_key_id)
            && filled($this->settings->razorpay_key_secret);
    }

    public function checkout(Tenant $tenant, Plan $plan): CheckoutResult
    {
        if (! $this->isConfigured()) {
            return CheckoutResult::error((string) __('billing_gateways.razorpay.not_configured'));
        }

        // Annual billing on Razorpay: not yet wired.
        // Razorpay plans are immutable on price+frequency, so honoring
        // `pending_interval = 'year'` requires the operator to pre-
        // create a yearly plan in their Razorpay dashboard and store
        // its id on Plan (e.g. `razorpay_plan_id_yearly`).  Until that
        // schema + checkout-resolver work lands, fail loud instead of
        // silently downgrading to monthly — the buyer would otherwise
        // pay monthly thinking they bought annual, and renew at a
        // price that doesn't match the pricing page.
        $pendingInterval = (string) (session('pending_interval') ?? 'month');
        if ($pendingInterval === 'year') {
            return CheckoutResult::error((string) __('billing_gateways.razorpay.annual_not_supported'));
        }

        try {
            if (filled($plan->razorpay_plan_id)) {
                $res = Http::withBasicAuth($this->settings->razorpay_key_id, $this->settings->razorpay_key_secret)
                    ->post('https://api.razorpay.com/v1/subscriptions', [
                        'plan_id'         => $plan->razorpay_plan_id,
                        'total_count'     => 120, // 10 years of monthly
                        'customer_notify' => 1,
                        'notes'           => [
                            'tenant_id' => (string) $tenant->id,
                            'plan_key'  => $plan->key,
                            'leadhub_coupon_code' => (string) (session('pending_coupon_code') ?? ''),
                        ],
                    ]);

                if ($res->failed()) {
                    $this->logError(new \RuntimeException($res->body()), 'checkout');
                    return CheckoutResult::error((string) __('billing_gateways.razorpay.subscription_failed'));
                }

                return CheckoutResult::redirect($res->json('short_url'));
            }

            // One-off order
            $res = Http::withBasicAuth($this->settings->razorpay_key_id, $this->settings->razorpay_key_secret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount'   => (int) round(((float) $plan->price) * 100),
                    'currency' => strtoupper($plan->currency ?? 'INR'),
                    'receipt'  => 'tenant_' . $tenant->id . '_' . time(),
                    'notes'    => [
                        'tenant_id' => (string) $tenant->id,
                        'plan_key'  => $plan->key,
                        'leadhub_coupon_code' => (string) (session('pending_coupon_code') ?? ''),
                    ],
                ]);

            if ($res->failed()) {
                $this->logError(new \RuntimeException($res->body()), 'checkout');
                return CheckoutResult::error((string) __('billing_gateways.razorpay.order_creation_failed'));
            }

            // Route to an intermediate checkout view that renders the Razorpay modal.
            return CheckoutResult::redirect(
                route('billing.razorpay.launch', ['order' => $res->json('id')])
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'checkout');
            return CheckoutResult::error((string) __('billing_gateways.razorpay.error', ['error' => $e->getMessage()]));
        }
    }

    public function handleWebhook(Request $request): bool
    {
        $secret = $this->settings->razorpay_webhook_secret;
        $payload = $request->getContent();

        // Webhook signature is MANDATORY — without it an attacker can
        // forge `subscription.activated` events to mark any tenant
        // active.  Default-deny when the operator hasn't configured
        // the secret; matches the same hardening on Stripe + PayPal.
        if (empty($secret)) {
            return false;
        }

        $signature = $request->header('X-Razorpay-Signature', '');
        $expected  = hash_hmac('sha256', $payload, $secret);
        if (! hash_equals($expected, $signature)) {
            return false;
        }

        $event = json_decode($payload, true) ?: [];
        $type  = $event['event'] ?? null;
        $eventId = (string) ($event['id'] ?? '');

        // Inbound idempotency — Razorpay retries every webhook on
        // failure.  Without dedup we'd re-book affiliate commission,
        // re-redeem coupons, and re-issue receipts on every retry.
        if ($eventId !== '' && \App\Models\ProcessedBillingEvent::wasProcessed('razorpay', $eventId)) {
            return true; // already processed — return 200 so Razorpay stops retrying
        }

        try {
            match ($type) {
                'subscription.activated',
                'subscription.charged',
                'order.paid'                => $this->onActivated($event),
                'subscription.cancelled'    => $this->onCancelled($event),
                'payment.failed'            => $this->onPaymentFailed($event),
                default                     => null,
            };
        } catch (\Throwable $e) {
            $this->logError($e, 'webhook/' . $type);
            return false;
        }

        if ($eventId !== '') {
            \App\Models\ProcessedBillingEvent::markProcessed('razorpay', $eventId, (string) $type);
        }

        return true;
    }

    /* -------------------------------------------------------- */

    private function onActivated(array $event): void
    {
        $notes = $event['payload']['subscription']['entity']['notes']
            ?? $event['payload']['order']['entity']['notes']
            ?? [];

        $tenantId = $notes['tenant_id'] ?? null;
        $planKey  = $notes['plan_key']  ?? null;
        if (! $tenantId || ! $planKey) return;

        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $externalId = $event['payload']['subscription']['entity']['id']
                ?? $event['payload']['order']['entity']['id']
                ?? null;

            // Forward the gateway-reported amount so the receipt
            // reflects the actual charge.  Razorpay reports amount in
            // paise (1 INR = 100 paise) on both subscription and order
            // entities; divide by 100 to get rupees (or the foreign-
            // currency equivalent in cents — same denomination math).
            $rawAmount = $event['payload']['subscription']['entity']['amount']
                ?? $event['payload']['order']['entity']['amount']
                ?? null;
            $actualAmount = $rawAmount !== null ? (float) $rawAmount / 100 : null;
            $interval = $this->resolveInterval();

            $this->markTenantActive(
                $tenant,
                $planKey,
                $externalId,
                $actualAmount,
                ['leadhub_interval' => $interval],
            );
            $this->bookAffiliateCommission($tenant, $planKey, $externalId);
            $this->redeemCouponIfPresent($tenant, $planKey, $notes, $externalId);
        }
    }

    /**
     * Resolve the buyer's chosen interval (month|year) from session.
     * BillingController stashes pending_interval before checkout so
     * the receipt metadata reflects the cycle the buyer paid for.
     * Razorpay plans are immutable on price+frequency (see the
     * "Annual billing not yet wired" comment block in checkout())
     * so this stays 'month' for almost all callers today.
     */
    private function resolveInterval(): string
    {
        $interval = (string) (session('pending_interval') ?? 'month');
        return in_array($interval, ['month', 'year'], true) ? $interval : 'month';
    }

    /**
     * If checkout notes stamped a coupon code, redeem it via
     * CouponService.  Same pattern as Stripe + Paystack — single
     * source of truth for the engine's bookkeeping.
     */
    private function redeemCouponIfPresent(Tenant $tenant, string $planKey, array $notes, ?string $externalId): void
    {
        $code = (string) ($notes['leadhub_coupon_code'] ?? '');
        if ($code === '') return;

        try {
            $coupon = \App\Models\Coupon::query()->where('code', $code)->first();
            if (! $coupon) return;

            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            $price    = $plan ? (float) $plan->price : 0.0;
            $currency = $plan ? (string) ($plan->currency ?? 'INR') : 'INR';

            app(\App\Services\CouponService::class)->redeem(
                coupon: $coupon,
                tenant: $tenant,
                planKey: $planKey,
                basePrice: $price,
                currency: $currency,
                metadata: ['source' => 'razorpay.subscription.activated', 'razorpay_external_id' => $externalId],
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'coupon-redemption');
        }
    }

    /**
     * Mirror of StripeGateway::bookAffiliateCommission for Razorpay.
     * Same 20% rate.  Idempotency is per-external-id so renewals
     * (subscription.charged with a fresh payment id each cycle)
     * book a new commission row, while webhook retries with the
     * same id don't double-book.  Defaults to INR (Razorpay's
     * primary market) when plan currency is null.
     *
     * Commission policy (intentional v1 choice):
     *   amount_attributed is the monthly $plan->price regardless of
     *   the buyer's billing cycle.  Operators who want commission on
     *   the actual charged amount should override this method.  See
     *   StripeGateway::bookAffiliateCommission docblock for the full
     *   rationale.
     */
    private function bookAffiliateCommission(Tenant $tenant, string $planKey, ?string $externalId): void
    {
        $referrerId = $tenant->referred_by_tenant_id;
        if (! $referrerId) return;

        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount   = (float) $plan->price;
            $currency = strtoupper((string) ($plan->currency ?? 'INR'));
            if ($amount <= 0) return;

            // Webhook-retry guard.  Renewals come with a different
            // external id each cycle so they still book; same id
            // (retried delivery) skips.
            if ($externalId) {
                try {
                    $exists = \App\Models\AffiliateReferral::query()
                        ->where('referred_tenant_id', $tenant->id)
                        ->where('plan_key', $planKey)
                        ->whereJsonContains('metadata->razorpay_external_id', $externalId)
                        ->exists();
                    if ($exists) return;
                } catch (\Throwable) {
                    // older MySQL JSON support — fall through.
                }
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
                    'source'              => 'razorpay.subscription.activated',
                    'razorpay_external_id' => $externalId,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logError($e, 'affiliate-commission-booking');
        }
    }

    /**
     * Settings-key under `tenants.settings.billing.<key>` where the
     * per-subscription ordered-event timestamps live.  AbstractGateway
     * uses this to gate out-of-order subscription.* deliveries — without
     * this override, a delayed `subscription.cancelled` arriving after
     * a fresh `subscription.activated` retry would flip the tenant
     * back to cancelled.
     */
    protected function webhookTimestampSettingsKey(): ?string
    {
        return 'last_webhook_at_razorpay';
    }

    private function onCancelled(array $event): void
    {
        $notes = $event['payload']['subscription']['entity']['notes'] ?? [];
        $tenantId = $notes['tenant_id'] ?? null;
        if (! $tenantId) return;

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        // Out-of-order guard: Razorpay retries on 5xx.  A delayed
        // cancel arriving after a fresh activate retry would silently
        // flip the tenant back to cancelled.  Razorpay sends `created_at`
        // as a Unix integer at the event root.
        $eventCreatedAt = (int) ($event['created_at'] ?? 0);
        $subId          = $event['payload']['subscription']['entity']['id'] ?? null;

        if (! $this->shouldApplyOrderedEvent($tenant, $subId, $eventCreatedAt)) {
            return;
        }

        $tenant->transitionSubscriptionStatus(\App\Enums\SubscriptionStatus::Cancelled);
        app(\App\Services\SubscriptionEventService::class)->cancelled($tenant);

        $this->recordOrderedEvent($tenant, $subId, $eventCreatedAt);
    }

    private function onPaymentFailed(array $event): void
    {
        $notes = $event['payload']['payment']['entity']['notes'] ?? [];
        $tenantId = $notes['tenant_id'] ?? null;
        if (! $tenantId) return;

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        $entity = $event['payload']['payment']['entity'] ?? [];
        app(\App\Services\SubscriptionEventService::class)->paymentFailed(
            $tenant,
            amount: number_format(($entity['amount'] ?? 0) / 100, 2, '.', ''),
            currency: strtoupper($entity['currency'] ?? 'INR'),
        );
    }
}
