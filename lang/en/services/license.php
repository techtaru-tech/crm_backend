<?php

declare(strict_types=1);

return [
    'no_key_provided'        => 'No license key provided.',
    'no_key_configured'      => 'No license key configured.',
    'verify_url_missing'     => 'License verification server is not configured. Set LEADHUB_LICENSE_VERIFY_URL in your .env file.',
    'key_not_found'          => 'License key not found. Please check your key and try again.',
    'not_valid_for_domain'   => 'License is not valid for this domain.',
    'server_error_http'      => 'License server returned an error (HTTP :status). Please try again or contact support.',
    'key_not_valid'          => 'License key is not valid.',
    'verified'               => 'License verified.',
    'default_license_type'   => 'Regular License',
    'cannot_reach_server'    => 'Could not reach the license server. If already verified, your cached status will remain active.',
    'demo_mode_active'       => 'Demo mode is active — license verification is bypassed for the live preview.',
    'item_id_mismatch'       => 'This purchase code is for CodeCanyon item #:actual, not LeadHub (#:expected). Use the code from your LeadHub purchase.',
    'activated_via_heartbeat' => 'License accepted — the licensing server is temporarily unreachable, but your install is unlocked while we retry in the background.',
    'unexpected_response'    => 'The licensing server returned an unexpected response. Try again in a minute or contact support.',
    'token_decode_failed'    => 'The licensing server returned a verification token we could not decode with your purchase code. Re-enter your code and try again.',
    'token_claims_mismatch'  => 'The verification token does not match the purchase record. Re-enter your purchase code to refresh the activation.',
    'cached_recent'          => 'License status was checked recently — using cached result.',
    'not_yet_activated'      => 'This install has not been activated yet. Enter your purchase code on Settings → License.',
    'invalid_verification_id_format' => 'The stored verification ID is malformed. Re-activate by re-entering your purchase code.',
    'purchase_code_mismatch' => 'The purchase code on file does not match the verification record. Re-activate by re-entering your purchase code.',
];
