<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGatewayManager;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Gateway-agnostic billing entry point. Delegates every real decision
 * to PaymentGatewayManager so the controller itself has zero provider
 * knowledge — adding a new gateway means dropping a driver class into
 * App\Billing\Gateways and registering it in the manager.
 */
class BillingController extends Controller
{
    public function __construct(protected PaymentGatewayManager $manager)
    {
    }

    /**
     * GET /billing/checkout/{gateway}/{plan}
     * Kick off a checkout flow for the given gateway + plan.
     */
    public function checkout(Request $request, string $gateway, string $plan): RedirectResponse|View
    {
        \App\Support\DemoMode::abortIfOn(__('filament/demo_mode.checkout_blocked'));

        $tenant   = $this->resolvedTenant($request);
        $planRow  = Plan::where('key', $plan)->first();

        if (! $planRow) {
            return redirect()->back()->withErrors(['billing' => __('messages.billing_unknown_plan', ['plan' => $plan])]);
        }

        try {
            $driver = $this->manager->driver($gateway);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['billing' => $e->getMessage()]);
        }

        // Stash billing interval (month|year) in session so gateways
        // that opt into annual pricing can pick the right unit_amount
        // and recurring.interval.  Default = month.
        $interval = (string) $request->input('interval', 'month');
        if (! in_array($interval, ['month', 'year'], true)) {
            $interval = 'month';
        }
        $request->session()->put('pending_interval', $interval);

        // Coupon preflight: validate the code (if any) before handing
        // off to the gateway.  Stash in session so the gateway picks
        // it up via resolvePendingCoupon().  Invalid codes surface
        // as a flash error and the user stays on the pricing page.
        $couponCode = (string) $request->input('coupon', '');
        if ($couponCode !== '') {
            $check = app(\App\Services\CouponService::class)->validate(
                code: $couponCode,
                tenant: $tenant,
                planKey: $planRow->key,
                basePrice: (float) $planRow->price,
                currency: $planRow->currency,
            );

            if (! $check['valid']) {
                return redirect()->back()->withErrors([
                    'billing' => __('auth_flow.coupon.prefix') . ($check['reason_label'] ?? __('auth_flow.coupon.invalid_code')),
                ]);
            }

            $request->session()->put('pending_coupon_code', $check['coupon']->code);
        } else {
            // Clear any stale pending coupon from a previous attempt.
            $request->session()->forget('pending_coupon_code');
        }

        $result = $driver->checkout($tenant, $planRow);

        if ($result->isRedirect()) {
            return redirect()->away($result->url);
        }

        if ($result->isInstructions()) {
            return view('billing.manual-instructions', [
                'result' => $result,
                'plan'   => $planRow,
                'tenant' => $tenant,
            ]);
        }

        return redirect()->back()->withErrors(['billing' => $result->message ?? __('messages.billing_checkout_failed')]);
    }

    /**
     * POST /billing/webhook/{gateway}
     * Gateway-specific inbound webhook endpoint.
     */
    public function webhook(Request $request, string $gateway): Response
    {
        try {
            $driver = $this->manager->driver($gateway);
        } catch (\InvalidArgumentException) {
            return response('Unknown gateway', 404);
        }

        return $driver->handleWebhook($request)
            ? response('OK', 200)
            : response('Webhook rejected', 400);
    }

    /**
     * GET /billing/razorpay/launch/{order} — intermediate view that
     * boots Razorpay's client-side modal. Only used when no plan_id
     * is mapped (one-off orders).
     */
    public function razorpayLaunch(Request $request, string $order): View
    {
        $tenant = $this->resolvedTenant($request);
        return view('billing.razorpay-launch', [
            'orderId' => $order,
            'keyId'   => app(\App\Settings\BillingSettings::class)->razorpay_key_id,
            'tenant'  => $tenant,
        ]);
    }

    /**
     * GET /billing/receipts/{id}.pdf — download a sequentially-
     * numbered tax receipt for a payment we've collected from this
     * tenant.  Authorisation: only members of the tenant the receipt
     * belongs to can download it (plus super-admins).
     */
    public function receiptPdf(Request $request, int $id): \Symfony\Component\HttpFoundation\Response
    {
        // Tenant-scope the lookup itself so an attacker can never enumerate
        // receipt ids — a wrong-tenant id 404s instead of leaking existence.
        // Super-admins bypass the tenant scope so they can audit any receipt.
        if ($request->user()?->is_super_admin) {
            $receipt = \App\Models\TenantBillingReceipt::findOrFail($id);
            $tenant  = $receipt->tenant ?? $this->resolvedTenant($request);
        } else {
            $tenant = $this->resolvedTenant($request);
            $receipt = \App\Models\TenantBillingReceipt::query()
                ->where('id', $id)
                ->where('tenant_id', $tenant->id)
                ->firstOrFail();
        }

        $plan = \App\Models\Plan::query()->where('key', $receipt->plan_key)->first();
        $companyName = $this->companyNameForReceipt();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('billing.receipt-pdf', [
            'receipt'      => $receipt,
            'tenant'       => $receipt->tenant ?? $tenant,
            'plan'         => $plan,
            'company_name' => $companyName,
        ])->setPaper('a4');

        return $pdf->stream($receipt->receipt_number . '.pdf');
    }

    protected function companyNameForReceipt(): string
    {
        try {
            $name = trim((string) (app(\App\Settings\GeneralSettings::class)->company_name ?? ''));
            if ($name !== '') return $name;
        } catch (\Throwable) {
            // GeneralSettings may not have company_name on legacy installs
        }
        return (string) config('app.name', __('controllers/billing.operator_fallback'));
    }

    /**
     * GET /billing/portal — redirect tenant owner to Stripe's hosted
     * Billing Portal so they can update their card, view invoices, or
     * cancel without leaving their account session.
     *
     * Only Stripe is supported here for now — PayPal/Razorpay/Paystack
     * tenants have to re-checkout to update payment method since their
     * APIs don't expose an equivalent portal.
     */
    public function customerPortal(Request $request): RedirectResponse
    {
        $tenant = $this->resolvedTenant($request);

        try {
            $stripe = $this->manager->driver('stripe');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('filament.admin.pages.billing')
                ->withErrors(['billing' => $e->getMessage()]);
        }

        if (! $stripe instanceof \App\Billing\Gateways\StripeGateway) {
            return redirect()->route('filament.admin.pages.billing')
                ->withErrors(['billing' => __('messages.billing_portal_stripe_only')]);
        }

        $url = $stripe->createCustomerPortalSession(
            $tenant,
            \App\Support\AdminUrl::for('billing'),
        );

        if (! $url) {
            return redirect()->route('filament.admin.pages.billing')
                ->withErrors(['billing' => __('messages.billing_portal_unavailable')]);
        }

        return redirect()->away($url);
    }

    protected function resolvedTenant(Request $request): Tenant
    {
        $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;
        if (! $tenant && $request->user()) {
            $tenant = $request->user()->tenant;
        }
        if (! $tenant) {
            abort(403, __('messages.no_workspace_resolved'));
        }
        return $tenant;
    }
}
