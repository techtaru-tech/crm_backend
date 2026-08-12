<?php

namespace App\Http\Controllers;

use App\Billing\Contracts\InvoiceCheckoutGateway;
use App\Billing\PaymentGatewayManager;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Settings\BillingSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Customer-facing invoice endpoints (public access gated by the
 * 64-char opaque token on the Invoice row — no auth required).
 *
 * ── Business-logic context ──────────────────────────────────────────
 * LeadHub has TWO distinct invoice concepts that can be easy to mix up:
 *
 *   1. TENANT → LEAD invoices  (handled by this controller)
 *      A tenant admin (the end-customer of the SaaS) bills one of
 *      their own leads/companies for services sold.  The lead gets a
 *      public /invoice/{token} URL, pays via Stripe/PayPal/Razorpay/
 *      Paystack or sees bank details for a manual transfer, and the
 *      payment flows into the tenant's own gateway account (NOT the
 *      SaaS operator's coffers).
 *
 *   2. SAAS OPERATOR → TENANT subscription billing
 *      Entirely separate.  Lives under /admin/billing + the operator's
 *      /super-admin/billing dashboard.  Tenants pay for their SaaS
 *      seats via the platform-level gateway; they don't see a public
 *      /invoice/ URL for their subscription.
 *
 * This file deals with category 1 only.  If you're looking for
 * subscription invoicing, see SubscriptionGatewayController +
 * BillingSettings.
 *
 * ── Payment flow ────────────────────────────────────────────────────
 *   1. GET  /invoice/{token}         — Blade view with "Pay Now" per
 *                                      tenant-enabled gateway
 *   2. POST /invoice/{token}/pay     — create gateway checkout session,
 *                                      302 to the hosted page
 *   3. GET  /invoice/{token}/success — record payment, show receipt
 *   4. GET  /invoice/{token}/pdf     — render invoice PDF via DomPDF
 *
 * The "Manual" gateway means no online payment: the show page renders
 * the tenant's configured bank-transfer details and the lead pays
 * out-of-band; the tenant marks the invoice paid manually later.
 */
class PublicInvoiceController extends Controller
{
    public function __construct(protected PaymentGatewayManager $manager)
    {
    }

    public function show(string $token): View|Response
    {
        $invoice = $this->resolve($token);

        // Public-facing invoice page — a customer opens a payment link
        // sent by a tenant.  If BillingSettings is uninitialised
        // (partial install, missing seed) the bare resolve would 500,
        // leaving the customer with an unexplained server error.
        // Pass null on failure; the blade renders without the
        // bank-details panel in that case.
        try {
            $manual = app(BillingSettings::class);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('public.invoice.show: BillingSettings unavailable', [
                'invoice_token' => substr($token, 0, 6) . '…',
                'error'         => $e->getMessage(),
            ]);
            $manual = null;
        }

        return response()->view('public.invoice.show', [
            'invoice'  => $invoice,
            'gateways' => $this->availableInvoiceGateways(),
            'manual'   => $manual,
        ])->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function pay(string $token, Request $request)
    {
        $invoice = $this->resolve($token);

        if ($invoice->amountDue() <= 0) {
            return redirect()->route('invoice.show', $token)->with('info', __('messages.invoice_already_paid'));
        }

        $gatewayId = $request->validate([
            'gateway' => 'required|string|in:stripe,paypal,razorpay,paystack,manual',
        ])['gateway'];

        // Manual gateway → just show the show page with the bank details
        // (the Blade already renders them). No external checkout needed.
        if ($gatewayId === 'manual') {
            return redirect()->route('invoice.show', $token)->with('info', __('messages.invoice_pay_manual_instructions'));
        }

        try {
            $driver = $this->manager->driver($gatewayId);
        } catch (\InvalidArgumentException) {
            abort(404);
        }

        if (! $driver instanceof InvoiceCheckoutGateway) {
            return redirect()->route('invoice.show', $token)->withErrors([
                'gateway' => __('auth_flow.invoice_payment.gateway_not_supported'),
            ]);
        }

        $successUrl = route('invoice.success', $token);
        $cancelUrl  = route('invoice.show', $token);

        $url = $driver->createInvoiceCheckout($invoice, $successUrl, $cancelUrl);

        if (! $url) {
            return redirect()->route('invoice.show', $token)->withErrors([
                'gateway' => __('auth_flow.invoice_payment.start_failed'),
            ]);
        }

        // Record a pending ledger row so the tenant can see the attempt.
        InvoicePayment::create([
            'tenant_id'  => $invoice->tenant_id,
            'invoice_id' => $invoice->id,
            'gateway'    => $gatewayId,
            'amount'     => $invoice->amountDue(),
            'currency'   => $invoice->currency,
            'status'     => InvoicePayment::STATUS_PENDING,
            'meta'       => ['initiated_at' => now()->toIso8601String()],
        ]);

        return redirect()->away($url);
    }

    public function paymentSuccess(string $token, Request $request): View
    {
        $invoice = $this->resolve($token);

        // Render-only thank-you page.  We DO NOT write succeeded
        // status here, even with a session_id query param — anyone
        // with the public token could otherwise mark an invoice paid
        // by hitting `?session_id=anything`.  The gateway webhook
        // (handled by BillingController::webhook) is the single
        // source of truth for payment status.  If the webhook hasn't
        // landed yet the user sees the most recent state on
        // `$invoice->fresh()`; the page can poll or refresh until
        // the webhook arrives.
        return view('public.invoice.paid', [
            'invoice' => $invoice->fresh(),
        ]);
    }

    public function pdf(string $token): SymfonyResponse
    {
        $invoice = $this->resolve($token);

        $pdf = Pdf::loadView('public.invoice.pdf', [
            'invoice' => $invoice,
            'logo'    => \App\Support\PdfBranding::logoDataUri($invoice->tenant),
        ])->setPaper('a4');

        return $pdf->stream($invoice->invoice_number . '.pdf');
    }

    /* -------------------------------------------------------- */

    protected function resolve(string $token): Invoice
    {
        $invoice = Invoice::withoutGlobalScope('tenant')
            ->with(['items.product', 'lead', 'company', 'tenant', 'payments'])
            ->where('public_token', $token)
            ->first();

        abort_if(! $invoice, 404);

        return $invoice;
    }

    /**
     * Gateways that are (a) enabled in BillingSettings, (b) configured
     * with credentials, and (c) implement InvoiceCheckoutGateway (or
     * are the Manual driver, which is always eligible if enabled).
     *
     * @return array<string, string>  [gateway_id => label]
     */
    protected function availableInvoiceGateways(): array
    {
        $out = [];
        foreach ($this->manager->enabled() as $id => $driver) {
            if ($id === 'manual') {
                $out[$id] = $driver->label();
                continue;
            }
            if ($driver instanceof InvoiceCheckoutGateway) {
                $out[$id] = $driver->label();
            }
        }
        return $out;
    }
}
