<?php

namespace App\Billing\Gateways;

use App\Billing\AbstractGateway;
use App\Billing\CheckoutResult;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * PayPal driver using PayPal REST v2 Orders API (one-off captures).
 * For a true recurring subscription the buyer would map a PayPal
 * `plan_id` on the Plan model; if that's set we use /v1/billing
 * subscriptions, otherwise we create a one-off checkout.
 *
 * Credentials come from BillingSettings. Access tokens are cached
 * for ~8 minutes so we don't re-auth every checkout click.
 */
class PayPalGateway extends AbstractGateway
{
    public function id(): string
    {
        return 'paypal';
    }

    public function label(): string
    {
        return 'PayPal';
    }

    public function isConfigured(): bool
    {
        return filled($this->settings->paypal_client_id)
            && filled($this->settings->paypal_client_secret);
    }

    public function checkout(Tenant $tenant, Plan $plan): CheckoutResult
    {
        if (! $this->isConfigured()) {
            return CheckoutResult::error((string) __('billing_gateways.paypal.not_configured'));
        }

        try {
            $token = $this->accessToken();

            $couponCode = (string) (session('pending_coupon_code') ?? '');
            $customId = $tenant->id . ':' . $plan->key . ':' . $couponCode;

            // Resolve the buyer's chosen interval.  PayPal recurring
            // requires a separate plan id per cycle (PayPal-side plans
            // are immutable on price+frequency), so annual is honored
            // only when the operator pre-creates a yearly plan and
            // maps it via meta.paypal_plan_id_yearly.  For one-off
            // captures we just charge the annual base price directly.
            //
            // Annual billing on PayPal: requires the operator to
            // pre-create a yearly plan in PayPal and map its id via
            // `meta.paypal_plan_id_yearly` on the Plan row.  Until
            // that mapping exists the recurring path falls back to
            // monthly silently — surfacing this in the SA UI is the
            // remaining follow-up work.
            $interval = (string) (session('pending_interval') ?? $plan->interval ?? 'month');
            if (! in_array($interval, ['month', 'year'], true)) {
                $interval = 'month';
            }

            $monthlyPlanId = $plan->meta['paypal_plan_id'] ?? null;
            $yearlyPlanId  = $plan->meta['paypal_plan_id_yearly'] ?? null;

            // Fail loud rather than silently downgrading the buyer's
            // chosen cycle.  Without this check, a yearly selection on
            // a plan that only has paypal_plan_id (monthly) mapped
            // would subscribe the buyer monthly — they'd pay one
            // month and renew a year later at a price that doesn't
            // match what the marketing page advertised.  Operators
            // see this error and either pre-create the yearly plan in
            // PayPal + map paypal_plan_id_yearly, or remove yearly
            // from the public pricing page.
            if ($interval === 'year' && empty($yearlyPlanId) && ! empty($monthlyPlanId)) {
                return CheckoutResult::error((string) __('billing_gateways.paypal.annual_plan_id_missing'));
            }

            $recurringPlanId = $interval === 'year' && $yearlyPlanId
                ? $yearlyPlanId
                : $monthlyPlanId;

            // Recurring via Billing Subscriptions when a plan_id is mapped
            if (! empty($recurringPlanId)) {
                $res = Http::withToken($token)->post($this->baseUrl() . '/v1/billing/subscriptions', [
                    'plan_id'      => $recurringPlanId,
                    'custom_id'    => $customId,
                    'application_context' => [
                        'return_url' => $this->successUrl($tenant),
                        'cancel_url' => $this->cancelUrl($tenant),
                        // Pass the buyer's UI locale so PayPal's
                        // hosted approval page renders in their
                        // language.  PayPal expects xx_XX format
                        // (e.g. 'en_US', 'es_ES', 'ar_EG') so the
                        // helper maps the LeadHub two-letter locale
                        // to PayPal's regional form.  Without this
                        // a Spanish/Arabic/Hindi buyer sees an
                        // English approval page even though the
                        // pricing page that sent them was localised.
                        'locale'     => $this->paypalLocale(),
                    ],
                ]);

                $approve = collect($res->json('links', []))->firstWhere('rel', 'approve')['href'] ?? null;
                return $approve
                    ? CheckoutResult::redirect($approve)
                    : CheckoutResult::error((string) __('billing_gateways.paypal.no_approval_link'));
            }

            // Fallback: one-off capture.  Use basePriceFor() so annual
            // checkouts charge the operator's configured yearly price
            // (or 12× monthly fallback) instead of a single month.
            $captureAmount = $plan->basePriceFor($interval);

            $res = Http::withToken($token)->post($this->baseUrl() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $customId,
                    'description'  => $plan->name,
                    'custom_id'    => $customId,
                    'amount' => [
                        'currency_code' => strtoupper($plan->currency ?? 'USD'),
                        'value' => number_format($captureAmount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => $this->successUrl($tenant),
                    'cancel_url' => $this->cancelUrl($tenant),
                    'user_action' => 'PAY_NOW',
                    // Same buyer-locale handling as the recurring
                    // path above — keeps the one-off PayPal hosted
                    // approval page localised in the buyer's
                    // language instead of falling back to English.
                    'locale'      => $this->paypalLocale(),
                ],
            ]);

            if ($res->failed()) {
                $this->logError(new \RuntimeException($res->body()), 'checkout');
                return CheckoutResult::error((string) __('billing_gateways.paypal.checkout_failed'));
            }

            $approve = collect($res->json('links', []))->firstWhere('rel', 'approve')['href'] ?? null;
            return $approve
                ? CheckoutResult::redirect($approve)
                : CheckoutResult::error((string) __('billing_gateways.paypal.no_approval_link'));
        } catch (\Throwable $e) {
            $this->logError($e, 'checkout');
            return CheckoutResult::error((string) __('billing_gateways.paypal.error', ['error' => $e->getMessage()]));
        }
    }

