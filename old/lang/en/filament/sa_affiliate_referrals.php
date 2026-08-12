<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin AffiliateReferralResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_affiliate_referrals.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_affiliate_referrals.php.
*/

return [
    'nav_label'            => 'Affiliate Commissions',
    'model_label'          => 'Affiliate Commission',
    'plural_model_label'   => 'Affiliate Commissions',

    // Table columns
    'col_affiliate'        => 'Affiliate',
    'col_referred'         => 'Referred Workspace',
    'col_plan'             => 'Plan',
    'col_commission'       => 'Commission',
    'col_rate'             => 'Rate',
    'col_status'           => 'Status',
    'col_booked'           => 'Booked',
    'col_paid_at'          => 'Paid On',

    // Filter
    'filter_status'        => 'Status',

    // Row actions
    'action_approve'       => 'Approve',
    'action_mark_paid'     => 'Mark as Paid',
    'action_reverse'       => 'Reverse',

    // Row-action notifications
    'notify_approved'      => 'Commission approved.',
    'notify_paid'          => 'Commission marked as paid.',
    'notify_reversed'      => 'Commission reversed.',

    // Bulk actions
    'bulk_approve'         => 'Approve selected',
    'bulk_mark_paid'       => 'Mark selected as paid',
    'notify_bulk_approved' => ':count commission(s) approved.',
    'notify_bulk_paid'     => ':count commission(s) marked as paid.',

    // Empty state
    'empty_heading'        => 'No affiliate commissions yet',
    'empty_description'    => 'Commissions are booked automatically when a referred workspace makes a payment.',
];
