<?php

namespace App\Billing\Contracts;

use App\Models\Invoice;

/**
 * Optional capability a PaymentGateway can implement to accept
 * arbitrary-amount payments for a TENANT→CUSTOMER Invoice (as opposed
 * to the subscription-style tenant-pays-platform flow served by
 * {@see PaymentGateway::checkout()}).
 *
 * Gateways that don't implement this interface are simply hidden from
 * the invoice pay-now UI — see PublicInvoiceController and the Blade
 * view. This keeps the existing 5 subscription drivers untouched.
 */
interface InvoiceCheckoutGateway
{
    /**
     * Create a hosted-checkout session for the given invoice and
     * return the URL the customer should be redirected to.
     *
     * Return null if the gateway is configured but the session could
     * not be created (logs + falls back to manual instructions).
     */
    public function createInvoiceCheckout(Invoice $invoice, string $successUrl, string $cancelUrl): ?string;
}