    public function handleWebhook(Request $request): bool
    {
        $event = $request->json()->all();
        $type  = $event['event_type'] ?? null;

        // Verify the signature unless the operator has explicitly
        // opted out (paypal_skip_verify=true).  Without verification,
        // anyone with curl can POST a forged BILLING.SUBSCRIPTION.
        // ACTIVATED event and unlock paid plans for free.
        if (! $this->verifySignature($request, $event)) {
            return false;
        }

        // Inbound idempotency — PayPal retries every webhook up to 3
        // times; without dedup we'd book commission / redeem coupons
        // multiple times per event.
        $eventId = (string) ($event['id'] ?? '');
        if ($eventId !== '' && \App\Models\ProcessedBillingEvent::wasProcessed('paypal', $eventId)) {
            return true; // already processed — return 200 so PayPal stops retrying
        }

        try {
            match ($type) {
                'CHECKOUT.ORDER.APPROVED',
                'PAYMENT.CAPTURE.COMPLETED'             => $this->onPaymentCaptured($event),
                'BILLING.SUBSCRIPTION.ACTIVATED'        => $this->onSubscriptionActivated($event),
                'BILLING.SUBSCRIPTION.PAYMENT.SUCCEEDED'=> $this->onSubscriptionRenewal($event),
                'BILLING.SUBSCRIPTION.CANCELLED'        => $this->onSubscriptionCancelled($event),
                'BILLING.SUBSCRIPTION.PAYMENT.FAILED'   => $this->onPaymentFailed($event),
                default                                 => null,
            };
        } catch (\Throwable $e) {
            $this->logError($e, 'webhook/' . $type);
            return false;
        }

        if ($eventId !== '') {
            \App\Models\ProcessedBillingEvent::markProcessed('paypal', $eventId, (string) $type);
        }

        return true;
    }

