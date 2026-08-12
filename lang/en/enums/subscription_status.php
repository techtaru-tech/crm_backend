<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SubscriptionStatus enum — translatable case labels
|------------------------------------------------------------
| Accessed via __('enums/subscription_status.<case_value>').
*/

return [
    'trial'             => 'Trial',
    'trial_expired'     => 'Trial expired',
    'active'            => 'Active',
    'pending_payment'   => 'Awaiting payment',
    'cancelled'         => 'Cancelled',
    'expired'           => 'Expired',
    'pending_deletion'  => 'Scheduled for deletion',
];
