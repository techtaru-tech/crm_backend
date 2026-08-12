<?php

namespace App\Billing\Contracts;

use App\Billing\CheckoutResult;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;

/**
 * Contract every payment driver must implement. The goal is a uniform
 * entry point so BillingController doesn't care which gateway is active,
 * and the script owner can enable multiple gateways side-by-side from
 * the Super Admin Billing Settings page.
 */
interface PaymentGateway
{
    /**
     * Stable machine id (stripe, paypal, razorpay, paystack, manual).
     */
    public function id(): string;

    /**
     * Human label for Settings UI and pricing-page buttons.
     */
    public function label(): string;

    /**
     * True when credentials for this gateway have been filled in.
     * Gateways are never offered to tenants until they report true.
     */
    public function isConfigured(): bool;

    /**
     * Kick off a checkout for the given tenant + plan. Implementations
     * either return a hosted-checkout redirect URL (Stripe/PayPal etc.)
     * or a rendered instructions view (Manual bank transfer).
     */
    public function checkout(Tenant $tenant, Plan $plan): CheckoutResult;

    /**
     * Handle the gateway's inbound webhook. Return true when the event
     * was verified and processed so the controller can respond 200.
     */
    public function handleWebhook(Request $request): bool;
}