    /**
     * PayPal webhook signature verification via
     * /v1/notifications/verify-webhook-signature.  The operator
     * configures their webhook id at PayPal and stores it in
     * BillingSettings.paypal_webhook_id; this method assembles
     * the verify-signature payload from the inbound headers and
     * the raw body.
     *
     * Bypass: paypal_skip_verify=true skips the call.  Default OFF
     * because skipping makes the webhook trivially forgeable.
     */
    protected function verifySignature(Request $request, array $event): bool
    {
        if (($this->settings->paypal_skip_verify ?? false) === true) {
            return true; // operator opt-out
        }

        $webhookId = (string) ($this->settings->paypal_webhook_id ?? '');
        if ($webhookId === '') {
            $this->logError(
                new \RuntimeException('paypal_webhook_id not set; rejecting by default. Set paypal_skip_verify=true to opt out (NOT recommended).'),
                'webhook/verify-signature'
            );
            return false;
        }

        try {
            $token = $this->accessToken();

            $res = Http::withToken($token)
                ->acceptJson()
                ->post($this->baseUrl() . '/v1/notifications/verify-webhook-signature', [
                    'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO', ''),
                    'cert_url'          => $request->header('PAYPAL-CERT-URL', ''),
                    'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID', ''),
                    'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG', ''),
                    'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME', ''),
                    'webhook_id'        => $webhookId,
                    'webhook_event'     => $event,
                ]);

            if ($res->failed()) {
                $this->logError(new \RuntimeException($res->body()), 'webhook/verify-signature');
                return false;
            }

            return $res->json('verification_status') === 'SUCCESS';
        } catch (\Throwable $e) {
            $this->logError($e, 'webhook/verify-signature');
            return false;
        }
    }

    /* -------------------------------------------------------- */

    /**
     * Map the LeadHub two-letter app locale into PayPal's xx_XX
     * format for the application_context.locale parameter.  PayPal
     * accepts ISO 639-1 + ISO 3166-1 (e.g. en_US, es_ES, ar_EG,
     * hi_IN) and falls back to English when the supplied locale
     * isn't in its supported set.  The mapping below covers every
     * locale LeadHub ships out of the box; new locales added to
     * config/locales.php should be appended here.
     */
    private function paypalLocale(): string
    {
        $map = [
            'en' => 'en_US',
            'es' => 'es_ES',
            'ar' => 'ar_EG',
            'hi' => 'hi_IN',
        ];

        $locale = (string) app()->getLocale();
        return $map[$locale] ?? 'en_US';
    }

    private function baseUrl(): string
    {
        return $this->settings->paypal_sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    private function accessToken(): string
    {
        return Cache::remember('leadhub.paypal.token', 480, function () {
            $res = Http::asForm()
                ->withBasicAuth($this->settings->paypal_client_id, $this->settings->paypal_client_secret)
                ->post($this->baseUrl() . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

            if ($res->failed()) {
                throw new \RuntimeException((string) __('billing_gateways.paypal.auth_failed', ['body' => $res->body()]));
            }

            return $res->json('access_token');
        });
    }

    private function onPaymentCaptured(array $event): void
    {
        $custom = $event['resource']['custom_id']
            ?? $event['resource']['purchase_units'][0]['reference_id']
            ?? null;

        if (! $custom || ! str_contains($custom, ':')) return;
        $parts = explode(':', $custom, 3);
        $tenantId   = $parts[0] ?? null;
        $planKey    = $parts[1] ?? null;
        $couponCode = $parts[2] ?? '';
        if (! $tenantId || ! $planKey) return;

        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $resourceId = $event['resource']['id'] ?? null;

            // Forward the gateway-reported amount + currency so the
            // receipt reflects the actual capture.  Annual checkouts
            // pay basePriceFor('year') (12× monthly or operator-defined
            // annual) — without this the receipt always showed the
            // monthly $plan->price regardless of what was charged.
            [$actualAmount, $_currency] = $this->extractAmount($event['resource'] ?? []);
            $interval = $this->resolveInterval();

            $this->markTenantActive(
                $tenant,
                $planKey,
                $resourceId,
                $actualAmount,
                ['leadhub_interval' => $interval],
            );
            $this->bookAffiliateCommission($tenant, $planKey, $resourceId, 'paypal.checkout.completed');
            $this->redeemCouponIfPresent($tenant, $planKey, $couponCode, $resourceId, 'paypal.checkout.completed');
        }
    }

    private function onSubscriptionActivated(array $event): void
    {
        $custom = $event['resource']['custom_id'] ?? null;
        if (! $custom || ! str_contains($custom, ':')) return;
        $parts = explode(':', $custom, 3);
        $tenantId   = $parts[0] ?? null;
        $planKey    = $parts[1] ?? null;
        $couponCode = $parts[2] ?? '';
        if (! $tenantId || ! $planKey) return;

        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $resourceId = $event['resource']['id'] ?? null;

            // Same fan-out fix as onPaymentCaptured — pull the actual
            // billing-info value when PayPal stamps it on the
            // subscription, so the receipt mirrors the actual charge
            // rather than the monthly $plan->price.
            $billingInfo = $event['resource']['billing_info']['last_payment'] ?? [];
            [$actualAmount, $_currency] = $this->extractAmount(
                ! empty($billingInfo) ? $billingInfo : ($event['resource'] ?? [])
            );
            $interval = $this->resolveInterval();

            $this->markTenantActive(
                $tenant,
                $planKey,
                $resourceId,
                $actualAmount,
                ['leadhub_interval' => $interval],
            );
            $this->bookAffiliateCommission($tenant, $planKey, $resourceId, 'paypal.subscription.activated');
            $this->redeemCouponIfPresent($tenant, $planKey, $couponCode, $resourceId, 'paypal.subscription.activated');
        }
    }

    /**
     * Pull amount + currency from a PayPal resource shape.  PayPal
     * Orders v2 puts the value at amount.value (string) with
     * amount.currency_code; subscription billing_info.last_payment
     * uses the same shape.  Returns [null, null] when neither is
     * present so the caller falls back to plan->price.
     */
    private function extractAmount(array $resource): array
    {
        $amountNode = $resource['amount']
            ?? $resource['purchase_units'][0]['amount']
            ?? null;

        if (! is_array($amountNode)) {
            return [null, null];
        }

        $value = $amountNode['value'] ?? $amountNode['total'] ?? null;
        $currency = $amountNode['currency_code'] ?? null;

        if ($value === null || $value === '') {
            return [null, $currency];
        }

        return [(float) $value, $currency];
    }

    /**
     * Resolve the buyer's chosen interval (month|year) from session.
     * BillingController stashes pending_interval before checkout so
     * the receipt metadata reflects the cycle the buyer paid for.
     */
    private function resolveInterval(): string
    {
        $interval = (string) (session('pending_interval') ?? 'month');
        return in_array($interval, ['month', 'year'], true) ? $interval : 'month';
    }

    /**
     * If the custom_id stamped a coupon code, redeem it via
     * CouponService (writes coupon_redemptions row + increments
     * times_used).  Empty couponCode is a no-op so the unencoded
     * `tenantId:planKey:` PayPal id case stays safe.
     */
    private function redeemCouponIfPresent(Tenant $tenant, string $planKey, string $couponCode, ?string $resourceId, string $source): void
    {
        if ($couponCode === '') return;

        try {
            $coupon = \App\Models\Coupon::query()->where('code', $couponCode)->first();
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
                metadata: ['source' => $source, 'paypal_resource_id' => $resourceId],
            );
        } catch (\Throwable $e) {
            $this->logError($e, 'coupon-redemption');
        }
    }

    /**
     * Mirror of StripeGateway::bookAffiliateCommission for PayPal.
     * Same 20% rate.  Idempotency is per-resource-id so each
     * PayPal event (CHECKOUT.ORDER.APPROVED, BILLING.SUBSCRIPTION.
     * ACTIVATED, BILLING.SUBSCRIPTION.PAYMENT.SUCCEEDED for renewals)
     * books a fresh commission row, while webhook retries with the
     * same id don't double-book.
     *
     * Commission policy (intentional v1 choice):
     *   amount_attributed is the monthly $plan->price regardless of
     *   the buyer's billing cycle.  Annual subscribers don't 12×
     *   the referrer's payout.  Operators who want commission on
     *   the actual charged amount should override this method.
     *   See StripeGateway::bookAffiliateCommission docblock for the
     *   full rationale.
     */
    private function bookAffiliateCommission(Tenant $tenant, string $planKey, ?string $resourceId, string $source): void
    {
        $referrerId = $tenant->referred_by_tenant_id;
        if (! $referrerId) return;

        try {
            $plan = \App\Models\Plan::query()->where('key', $planKey)->first();
            if (! $plan) return;

            $amount   = (float) $plan->price;
            $currency = strtoupper((string) ($plan->currency ?? 'USD'));
            if ($amount <= 0) return;

            // Webhook-retry guard.  Renewals come with a different
            // resource id each cycle so they still book; same id
            // (retried delivery) skips.
            if ($resourceId) {
                try {
                    $exists = \App\Models\AffiliateReferral::query()
                        ->where('referred_tenant_id', $tenant->id)
                        ->where('plan_key', $planKey)
                        ->whereJsonContains('metadata->paypal_resource_id', $resourceId)
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
                    'source'              => $source,
                    'paypal_resource_id'  => $resourceId,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logError($e, 'affiliate-commission-booking');
        }
    }

    /**
     * BILLING.SUBSCRIPTION.PAYMENT.SUCCEEDED fires on every renewal
     * after the initial activation.  We use it to book recurring
     * affiliate commissions — without this, only the first payment
     * paid out the referrer.
     *
     * Looks up the tenant via the parent subscription id (custom_id
     * stamped at subscription creation), then books a fresh
     * commission row keyed on the renewal capture id.
     */
    private function onSubscriptionRenewal(array $event): void
    {
        $resource = $event['resource'] ?? [];
        $billingAgreementId = $resource['billing_agreement_id']
            ?? $resource['supplementary_data']['related_ids']['order_id']
            ?? null;

        // PayPal stamps custom_id on the subscription itself; on a
        // renewal capture it's available as either custom or
        // custom_id depending on event shape.
        $custom = $resource['custom_id']
            ?? $resource['custom']
            ?? null;

        if (! $custom || ! str_contains($custom, ':')) return;
        $parts = explode(':', $custom, 3);
        $tenantId = $parts[0] ?? null;
        $planKey  = $parts[1] ?? null;
        if (! $tenantId || ! $planKey) return;

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        $captureId = $resource['id'] ?? $billingAgreementId ?? null;
        $this->bookAffiliateCommission(
            $tenant,
            $planKey,
            $captureId,
            'paypal.subscription.payment.succeeded',
        );

        // Issue a tax receipt for this renewal cycle.  Without this,
        // tenants would only ever receive a receipt for their first
        // PayPal payment — every subsequent renewal of paid revenue
        // was silently undocumented.  Idempotent on (tenant, gateway,
        // captureId) so PayPal's retries never duplicate.
        $amountValue = $resource['amount']['total']
            ?? $resource['amount']['value']
            ?? null;
        $renewalAmount = $amountValue !== null ? (float) $amountValue : 0.0;
        $renewalCurrency = strtoupper((string) ($resource['amount']['currency_code'] ?? 'USD'));
        $this->issueReceiptForPayment(
            tenant:       $tenant,
            planKey:      $planKey,
            externalId:   $captureId,
            actualAmount: $renewalAmount,
            currency:     $renewalCurrency,
            metadata:     [
                'source'             => 'paypal.subscription.payment.succeeded',
                'paypal_capture_id'  => $captureId,
            ],
        );
    }

    private function onSubscriptionCancelled(array $event): void
    {
        $custom = $event['resource']['custom_id'] ?? null;
        if (! $custom || ! str_contains($custom, ':')) return;
        [$tenantId, $_] = explode(':', $custom, 2);

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        // Out-of-order webhook guard.  PayPal can re-deliver events
        // under retry; a delayed BILLING.SUBSCRIPTION.CANCELLED
        // arriving AFTER a fresh BILLING.SUBSCRIPTION.ACTIVATED
        // would otherwise flip the tenant back to cancelled.  Compare
        // event['create_time'] (ISO8601, e.g. "2026-04-28T14:30:00Z")
        // to the last applied PayPal event timestamp keyed by PayPal
        // subscription id (resource.id, e.g. "I-XXXXXX") so a tenant
        // with two active subscriptions does not collapse their
        // ordering into a single bucket.
        $createTime = (string) ($event['create_time'] ?? '');
        $eventCreatedAt = $createTime !== '' ? (int) strtotime($createTime) : 0;

        $resource = $event['resource'] ?? [];
        $subId = isset($resource['id']) ? (string) $resource['id'] : null;

        if (! $this->shouldApplyOrderedEvent($tenant, $subId, $eventCreatedAt)) {
            return;
        }

        $tenant->transitionSubscriptionStatus(\App\Enums\SubscriptionStatus::Cancelled);
        app(\App\Services\SubscriptionEventService::class)->cancelled($tenant);
        $this->recordOrderedEvent($tenant, $subId, $eventCreatedAt);
    }

    /**
     * Settings-key under `tenants.settings.billing.<key>` where the
     * per-subscription ordered-event timestamps live.  AbstractGateway
     * uses this to gate out-of-order subscription.* deliveries.
     */
    protected function webhookTimestampSettingsKey(): ?string
    {
        return 'last_webhook_at_paypal';
    }

    private function onPaymentFailed(array $event): void
    {
        $custom = $event['resource']['custom_id'] ?? null;
        if (! $custom || ! str_contains($custom, ':')) return;
        [$tenantId, $_] = explode(':', $custom, 2);

        $tenant = Tenant::find($tenantId);
        if (! $tenant) return;

        app(\App\Services\SubscriptionEventService::class)->paymentFailed(
            $tenant,
            amount: $event['resource']['amount']['value'] ?? null,
            currency: strtoupper($event['resource']['amount']['currency_code'] ?? 'USD'),
        );
    }
}
