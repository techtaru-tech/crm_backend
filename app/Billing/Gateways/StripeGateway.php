<?php

namespace App\Billing\Gateways;

use App\Billing\AbstractGateway;
use App\Billing\CheckoutResult;
use App\Billing\Contracts\InvoiceCheckoutGateway;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Stripe driver using the REST API directly (no stripe/stripe-php
 * dependency). Checkout Sessions + Billing Portal + webhooks are all
 * reachable through plain HTTPS, which keeps the CodeCanyon install
 * footprint zero-config beyond filling in the secret key.
 *
 * If the buyer later wants `stripe/stripe-php`, they can drop it in
 * without touching this driver — Http::asForm() calls Stripe exactly
 * the same way the SDK does under the hood.
 */
class StripeGateway extends AbstractGateway implements InvoiceCheckoutGateway
{
    /**
     * One-off Payment-mode Checkout Session for a customer-facing Invoice.
     * Uses the same REST-over-HTTP approach as {@see checkout()} — no
     * stripe/stripe-php dependency required.
     */
    public function createInvoiceCheckout(Invoice $invoice, string $successUrl, string $cancelUrl): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $amountDue = $invoice->amountDue();
        if ($amountDue <= 0) {
            return null;
        }

        $currency = strtolower($invoice->currency ?: 'usd');
        $email    = $invoice->lead?->email;

        $payload = [
            'mode'                   => 'payment',
            'payment_method_types[]' => 'card',
            'client_reference_id'    => 'invoice_' . $invoice->id,
            'metadata[invoice_id]'   => $invoice->id,
            'metadata[tenant_id]'    => $invoice->tenant_id,
            'success_url'            => $successUrl,
            'cancel_url'             => $cancelUrl,
            'line_items[0][price_data][currency]'           => $currency,
            'line_items[0][price_data][product_data][name]' => $invoice->invoice_number,
            'line_items[0][price_data][unit_amount]'        => (int) round($amountDue * 100),
            'line_items[0][quantity]'                       => 1,
            // Same buyer-locale handling as the subscription checkout
            // payload above — keeps the public invoice pay link
            // localised end-to-end instead of jumping to English on
            // Stripe's hosted page.
            'locale'                 => $this->stripeLocale(),
        ];

