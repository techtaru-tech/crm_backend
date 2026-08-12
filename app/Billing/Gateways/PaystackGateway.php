<?php

namespace App\Billing\Gateways;

use App\Billing\AbstractGateway;
use App\Billing\CheckoutResult;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Paystack driver using the Transaction Initialize API. Popular with
 * African operators (Nigeria, Ghana, Kenya, South Africa) — returns
 * a hosted checkout URL directly.
 */
class PaystackGateway extends AbstractGateway
{
    public function id(): string
    {
        return 'paystack';
    }

    public function label(): string
    {
        return 'Paystack';
    }

    public function isConfigured(): bool
    {
        return filled($this->settings->paystack_secret_key);
    }

    public function checkout(Tenant $tenant, Plan $plan): CheckoutResult
    {
        if (! $this->isConfigured()) {
            return CheckoutResult::error((string) __('billing_gateways.paystack.not_configured'));
        }

        // Annual billing on Paystack: not yet wired.
        // Paystack plan codes encode price+interval at creation time,
        // so honoring `pending_interval = 'year'` requires the
        // operator to pre-create a yearly plan in their Paystack
        // dashboard and store its code on Plan (e.g.
        // `paystack_plan_code_yearly`).  Until that schema +
        // checkout-resolver work lands, fail loud instead of silently
        // downgrading to monthly — the buyer would otherwise pay
        // monthly thinking they bought annual, and renew at a price
        // that doesn't match the pricing page.
        $pendingInterval = (string) (session('pending_interval') ?? 'month');
        if ($pendingInterval === 'year') {
            return CheckoutResult::error((string) __('billing_gateways.paystack.annual_not_supported'));
        }

        try {
            $payload = [
                'email'    => $tenant->owner?->email ?? 'no-reply@' . parse_url(config('app.url'), PHP_URL_HOST),
                'amount'   => (int) round(((float) $plan->price) * 100),
                'currency' => strtoupper($plan->currency ?? 'NGN'),
                'reference' => 'tnt_' . $tenant->id . '_' . time(),
                'callback_url' => $this->successUrl($tenant),
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan_key'  => $plan->key,
                    'leadhub_coupon_code' => (string) (session('pending_coupon_code') ?? ''),
                ],
            ];

            if (filled($plan->paystack_plan_code)) {
                $payload['plan'] = $plan->paystack_plan_code;
            }

            $res = Http::withToken($this->settings->paystack_secret_key)
                ->acceptJson()
                ->post('https://api.paystack.co/transaction/initialize', $payload);

            if ($res->failed() || ! $res->json('status')) {
                $this->logError(new \RuntimeException($res->body()), 'checkout');
                return CheckoutResult::error($res->json('message', (string) __('billing_gateways.paystack.checkout_failed')));
            }

            return CheckoutResult::redirect($res->json('data.authorization_url'));
        } catch (\Throwable $e) {
            $this->logError($e, 'checkout');
            return CheckoutResult::error((string) __('billing_gateways.paystack.error', ['error' => $e->getMessage()]));
        }
    }

    public function handleWebhook(Request $request): bool
    {
        $secret = $this->settings->paystack_secret_key;
        $payload = $request->getContent();

        // Webhook signature is MANDATORY — without it an attacker can
        // forge `charge.success` events to mark any tenant active.
        // Default-deny when the operator hasn't configured the secret.
        if (empty($secret)) {
            return false;
        }

        $signature = $request->header('X-Paystack-Signature', '');
        $expected  = hash_hmac('sha512', $payload, $secret);
        if (! hash_equals($expected, $signature)) {
            return false;
        }

        $event = json_decode($payload, true) ?: [];
        $type  = $event['event'] ?? null;

        // Inbound idempotency.  Paystack uses data.id for charge events
        // and event-level id for subscriptions; fall back to
        // data.reference as a tiebreaker.
        $eventId = (string) ($event['data']['id']
            ?? $event['id']
            ?? $event['data']['reference']
            ?? '');
        if ($eventId !== '' && \App\Models\ProcessedBillingEvent::wasProcessed('paystack', $eventId)) {
            return true;
        }

        try {
            match ($type) {
                'charge.success',
                'subscription.create'     => $this->onSuccess($event),
                'subscription.disable',
                'subscription.not_renew'  => $this->onCancelled($event),
                'invoice.payment_failed'  => $this->onPaymentFailed($event),
                default                   => null,
            };
        } catch (\Throwable $e) {
            $this->logError($e, 'webhook/' . $type);
            return false;
        }

        if ($eventId !== '') {
            \App\Models\ProcessedBillingEvent::markProcessed('paystack', $eventId, (string) $type);
        }

        return true;
    }

    /* -------------------------------------------------------- */

    private function onSuccess(array $event): void
    {
        $meta = $event['data']['metadata'] ?? [];
        $tenantId = $meta['tenant_id'] ?? null;
        $planKey  = $meta['plan_key']  ?? null;
        if (! $tenantId || ! $planKey) return;

        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $reference = $event['data']['reference'] ?? null;

            // Forward the gateway-reported amount + currency so the
            // receipt reflects the actual charge.  Paystack reports
            // data.amount in the smallest currency unit (kobo for NGN,
            // cents for USD/ZAR/etc.) so divide by 100 to get the
            // human-readable major-unit amount our receipts store.
            $rawAmount = $event['data']['amount'] ?? null;
            $actualAmount = $rawAmount !== null ? (float) $rawAmount / 100 : null;
            $interval = $this->resolveInterval();

            $this->markTenantActive(
                $tenant,
                $planKey,
                $reference,
                $actualAmount,
                ['leadhub_interval' => $interval],
            );
            $this->bookAffiliateCommission($tenant, $planKey, $reference);
            $this->redeemCouponIfPresent($tenant, $planKey, $meta, $reference);
        }
    }

    /**
     * Resolve the buyer's chosen interval (month|year) from session.
     * BillingController stashes pending_interval before checkout so
     * the receipt metadata reflects the cycle the buyer paid for.
     * Until Paystack supports yearly plan codes natively (see the
     * "Annual billing not yet wired" comment block in checkout())
     * this stays 'month' for almost all callers.
     */
    private function resolveInterval(): string
    {
        $interval = (string) (session('pending_interval') ?? 'month');
        return in_array($interval, ['month', 'year'], true) ? $interval : 'month';
    }

    /**
     * If checkout metadata stamped a coupon code, redeem it via
     * CouponService — increments times_used + writes redemption row.
     * Defence-in-depth: re-validates inside redeem() so a coupon
     * exhausted between checkout-start and webhook fails cleanly.
     */
    private function redeemCouponIfPresent(Tenant $tenant, string $planKey, array $meta, ?string $reference): void
    {
        $code = (string) ($meta['leadhub_coupon_code'] ?? '');
        if ($code === '') return;

        try {
            $coupon = \App\Models\Coupon::query()->where('code', $code)->first();
            if (! $coupon) return;

            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            $price    = $plan ? (float) $plan->price : 0.0;
            $currency = $plan ? (string) ($plan->currency ?? 'NGN') : 'NGN';

            app(\App\Services\CouponService::class)->redeem(
                coupon: $coupon,
                tenant: $tenant,
                planKey: $planKey,
                basePrice: $price,
                currency: $currency,
                metadata: ['source' => 'paystack.charge.success', 'paystack_reference' => $reference],
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'coupon-redemption');
        }
    }

    /**
     * Mirror of StripeGateway::bookAffiliateCommission for Paystack.
     * Same 20% default rate.  Idempotency is per-payment-reference so
     * recurring charges (charge.success on subscription renewals) book
     * a fresh commission row each cycle while webhook retries with the
     * same reference don't double-book.
     *
     * Commission policy (intentional v1 choice):
     *   amount_attributed is the monthly $plan->price regardless of
     *   the buyer's billing cycle.  Operators who want commission on
     *   the actual charged amount should override this method.  See
     *   StripeGateway::bookAffiliateCommission docblock for the full
     *   rationale.
     */
    private function bookAffiliateCommission(Tenant $tenant, string $planKey, ?string $reference): void
    {
        $referrerId = $tenant->referred_by_tenant_id;
        if (! $referrerId) return;

        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount   = (float) $plan->price;
            $currency = strtoupper((string) ($plan->currency ?? 'NGN'));
            if ($amount <= 0) return;

            // Webhook-retry guard: if we've already booked this
            // exact reference, skip.  Renewals get a different
            // reference each cycle so they still book.
            if ($reference) {
                try {
                    $exists = \App\Models\AffiliateReferral::query()
                        ->where('referred_tenant_id', $tenant->id)
                        ->where('plan_key', $planKey)
                        ->whereJsonContains('metadata->paystack_reference', $reference)
                        ->exists();
                    if ($exists) return;
                } catch (\Throwable) {
                    // older MySQL JSON support — fall through; worst case
                    // is a duplicate the operator can dedupe in admin.
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
                    'source'              => 'paystack.charge.success',
                    'paystack_reference'  => $reference,
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
     * this override, a delayed `subscription.disable` arriving after a
     * fresh `subscription.create` retry would flip the tenant back to
     * cancelled.
     */
    protected function webhookTimestampSettingsKey(): ?string
    {
        return 'last_webhook_at_paystack';
    }

    private function onCancelled(array $event): void
    {
        $meta = $event['data']['metadata'] ?? [];
        $tenantId = $meta['tenant_id'] ?? null;
        if (! $tenantId) return;

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        // Out-of-order guard.  Paystack emits `created_at` as an
        // ISO 8601 string at the event root — convert to Unix.
        $createdAtRaw   = (string) ($event['created_at'] ?? '');
        $eventCreatedAt = $createdAtRaw !== '' ? (int) strtotime($createdAtRaw) : 0;
        $subId          = $event['data']['subscription_code']
            ?? $event['data']['code']
            ?? null;

        if (! $this->shouldApplyOrderedEvent($tenant, $subId, $eventCreatedAt)) {
            return;
        }

        $tenant->transitionSubscriptionStatus(\App\Enums\SubscriptionStatus::Cancelled);
        app(\App\Services\SubscriptionEventService::class)->cancelled($tenant);

        $this->recordOrderedEvent($tenant, $subId, $eventCreatedAt);
    }

    private function onPaymentFailed(array $event): void
    {
        $meta = $event['data']['metadata'] ?? [];
        $tenantId = $meta['tenant_id'] ?? null;
        if (! $tenantId) return;

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        app(\App\Services\SubscriptionEventService::class)->paymentFailed(
            $tenant,
            amount: number_format(($event['data']['amount'] ?? 0) / 100, 2, '.', ''),
            currency: strtoupper($event['data']['currency'] ?? 'NGN'),
        );
    }
}
