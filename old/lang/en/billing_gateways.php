<?php

declare(strict_types=1);

/*
 * Buyer-facing strings shown when payment-gateway checkout fails or
 * when the manual bank-transfer gateway hands instructions back to the
 * user.  Brand names (Stripe, PayPal, Razorpay, Paystack) stay literal
 * inside the translated values — only the surrounding copy translates.
 *
 * Error-message variants follow the pattern:
 *   ':error'  → exception message interpolated by the gateway
 *   ':body'   → raw HTTP response body (PayPal auth failures)
 *   ':plan'   → plan name suffix on the manual-instructions view
 */
return [
    'stripe' => [
        'not_configured'  => 'Stripe is not configured.',
        'checkout_failed' => 'Stripe checkout failed.',
        'start_failed'    => 'Unable to start Stripe checkout: :error',
        // Suffix appended to the Stripe Checkout product line when an
        // active coupon discounts the plan price.  Shown to the buyer
        // on the hosted Stripe Checkout page so they can see the coupon
        // code that produced the discounted total at a glance.
        'product_coupon_suffix' => ' — coupon :code',
    ],
    'razorpay' => [
        'not_configured'        => 'Razorpay is not configured.',
        'subscription_failed'   => 'Razorpay subscription failed.',
        'order_creation_failed' => 'Razorpay order creation failed.',
        'error'                 => 'Razorpay error: :error',
        // Razorpay plans are immutable on price+frequency so honoring
        // an annual billing intent requires the operator to pre-create
        // a yearly plan in their Razorpay dashboard and store its id
        // on Plan.  Until that schema work lands we fail loud rather
        // than silently downgrading the buyer to monthly.
        'annual_not_supported'  => 'Annual billing on Razorpay is not yet supported. Please choose monthly billing or contact support to enable a yearly plan.',
    ],
    'paystack' => [
        'not_configured'  => 'Paystack is not configured.',
        'checkout_failed' => 'Paystack checkout failed.',
        'error'           => 'Paystack error: :error',
        // Paystack plan codes encode price+interval at creation time so
        // an annual billing intent requires the operator to pre-create
        // a yearly plan in their Paystack dashboard and map its code on
        // Plan.  Same fail-loud guard as Razorpay above.
        'annual_not_supported' => 'Annual billing on Paystack is not yet supported. Please choose monthly billing or contact support to enable a yearly plan.',
    ],
    'paypal' => [
        'not_configured'   => 'PayPal is not configured.',
        'no_approval_link' => 'PayPal did not return an approval link.',
        'checkout_failed'  => 'PayPal checkout failed.',
        'error'            => 'PayPal error: :error',
        'auth_failed'      => 'PayPal auth failed: :body',
        // PayPal subscription plans live in the PayPal dashboard and are
        // referenced by plan_id.  When the operator hasn't mapped a
        // yearly plan_id on Plan we refuse the checkout rather than
        // silently subscribing the buyer to the monthly plan_id.
        'annual_plan_id_missing' => 'Annual billing on PayPal is not configured for this plan. Ask the workspace owner to create a yearly plan in PayPal and map its id via meta.paypal_plan_id_yearly, or choose monthly billing.',
    ],
    'manual' => [
        'not_configured'     => 'Manual bank transfer is not configured.',
        'instructions_intro' => 'Please transfer the amount below and include the reference. Your plan will be activated once our team confirms the payment.',
        'plan_suffix'        => ':plan plan',
        'labels'             => [
            'bank'           => 'Bank',
            'account_name'   => 'Account Name',
            'account_number' => 'Account Number',
            'iban'           => 'IBAN',
            'swift_bic'      => 'SWIFT / BIC',
            'amount'         => 'Amount',
            'reference'      => 'Reference',
        ],
    ],
];