        if ($email) {
            $payload['customer_email'] = $email;
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($this->settings->stripe_secret_key, '')
                ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

            if ($response->failed()) {
                $this->logError(new \RuntimeException($response->body()), 'invoice-checkout');
                return null;
            }

            return $response->json('url');
        } catch (\Throwable $e) {
            $this->logError($e, 'invoice-checkout');
            return null;
        }
    }

    public function id(): string
    {
        return 'stripe';
    }

    public function label(): string
    {
        // "Stripe" is a brand name (kept literal in every locale);
        // the "(Card)" suffix is translatable via the billing
        // lang file so non-English deployments can localise it.
        return (string) __('billing.gateway_stripe_label');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->settings->stripe_secret_key);
    }

    public function checkout(Tenant $tenant, Plan $plan): CheckoutResult
    {
        if (! $this->isConfigured()) {
            return CheckoutResult::error((string) __('billing_gateways.stripe.not_configured'));
        }

        // Resolve the buyer's chosen billing interval.  BillingController
        // stashes 'pending_interval' (month|year) in the session before
        // redirecting here.  Falls back to the plan's native interval
        // and finally to 'month' for safety.
        $interval = (string) (session('pending_interval') ?? $plan->interval ?? 'month');
        if (! in_array($interval, ['month', 'year'], true)) {
            $interval = 'month';
        }

        // Coupon resolution — BillingController stores the validated
        // coupon code in session before redirecting to checkout.  We
        // pull it here, re-validate (defence-in-depth), and apply via
        // Plan::priceAfterCoupon() to the unit_amount on top of the
        // base price for the chosen interval.
        //
        // Pre-created Stripe Price IDs can't be discounted inline, so
        // when a coupon is active OR the buyer picked annual we always
        // fall through to the price_data payload path (the
        // pre-created Price is monthly by definition).
        $coupon = $this->resolvePendingCoupon($tenant, $plan);
        $effectivePrice = $plan->priceAfterCoupon($coupon, $interval);
        $intervalBasePrice = $plan->basePriceFor($interval);
        $couponActive = $coupon !== null && $effectivePrice < $intervalBasePrice;

        // Tax — read tenant's billing country + VAT id, compute rate.
        // Trial-extension coupons return base price (no $ discount) so
        // tax math runs on the same number Stripe will charge.
        $tax = $this->resolveTaxRate($tenant, $effectivePrice);

        // Pre-created Stripe Price IDs only encode the monthly cycle;
        // annual checkouts always go through price_data so we set the
        // correct unit_amount and recurring.interval.
        $priceId = ($couponActive || $interval === 'year') ? null : ($plan->stripe_price_id ?? null);

        $payload = [
            'mode'                       => 'subscription',
            'payment_method_types[]'     => 'card',
            'customer_email'             => $tenant->owner?->email,
            'client_reference_id'        => $tenant->id,
            'metadata[tenant_id]'        => $tenant->id,
            'metadata[plan_key]'         => $plan->key,
            'metadata[leadhub_interval]' => $interval,
            'success_url'                => $this->successUrl($tenant),
            'cancel_url'                 => $this->cancelUrl($tenant),
            // Pass the buyer's current UI locale so Stripe's hosted
            // checkout page renders in their language instead of
            // falling back to English.  Stripe expects ISO 639-1
            // plus an optional region (e.g. 'es', 'pt-BR', 'zh-HK')
            // and accepts 'auto' for browser detection.  When the
            // app locale isn't in Stripe's supported set (Arabic and
            // Hindi don't have native Stripe translations yet) we
            // pass 'auto' so Stripe falls back gracefully via Accept-
            // Language instead of erroring on an unknown locale.
            // Without this parameter a Spanish/Arabic/Hindi buyer
            // sees a localised LeadHub pricing page → English Stripe
            // checkout → localised return page (jarring UX).
            'locale'                     => $this->stripeLocale(),
        ];

        if ($coupon) {
            $payload['metadata[leadhub_coupon_code]'] = $coupon->code;
        }

        if ($tax['note']) {
            $payload['metadata[tax_note]'] = $tax['note'];
        }

        if ($priceId) {
            $payload['line_items[0][price]']    = $priceId;
            $payload['line_items[0][quantity]'] = 1;
        } else {
            $payload['line_items[0][price_data][currency]']            = strtolower($plan->currency ?? 'usd');
            $payload['line_items[0][price_data][product_data][name]']  = $plan->name . ($couponActive ? (string) __('billing_gateways.stripe.product_coupon_suffix', ['code' => $coupon->code]) : '');
            $payload['line_items[0][price_data][unit_amount]']         = (int) round($effectivePrice * 100);
            $payload['line_items[0][price_data][recurring][interval]'] = $interval === 'year' ? 'year' : 'month';
            $payload['line_items[0][quantity]']                        = 1;
        }

        // Apply VAT via Stripe's automatic_tax when we know the
        // tenant's tax country.  Operators with Stripe Tax enabled
        // get the most accurate rates; everyone else uses the
        // TaxCalculator-derived rate as metadata only and handles
        // tax themselves.
        if ($tax['rate'] > 0 && $tax['use_automatic_tax']) {
            $payload['automatic_tax[enabled]'] = 'true';
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($this->settings->stripe_secret_key, '')
                ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

            if ($response->failed()) {
                $this->logError(new \RuntimeException($response->body()), 'checkout');
                return CheckoutResult::error($response->json('error.message', (string) __('billing_gateways.stripe.checkout_failed')));
            }

            return CheckoutResult::redirect($response->json('url'));
        } catch (\Throwable $e) {
            $this->logError($e, 'checkout');
            return CheckoutResult::error((string) __('billing_gateways.stripe.start_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Read the pending coupon code from session (set by
     * BillingController preflight), validate via CouponService, and
     * return the Coupon model if it's still applicable to this plan.
     *
     * Defence-in-depth — the coupon could have been exhausted between
     * the preflight check and checkout.  Returning null means the
     * gateway falls through to the base price.
     */
    private function resolvePendingCoupon(Tenant $tenant, Plan $plan): ?\App\Models\Coupon
    {
        try {
            $code = (string) (session('pending_coupon_code') ?? '');
            if ($code === '') return null;

            $check = app(\App\Services\CouponService::class)->validate(
                code: $code,
                tenant: $tenant,
                planKey: $plan->key,
                basePrice: (float) $plan->price,
                currency: $plan->currency,
            );

            return $check['valid'] ? $check['coupon'] : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Compute the VAT rate to apply at checkout based on the tenant's
     * billing country + VAT id.  Returns:
     *   {rate:float, note:?string, use_automatic_tax:bool}
     *
     * use_automatic_tax=true means the operator has Stripe Tax
     * enabled (settings.billing.stripe_automatic_tax) and wants Stripe
     * to compute the actual rate instead of our local table.  When
     * false, our TaxCalculator-derived rate is exposed via metadata
     * for the operator's own tax-reporting workflow.
     */
    private function resolveTaxRate(Tenant $tenant, float $netAmount): array
    {
        $country = (string) ($tenant->getSetting('billing.tax_country') ?? '');
        $vatId   = (string) ($tenant->getSetting('billing.tax_id') ?? '');
        $opCountry = (string) (config('services.billing.operator_country') ?? env('OPERATOR_COUNTRY', ''));

        try {
            $result = app(\App\Services\TaxCalculator::class)->calculate(
                amount: $netAmount,
                country: $country ?: null,
                vatId: $vatId ?: null,
                operatorCountry: $opCountry ?: null,
            );

            return [
                'rate'              => (float) ($result['tax_rate'] ?? 0.0),
                'note'              => $result['note'] ?? null,
                'use_automatic_tax' => (bool) ($this->settings->stripe_automatic_tax ?? false),
            ];
        } catch (\Throwable) {
            return ['rate' => 0.0, 'note' => null, 'use_automatic_tax' => false];
        }
    }

    public function handleWebhook(Request $request): bool
    {
        $secret = $this->settings->stripe_webhook_secret;
        $payload = $request->getContent();

        // Webhook signature is MANDATORY — without it an attacker who
        // knows or guesses a tenant id can POST a forged
        // `checkout.session.completed` event to mark any tenant active
        // on the free plan.  Default-deny when the operator hasn't
        // configured the secret, mirroring PayPal's existing
        // `paypal_webhook_id` guard.  Operators get clear feedback in
        // Stripe's webhook dashboard ("delivery rejected with 400").
        if (empty($secret)) {
            return false;
        }

        // Stripe signature scheme: t=TIMESTAMP,v1=SIGNATURE
        $header = $request->header('Stripe-Signature', '');
        if (! $this->verifyStripeSignature($payload, $header, $secret)) {
            return false;
        }

        $event = json_decode($payload, true) ?: [];
        $type  = $event['type'] ?? null;
        $data  = $event['data']['object'] ?? [];
        $eventId = (string) ($event['id'] ?? '');

        // Inbound idempotency — Stripe retries every webhook up to 3
        // times.  Without dedup we'd re-book commission, re-redeem
        // coupons, and re-issue receipts on every retry.
        if ($eventId !== '' && \App\Models\ProcessedBillingEvent::wasProcessed('stripe', $eventId)) {
            return true; // already processed
        }

        // Forward the event-level created timestamp to subscription
        // mutators so they can drop out-of-order deliveries.  Stripe
        // can deliver events out of order under retry/restore — without
        // this a delayed customer.subscription.deleted event arriving
        // AFTER a fresh customer.subscription.updated would flip the
        // tenant back to cancelled.
        $eventCreatedAt = (int) ($event['created'] ?? 0);

        // Side-effects atomic with the markProcessed write.  Without
        // this transaction a handler that crashed halfway through (say,
        // bookAffiliateCommission row created but issueReceiptForPayment
        // throws) would already have written some state.  Stripe's
        // automatic retry (3 attempts) would then run the WHOLE handler
        // again — coupons re-redeemed, commissions doubled, receipts
        // duplicated.  Wrapping the dispatch + markProcessed inside one
        // transaction means either everything lands or nothing does, and
        // markProcessed only writes on full success.
        try {
            DB::transaction(function () use ($type, $data, $eventCreatedAt, $eventId) {
            match ($type) {
                'checkout.session.completed'    => $this->onCheckoutCompleted($data),
                'customer.subscription.updated' => $this->onSubscriptionUpdated($data, $eventCreatedAt),
                'customer.subscription.deleted' => $this->onSubscriptionDeleted($data, $eventCreatedAt),
                'invoice.payment_failed'        => $this->onPaymentFailed($data),
                'invoice.payment_succeeded'     => $this->onInvoicePaid($data),
                'charge.refunded'               => $this->onRefund($data),
                'charge.dispute.created'        => $this->onDispute($data),
                default                         => null,
            };

            // Inside the transaction so a partial-write failure rolls back
            // the markProcessed too — Stripe will retry and we'll get a
            // clean attempt instead of being stuck in a half-applied state.
            if ($eventId !== '') {
                \App\Models\ProcessedBillingEvent::markProcessed('stripe', $eventId, (string) $type);
            }
            });
        } catch (\Throwable $e) {
            $this->logError($e, 'webhook/' . $type);
            return false;
        }

        return true;
    }

    /* -------------------------------------------------------- */

    /**
     * Resolve the buyer's UI locale into a value Stripe's Checkout
     * Session `locale` parameter accepts.  Stripe localises the
     * hosted checkout into a fixed set of languages — passing an
     * unsupported value (e.g. 'ar', 'hi') returns a 400 from the
     * API and breaks checkout entirely.  When the current app
     * locale isn't in Stripe's supported set we hand it 'auto' so
     * Stripe falls back to browser Accept-Language detection,
     * which still beats hard-coded English.
     *
     * Source for the supported list: Stripe Checkout Sessions API
     * docs (Locale parameter).  Kept in sync manually — Stripe
     * adds new locales periodically.
     */
    private function stripeLocale(): string
    {
        $supported = [
            'auto', 'bg', 'cs', 'da', 'de', 'el', 'en', 'en-GB',
            'es', 'es-419', 'et', 'fi', 'fil', 'fr', 'fr-CA',
            'hr', 'hu', 'id', 'it', 'ja', 'ko', 'lt', 'lv', 'ms',
            'mt', 'nb', 'nl', 'pl', 'pt', 'pt-BR', 'ro', 'ru',
            'sk', 'sl', 'sv', 'th', 'tr', 'vi', 'zh', 'zh-HK',
            'zh-TW',
        ];

        $locale = (string) app()->getLocale();
        return in_array($locale, $supported, true) ? $locale : 'auto';
    }

    private function verifyStripeSignature(string $payload, string $header, string $secret): bool
    {
        if (! $header) return false;

        $parts = [];
        foreach (explode(',', $header) as $item) {
            [$k, $v] = array_pad(explode('=', trim($item), 2), 2, null);
            $parts[$k] ??= $v;
        }

        if (! isset($parts['t'], $parts['v1'])) return false;

        // Replay protection: reject deliveries older than 5 minutes per
        // Stripe's documented tolerance window.  Without this, a leaked
        // signature header (proxy logs, MITM on misconfigured TLS) could
        // be replayed indefinitely against a different tenant — the
        // event-id idempotency guard at handleWebhook() blocks the
        // EXACT same event but not historically valid headers attached
        // to fresh attacker-controlled bodies.
        $timestamp = (int) $parts['t'];
        if ($timestamp <= 0 || (time() - $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $parts['t'] . '.' . $payload, $secret);
        return hash_equals($expected, $parts['v1']);
    }

    /**
     * Settle a customer-facing Invoice checkout (Stripe session carrying
     * metadata[invoice_id]). Flips the pending InvoicePayment created in
     * PublicInvoiceController::pay() to succeeded - the model's updated() hook
     * then recomputes the invoice's amount_paid + status. Falls back to
     * creating a succeeded row if the pending one is gone. Idempotency comes
     * from handleWebhook()'s ProcessedBillingEvent guard.
     */
    private function settleInvoiceCheckout(array $session): void
    {
        $invoiceId = (int) ($session['metadata']['invoice_id'] ?? 0);
        $tenantId  = (int) ($session['metadata']['tenant_id'] ?? 0);

        // Webhook has no auth context, so load past the tenant global scope;
        // the metadata tenant_id still pins it to the right tenant.
        $invoice = Invoice::withoutGlobalScope('tenant')
            ->where('id', $invoiceId)
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->first();

        if (! $invoice) {
            return;
        }

        // Bind the tenant so InvoicePayment's BelongsToTenant queries (used by
        // Invoice::recordPayment) resolve here instead of failing closed.
        if ($tenant = Tenant::find($invoice->tenant_id)) {
            app()->instance('current_tenant', $tenant);
        }

        $amount = isset($session['amount_total'])
            ? (float) $session['amount_total'] / 100
            : (float) $invoice->amountDue();

        $externalId = (string) ($session['payment_intent'] ?? $session['id'] ?? '');

        $payment = \App\Models\InvoicePayment::where('invoice_id', $invoice->id)
            ->where('gateway', 'stripe')
            ->where('status', \App\Models\InvoicePayment::STATUS_PENDING)
            ->latest('id')
            ->first();

        if ($payment) {
            // updated() hook -> recordPayment() recomputes the invoice.
            $payment->update([
                'status'      => \App\Models\InvoicePayment::STATUS_SUCCEEDED,
                'external_id' => $externalId,
                'amount'      => $amount,
                'paid_at'     => now(),
            ]);
            return;
        }

        // created() hook -> recordPayment() recomputes the invoice.
        \App\Models\InvoicePayment::create([
            'tenant_id'   => $invoice->tenant_id,
            'invoice_id'  => $invoice->id,
            'gateway'     => 'stripe',
            'external_id' => $externalId,
            'amount'      => $amount,
            'currency'    => $invoice->currency,
            'status'      => \App\Models\InvoicePayment::STATUS_SUCCEEDED,
            'paid_at'     => now(),
            'meta'        => ['settled_via' => 'webhook'],
        ]);
    }

    private function onCheckoutCompleted(array $session): void
    {
        // Customer-facing Invoice checkouts carry metadata[invoice_id] and have
        // NO plan_key. Settle them so the online "Pay Now" flow actually
        // completes - previously these sessions fell through the plan_key guard
        // below and the invoice stayed unpaid until a manual record-payment.
        if (! empty($session['metadata']['invoice_id'])) {
            $this->settleInvoiceCheckout($session);
            return;
        }

        $tenantId = $session['metadata']['tenant_id'] ?? null;
        $planKey  = $session['metadata']['plan_key'] ?? null;

        if (! $tenantId || ! $planKey) return;

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        // Pass the gateway-reported amount_total (in cents) so the
        // receipt reflects the actual charge — including annual
        // (12×) plans, coupon discounts, and any tax Stripe added
        // via automatic_tax.  Falls back to plan price inside
        // markTenantActive when amount_total is absent.
        $amountTotal = isset($session['amount_total'])
            ? (float) $session['amount_total'] / 100
            : null;

        // Persist the chosen interval (set in checkout() metadata) so
        // downstream receipts and reporting reflect annual vs monthly
        // without re-deriving from price math.
        $interval = (string) ($session['metadata']['leadhub_interval'] ?? 'month');
        if (! in_array($interval, ['month', 'year'], true)) {
            $interval = 'month';
        }
        if ($interval === 'year') {
            $settings = (array) ($tenant->settings ?? []);
            $settings['billing']['interval'] = 'year';
            $tenant->forceFill(['settings' => $settings])->save();
        }

        $this->markTenantActive(
            $tenant,
            $planKey,
            $session['subscription'] ?? null,
            $amountTotal,
            ['leadhub_interval' => $interval],
        );
        $this->bookAffiliateCommission($tenant, $planKey);
        $this->redeemCouponIfPresent($tenant, $planKey, $session);

        // Persist Stripe customer id so /admin/billing can deep-link
        // the user to Stripe's Customer Portal for self-service card
        // updates without re-checkout.
        if (! empty($session['customer'])) {
            $settings = (array) ($tenant->settings ?? []);
            $settings['billing']['stripe_customer_id'] = $session['customer'];
            $settings['billing']['last_gateway']       = 'stripe';
            $tenant->forceFill(['settings' => $settings])->save();
        }
    }

    /**
     * If checkout.session.metadata.leadhub_coupon_code is set, call
     * CouponService::redeem() so:
     *   - coupons.times_used increments (enforces max_uses cap)
     *   - coupon_redemptions row created (enforces max_per_tenant)
     *   - trial-extension coupons actually extend trial_ends_at
     *   - SA-side analytics see who redeemed what
     *
     * Without this, validate() runs but the engine's bookkeeping is
     * never updated — the same code is reusable forever.  The
     * defence-in-depth re-validation inside redeem() handles the
     * rare race where the coupon was exhausted between validate
     * (at checkout start) and redeem (at webhook time).
     */
    private function redeemCouponIfPresent(Tenant $tenant, string $planKey, array $session): void
    {
        $code = $session['metadata']['leadhub_coupon_code'] ?? null;
        if (! $code) return;

        try {
            $coupon = \App\Models\Coupon::query()->where('code', $code)->first();
            if (! $coupon) return;

            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            $price    = $plan ? (float) $plan->price : 0.0;
            $currency = $plan ? (string) ($plan->currency ?? 'USD') : 'USD';

            app(\App\Services\CouponService::class)->redeem(
                coupon: $coupon,
                tenant: $tenant,
                planKey: $planKey,
                basePrice: $price,
                currency: $currency,
                metadata: ['source' => 'stripe.checkout', 'stripe_session_id' => $session['id'] ?? null],
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'coupon-redemption');
        }
    }

    /**
     * If the paying tenant was referred by another tenant, create a
     * pending commission row so the operator can review + pay out.
     * Default 20% commission rate — operator can change per row in
     * the SA admin before approving.
     *
     * Idempotent — guards against duplicate rows on webhook re-delivery
     * by checking for an existing pending row keyed on
     * (referrer × referred × plan_key).
     *
     * Commission policy (intentional v1 choice):
     *   Commission is based on the monthly $plan->price regardless of
     *   the buyer's billing cycle.  An annual subscriber pays 12×
     *   monthly up-front but the referrer earns the same monthly
     *   commission as a month-by-month subscriber.  This keeps
     *   operator payout planning simple — commission rates are set
     *   per cycle, not per payment.  Operators who want commission
     *   on the actual charged amount should override this method
     *   in a subclass.
     */
    private function bookAffiliateCommission(Tenant $tenant, string $planKey): void
    {
        $referrerId = $tenant->referred_by_tenant_id;
        if (! $referrerId) return;

        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount   = (float) $plan->price;
            $currency = strtoupper((string) ($plan->currency ?? 'USD'));
            if ($amount <= 0) return;

            $exists = \App\Models\AffiliateReferral::query()
                ->where('referrer_tenant_id', $referrerId)
                ->where('referred_tenant_id', $tenant->id)
                ->where('plan_key', $planKey)
                ->where('commission_status', \App\Models\AffiliateReferral::STATUS_PENDING)
                ->exists();
            if ($exists) return;

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
                'metadata'            => ['source' => 'stripe.checkout.session.completed'],
            ]);
        } catch (\Throwable $e) {
            $this->logError($e, 'affiliate-commission-booking');
        }
    }

    private function onSubscriptionUpdated(array $subscription, int $eventCreatedAt = 0): void
    {
        $tenant = $this->resolveTenantFromSubscription($subscription);
        if (! $tenant) return;

        $subId = isset($subscription['id']) ? (string) $subscription['id'] : null;

        if (! $this->shouldApplyOrderedEvent($tenant, $subId, $eventCreatedAt)) {
            return;
        }

        // Map Stripe's subscription status enum into LeadHub's local
        // vocabulary.  Without this mapping a card-decline trial
        // tenant landed in TenantSubscriptionState::UNKNOWN (because
        // 'past_due' / 'unpaid' / 'incomplete' aren't local statuses)
        // and EnforceSubscription bounced them to /admin/subscription-
        // required instead of letting them update their card from the
        // billing portal.  Spelling normalisation: Stripe emits
        // 'canceled' (one l), LeadHub uses 'cancelled' (two).
        $stripeStatus = (string) ($subscription['status'] ?? 'active');
        $localStatus = match ($stripeStatus) {
            'active', 'trialing'                                                        => \App\Enums\SubscriptionStatus::Active,
            'canceled', 'paused'                                                        => \App\Enums\SubscriptionStatus::Cancelled,
            'past_due', 'unpaid', 'incomplete', 'incomplete_expired'                    => \App\Enums\SubscriptionStatus::PendingPayment,
            default                                                                     => \App\Enums\SubscriptionStatus::Active,
        };

        // Route through the audited writer so the SA can see this
        // transition in /admin/super/audit alongside every other
        // subscription_status change.
        $tenant->transitionSubscriptionStatus($localStatus);

        // Surface "scheduled cancellation" state in the billing UI so
        // the operator can see the planned end date.  Stripe sets
        // cancel_at_period_end=true when a customer cancels through
        // the customer-portal — the subscription stays 'active' until
        // current_period_end, then transitions to 'canceled'.  Without
        // persisting cancel_at the SA dashboard couldn't distinguish
        // "cancelled but still using the service" from a fresh active
        // tenant, and the renewal-email cron would still fire reminders
        // pointing at upgrade flows.
        if (! empty($subscription['cancel_at_period_end'])) {
            $endTs = (int) ($subscription['current_period_end'] ?? 0);
            $tenant->forceFill([
                'settings' => array_merge((array) $tenant->settings, [
                    'billing' => array_merge(
                        (array) data_get($tenant->settings, 'billing', []),
                        ['cancel_at' => $endTs],
                    ),
                ]),
            ])->save();
        }

        $this->recordOrderedEvent($tenant, $subId, $eventCreatedAt);
    }

    private function onSubscriptionDeleted(array $subscription, int $eventCreatedAt = 0): void
    {
        $tenant = $this->resolveTenantFromSubscription($subscription);
        if (! $tenant) return;

        $subId = isset($subscription['id']) ? (string) $subscription['id'] : null;

        if (! $this->shouldApplyOrderedEvent($tenant, $subId, $eventCreatedAt)) {
            return;
        }

        $tenant->transitionSubscriptionStatus(\App\Enums\SubscriptionStatus::Cancelled);
        app(\App\Services\SubscriptionEventService::class)->cancelled($tenant);
        $this->recordOrderedEvent($tenant, $subId, $eventCreatedAt);
    }

    /**
     * Out-of-order webhook guard.  Stripe can re-deliver events under
     * retry / dashboard "Resend webhook", and Stripe's docs explicitly
     * warn that delivery order is NOT guaranteed.  A delayed
     * customer.subscription.deleted arriving AFTER a fresh
     * customer.subscription.updated would otherwise flip the tenant
     * back to cancelled.
     *
     * The guard is keyed on subscription_id so a tenant that owns
     * MULTIPLE Stripe subscriptions (e.g. test+prod, or upgrade-during-
     * trial creating both) does not silently drop a newer event for
     * sub B because sub A's last event happened to land later.  We
     * compare the inbound event['created'] Unix timestamp to the
     * per-sub timestamp stored under
     * tenants.settings.billing.last_webhook_at_stripe.{$subId} and
     * skip the older delivery.
     *
     * When $subId is null/empty (legacy callers, or events that arrive
     * without a subscription id) we fall back to the tenant-wide bucket
     * `last_webhook_at_stripe._tenant` so single-sub installs that
     * already wrote that key keep working.  $eventCreatedAt = 0
     * (legacy callers without a timestamp) bypasses the check.
     */
    /**
     * Settings-key under `tenants.settings.billing.<key>` where the
     * per-subscription ordered-event timestamps live.  AbstractGateway
     * uses this to gate out-of-order subscription.* deliveries.
     */
    protected function webhookTimestampSettingsKey(): ?string
    {
        return 'last_webhook_at_stripe';
    }

    /**
     * Stripe fires invoice.payment_succeeded on every successful
     * recurring charge.  Book a fresh commission row for the
     * referrer so monthly/yearly renewals keep paying out the
     * affiliate, not just the first charge.
     *
     * Idempotency lives in bookAffiliateCommission() which checks
     * for an existing pending row keyed on plan_key — but we add
     * a per-cycle marker so two renewals in the same plan can
     * both book.  The marker is the invoice.id (Stripe-unique).
     */
    private function onInvoicePaid(array $invoice): void
    {
        $subId = $invoice['subscription'] ?? null;
        if (! $subId) return;

        $tenant = $this->tenantFromSubscriptionId($subId);
        if (! $tenant) return;

        $planKey = $tenant->plan ?? null;
        if (! $planKey) return;

        // Skip the first invoice — that's already booked by
        // checkout.session.completed.  Stripe billing_reason on
        // the very first invoice is 'subscription_create'; renewals
        // are 'subscription_cycle'.
        if (($invoice['billing_reason'] ?? '') === 'subscription_create') {
            return;
        }

        $invoiceId = $invoice['id'] ?? null;

        try {
            $exists = \App\Models\AffiliateReferral::query()
                ->where('referred_tenant_id', $tenant->id)
                ->where('plan_key', $planKey)
                ->whereJsonContains('metadata->stripe_invoice_id', $invoiceId)
                ->exists();
            if ($exists) return;
        } catch (\Throwable) {
            // whereJsonContains may not be supported on older MySQL —
            // fall through to bookAffiliateCommission's coarser
            // pending-row guard.  Worst case: a duplicate that the
            // operator can dedupe in admin.
        }

        // Stamp charge_id alongside invoice_id on both the commission
        // row AND the receipt below — so the refund matcher at
        // onRefund() can still tie a charge.refunded event back to
        // either record when Stripe omits the `invoice` field
        // (out-of-band refunds, manual refunds via Stripe dashboard
        // for one-off payments).  Lookup chain:
        //   1. metadata.stripe_invoice_id matches refund.invoice
        //   2. metadata.stripe_charge_id  matches refund.id
        $renewalChargeId = isset($invoice['charge']) ? (string) $invoice['charge'] : null;

        $this->bookRecurringAffiliateCommission($tenant, $planKey, $invoiceId, $renewalChargeId);

        // Issue a tax receipt for the renewal cycle.  Without this,
        // tenants would only ever receive a receipt for their first
        // payment — every subsequent month/year of paid revenue was
        // silently undocumented.  Idempotent on (tenant, gateway,
        // invoice_id) so Stripe's three retries never duplicate.
        $renewalAmount = (float) ($invoice['amount_paid'] ?? 0) / 100;
        $renewalCurrency = strtoupper((string) ($invoice['currency'] ?? 'USD'));

        $this->issueReceiptForPayment(
            tenant:       $tenant,
            planKey:      $planKey,
            externalId:   $invoiceId,
            actualAmount: $renewalAmount,
            currency:     $renewalCurrency,
            metadata:     array_filter([
                'source'             => 'stripe.invoice.payment_succeeded',
                'invoice_id'         => $invoiceId,
                'stripe_invoice_id'  => $invoiceId,
                'stripe_charge_id'   => $renewalChargeId,
            ]),
        );
    }

    /**
     * Per-cycle commission booking — same shape as
     * bookAffiliateCommission but tags the row with the invoice id
     * so the SA can see which cycle each commission corresponds to.
     *
     * Same monthly-price policy as bookAffiliateCommission applies:
     * the row's amount_attributed is always (float) $plan->price
     * (the monthly base) regardless of whether this renewal is a
     * monthly or yearly cycle.  See bookAffiliateCommission's
     * docblock for the rationale.
     */
    private function bookRecurringAffiliateCommission(
        \App\Models\Tenant $tenant,
        string $planKey,
        ?string $invoiceId,
        ?string $chargeId = null,
    ): void {
        $referrerId = $tenant->referred_by_tenant_id;
        if (! $referrerId) return;

        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount   = (float) $plan->price;
            $currency = strtoupper((string) ($plan->currency ?? 'USD'));
            if ($amount <= 0) return;

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
                'metadata'            => array_filter([
                    'source'             => 'stripe.invoice.payment_succeeded',
                    'stripe_invoice_id'  => $invoiceId,
                    // Stamped so onRefund() can match by charge_id when
                    // Stripe omits `invoice` on the refund payload.
                    'stripe_charge_id'   => $chargeId,
                ]),
            ]);
        } catch (\Throwable $e) {
            $this->logError($e, 'recurring-affiliate-commission');
        }
    }

    /**
     * Stripe charge.refunded → reverse the most recent pending or
     * approved affiliate commission for this tenant.  We never claw
     * back already-paid commissions automatically (operator decides
     * that case-by-case), but we DO flip pending/approved to
     * 'reversed' so the operator doesn't pay out on revenue that was
     * refunded.
     *
     * Also writes an audit log so the SA timeline shows the refund,
     * and issues a NEGATIVE credit-note receipt linked to the
     * original positive receipt — required by EU VAT and most US
     * sales-tax regimes for refund reconciliation.  The original
     * receipt stays untouched (regulators require both the original
     * and the credit note as separate sequentially-numbered
     * documents).
     */
    private function onRefund(array $charge): void
    {
        $customerId = $charge['customer'] ?? null;
        $chargeId   = $charge['id']       ?? null;
        $invoiceId  = $charge['invoice']  ?? null;
        if (! $customerId) return;

        $tenant = $this->tenantFromCustomerId($customerId);
        if (! $tenant) return;

        try {
            // H2: pick the AffiliateReferral row that matches the
            // ACTUAL refunded charge/invoice instead of the most
            // recent one.  The previous `latest('id')->limit(1)`
            // pattern silently clawed back a later renewal's
            // commission while the truly-refunded commission stayed
            // payable.  Order of preference:
            //   1. metadata.stripe_invoice_id matches refund.invoice
            //   2. metadata.stripe_charge_id  matches refund.id
            //   3. fallback: latest pending row (legacy commission
            //      rows that pre-date the metadata stamp)
            $base = \App\Models\AffiliateReferral::query()
                ->where('referred_tenant_id', $tenant->id)
                ->whereIn('commission_status', [
                    \App\Models\AffiliateReferral::STATUS_PENDING,
                    \App\Models\AffiliateReferral::STATUS_APPROVED,
                ]);

            $ref = null;
            if ($invoiceId) {
                $ref = (clone $base)
                    ->whereJsonContains('metadata->stripe_invoice_id', $invoiceId)
                    ->first();
            }
            if (! $ref && $chargeId) {
                $ref = (clone $base)
                    ->whereJsonContains('metadata->stripe_charge_id', $chargeId)
                    ->first();
            }
            if (! $ref) {
                $ref = $base->latest('id')->first();
            }

            if ($ref) {
                $meta = (array) ($ref->metadata ?? []);
                $meta['refunded_at']      = now()->toIso8601String();
                $meta['stripe_charge_id'] = $chargeId;
                $ref->forceFill([
                    'commission_status' => \App\Models\AffiliateReferral::STATUS_REVERSED,
                    'metadata'          => $meta,
                ])->save();
            }

            \App\Models\AuditLog::record(
                action: 'subscription.refund_processed',
                auditable: $tenant,
                oldValues: [],
                newValues: [
                    'tenant_name'      => $tenant->name,
                    'stripe_charge_id' => $chargeId,
                    'amount_refunded'  => isset($charge['amount_refunded'])
                        ? round(($charge['amount_refunded'] / 100), 2) : null,
                    'currency'         => strtoupper((string) ($charge['currency'] ?? 'USD')),
                ],
                tags: 'subscription,refund,billing',
            );

            $this->issueRefundCreditNote($tenant, $charge);
        } catch (\Throwable $e) {
            $this->logError($e, 'refund-handler');
        }
    }

    /**
     * Issue a negative credit-note receipt for a Stripe refund.
     * Looks up the original receipt by (tenant, gateway, external_id)
     * — Stripe's external_id is the charge id for one-off captures or
     * the invoice id for subscription cycles, so we try both.  The
     * credit note's own external_id is prefixed with 'refund_' to
     * keep it distinct from the original (the unique-index on
     * tenant_billing_receipts would otherwise collide).
     *
     * Best-effort: a credit-note write failure does NOT roll back the
     * commission reversal or audit log above (caller's outer try/
     * catch already handles that path).
     */
    private function issueRefundCreditNote(Tenant $tenant, array $charge): void
    {
        $chargeId = $charge['id'] ?? null;
        if (! $chargeId) return;

        $amountRefundedCents = (int) ($charge['amount_refunded'] ?? 0);
        if ($amountRefundedCents <= 0) return; // nothing to credit

        $currency = strtoupper((string) ($charge['currency'] ?? 'USD'));
        $invoiceId = $charge['invoice'] ?? null;

        // Look up the original receipt — Stripe stamps the charge id
        // for one-off captures and the invoice id for subscription
        // cycles, so try both.  When neither matches we still issue
        // the credit note but with refund_of_receipt_number = null so
        // the SA can reconcile manually.
        $originalReceipt = \App\Models\TenantBillingReceipt::query()
            ->where('tenant_id', $tenant->id)
            ->where('gateway', $this->id())
            ->where(function ($q) use ($chargeId, $invoiceId) {
                $q->where('external_id', $chargeId);
                if ($invoiceId) {
                    $q->orWhere('external_id', $invoiceId);
                }
            })
            ->first();

        $planKey = $originalReceipt?->plan_key ?? ($tenant->plan ?? '');

        $negativeAmount = -1 * round($amountRefundedCents / 100, 2);

        \App\Models\TenantBillingReceipt::createForCheckout(
            tenant:     $tenant,
            gateway:    $this->id(),
            externalId: 'refund_' . $chargeId,
            planKey:    (string) $planKey,
            amount:     $negativeAmount,
            currency:   $currency,
            metadata:   [
                'type'                     => 'credit_note',
                'source'                   => 'stripe.charge.refunded',
                'refund_of_charge'         => $chargeId,
                'refund_of_invoice'        => $invoiceId,
                'refund_of_receipt_number' => $originalReceipt?->receipt_number,
            ],
        );
    }

    /**
     * Stripe charge.dispute.created → mark commission reversed and
     * audit-log so the operator sees the chargeback in the SA panel
     * before deciding whether to suspend the tenant.
     */
    private function onDispute(array $dispute): void
    {
        $customerId = $dispute['customer'] ?? null;
        $chargeId   = $dispute['charge']   ?? null;

        // Dispute payload is the dispute object — customer often lives
        // on the parent charge.  Look up via charge id when needed.
        // H5: when Stripe's charge endpoint is unreachable (rate-limit,
        // 5xx, timeout) we throw so the outer handleWebhook try/catch
        // returns false and Stripe retries.  Previously a silent
        // `return` here let markProcessed run on the unprocessed event,
        // permanently dropping the chargeback signal — operator never
        // saw the dispute, suspension never happened.
        if (! $customerId && $chargeId) {
            $resp = Http::asForm()
                ->withBasicAuth($this->settings->stripe_secret_key, '')
                ->get("https://api.stripe.com/v1/charges/{$chargeId}");
            if (! $resp->successful()) {
                throw new \RuntimeException(
                    "Stripe dispute customer lookup failed (status {$resp->status()}); will retry."
                );
            }
            $customerId = $resp->json('customer') ?? null;
        }
        if (! $customerId) return;

        $tenant = $this->tenantFromCustomerId($customerId);
        if (! $tenant) return;

        try {
            // Same metadata-keyed lookup as onRefund (H2): prefer the
            // referral row that actually corresponds to the disputed
            // charge.  Without this, latest('id') would reverse a
            // later renewal's commission while leaving the truly
            // disputed commission payable.
            $base = \App\Models\AffiliateReferral::query()
                ->where('referred_tenant_id', $tenant->id)
                ->whereIn('commission_status', [
                    \App\Models\AffiliateReferral::STATUS_PENDING,
                    \App\Models\AffiliateReferral::STATUS_APPROVED,
                ]);

            $ref = null;
            if ($chargeId) {
                $ref = (clone $base)
                    ->whereJsonContains('metadata->stripe_charge_id', $chargeId)
                    ->first();
            }
            if (! $ref) {
                $ref = $base->latest('id')->first();
            }

            if ($ref) {
                $meta = (array) ($ref->metadata ?? []);
                $meta['disputed_at']      = now()->toIso8601String();
                $meta['stripe_charge_id'] = $chargeId;
                $ref->forceFill([
                    'commission_status' => \App\Models\AffiliateReferral::STATUS_REVERSED,
                    'metadata'          => $meta,
                ])->save();
            }

            \App\Models\AuditLog::record(
                action: 'subscription.dispute_received',
                auditable: $tenant,
                oldValues: [],
                newValues: [
                    'tenant_name'      => $tenant->name,
                    'stripe_charge_id' => $chargeId,
                    'reason'           => (string) ($dispute['reason'] ?? 'unknown'),
                    'amount'           => isset($dispute['amount'])
                        ? round(($dispute['amount'] / 100), 2) : null,
                ],
                tags: 'subscription,dispute,chargeback,billing',
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'dispute-handler');
        }
    }

    /**
     * Resolve a tenant from a Stripe customer id (saved to
     * tenants.settings.billing.stripe_customer_id on checkout).
     */
    private function tenantFromCustomerId(string $customerId): ?Tenant
    {
        // Migration 2026_05_01_000002 added an indexed generated column
        // populated from `settings.billing.stripe_customer_id`.  Querying
        // it directly turns the previous full-table scan into an O(log N)
        // index lookup on every webhook delivery.  The whereJsonContains
        // fallback covers the brief window between the buyer running
        // composer install and `php artisan migrate` adding the column.
        try {
            $tenant = Tenant::query()
                ->where('stripe_customer_id_idx', $customerId)
                ->first();
            if ($tenant) {
                return $tenant;
            }
        } catch (\Throwable) {
            // Column not yet added — fall through to the JSON scan.
        }

        return Tenant::query()
            ->whereJsonContains('settings->billing->stripe_customer_id', $customerId)
            ->first();
    }

    /**
     * Create a Stripe Billing Portal session URL so the tenant owner
     * can self-service:
     *   - Update saved card / payment method
     *   - View invoices and download receipts
     *   - Cancel or pause their subscription
     *
     * Stripe handles all of this UI for us — the buyer doesn't have to
     * build a card-update form, and PCI scope stays at zero on our side.
     *
     * Returns null when:
     *   - Stripe isn't configured (buyer hasn't set up keys yet)
     *   - The tenant has no stripe_customer_id (never paid via Stripe)
     *   - Stripe rejects the request (logged, surfaced as null)
     */
    public function createCustomerPortalSession(Tenant $tenant, string $returnUrl): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $customerId = data_get($tenant->settings, 'billing.stripe_customer_id');
        if (! is_string($customerId) || $customerId === '') {
            return null;
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($this->settings->stripe_secret_key, '')
                ->post('https://api.stripe.com/v1/billing_portal/sessions', [
                    'customer'   => $customerId,
                    'return_url' => $returnUrl,
                ]);

            if ($response->failed()) {
                $this->logError(new \RuntimeException($response->body()), 'customer-portal');
                return null;
            }

            return $response->json('url');
        } catch (\Throwable $e) {
            $this->logError($e, 'customer-portal');
            return null;
        }
    }

    private function onPaymentFailed(array $invoice): void
    {
        $subId  = $invoice['subscription'] ?? null;
        $tenant = $subId ? $this->tenantFromSubscriptionId($subId) : null;
        if (! $tenant) return;

        app(\App\Services\SubscriptionEventService::class)->paymentFailed(
            $tenant,
            amount: number_format(($invoice['amount_due'] ?? 0) / 100, 2, '.', ''),
            currency: strtoupper($invoice['currency'] ?? 'USD'),
        );
    }

    private function resolveTenantFromSubscription(array $subscription): ?Tenant
    {
        return $this->tenantFromSubscriptionId($subscription['id'] ?? null);
    }

    private function tenantFromSubscriptionId(?string $id): ?Tenant
    {
        if (! $id) return null;

        // Indexed-column lookup first (migration 2026_05_01_000002).
        // Falls back to whereJsonContains when the column hasn't been
        // added yet — same compatibility window as tenantFromCustomerId.
        try {
            $tenant = Tenant::query()
                ->where('stripe_subscription_id_idx', $id)
                ->first();
            if ($tenant) {
                return $tenant;
            }
        } catch (\Throwable) {
            // Column not yet added — fall through to the JSON scan.
        }

        return Tenant::query()
            ->whereJsonContains('settings->stripe_subscription_id', $id)
            ->first();
    }
}
