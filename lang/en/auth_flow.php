<?php

declare(strict_types=1);

return [
    'calendar_oauth' => [
        'consent_rejected'       => 'Provider rejected the consent: :error',
        'no_authorization_code'  => 'No authorization code received from provider.',
        'token_exchange_failed'  => 'Could not exchange authorization code. Check :provider client credentials in your services config.',
        'connection_save_failed' => 'Could not save the connection. Try again.',
        'connected_success'      => ':provider Calendar connected as :email.',
    ],

    'recaptcha' => [
        'verification_failed'       => 'reCAPTCHA verification failed. Please refresh the page and try again.',
        'verification_failed_short' => 'reCAPTCHA verification failed. Please refresh and try again.',
    ],

    'invoice_payment' => [
        'gateway_not_supported' => 'The selected gateway does not yet support invoice payment. Please choose another.',
        'start_failed'          => 'Could not start payment. Please try another method.',
    ],

    'password_setup' => [
        'invalid_or_expired' => 'This setup link is invalid or has expired. Use the "Forgot password" link on the sign-in page to request a new one.',
    ],

    'coupon' => [
        'prefix'       => 'Coupon: ',
        'invalid_code' => 'Invalid code.',
    ],

    'oauth' => [
        'no_token_url'             => 'No token URL for :type',
        'token_exchange_failed'    => 'Token exchange failed (:status): :body',
        'salesforce_invalid_url'   => 'Salesforce instance_url must be on *.salesforce.com or *.force.com (got :host).',
        'salesforce_safety_failed' => 'Salesforce instance_url failed safety check: :error',
        'salesforce_token_failed'  => 'Salesforce token exchange failed (:status): :body',
        'meta_token_failed'        => 'Meta token exchange failed: :body',
        'meta_no_access_token'     => 'No access_token returned from Meta',
    ],
];
