<?php

declare(strict_types=1);

return [
    'enforce_2fa' => [
        'required' => 'Two-factor authentication is required. Please enable it to continue.',
    ],

    'login_lockout' => [
        'too_many_attempts' => 'Too many failed attempts. Please try again in :count minute.|Too many failed attempts. Please try again in :count minutes.',
    ],

    'not_suspended' => [
        'scheduled_for_deletion' => 'This workspace is scheduled for deletion. Contact your workspace owner if you need to cancel.',
        'suspended'              => 'Your account has been suspended. Please contact your workspace admin.',
    ],

    'seat_limit' => [
        'reached_short'     => 'Seat limit reached.',
        'reached_short_alt' => 'Your workspace has reached its maximum number of seats.',
        'reached_full'      => 'This workspace has reached its maximum number of seats (:max). Upgrade your plan to add more members.',
    ],

    'impersonation' => [
        'expired'     => 'Impersonation session expired after :minutes minutes.',
        'invalidated' => 'Impersonation session invalidated: super admin no longer valid.',
    ],

    'resolve_tenant' => [
        'not_found'        => 'Workspace not found',
        'not_found_detail' => 'No workspace is registered for the domain: :host',
    ],
];
