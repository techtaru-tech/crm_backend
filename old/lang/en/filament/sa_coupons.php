<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin CouponResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_coupons.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_coupons.php.
*/

return [

    // ----- Navigation -----
    'nav_label'                   => 'Coupons',

    // ----- Code & description section -----
    'code_helper'                 => 'Customers type this at checkout. Case-insensitive — stored uppercase.',
    'description_helper'          => 'Internal note — what this code is for, which campaign, expected redemption count.',

    // ----- Discount section -----
    'discount_type_helper'        => 'Percent shaves a %. Fixed shaves an amount in a specific currency. Trial extension adds days to the tenant\'s trial — no money discount.',
    'discount_value_suffix_days'  => 'days',
    'discount_value_helper_percent' => '0–100. 100 means "free for the discount window".',
    'discount_value_helper_fixed' => 'Whole-currency amount, e.g. 20 for $20.',
    'discount_value_helper_trial' => 'Days added to the tenant\'s trial_ends_at on redemption.',
    'currency_helper'             => 'Leave empty to apply to any currency.',

    // ----- Limits & targeting section -----
    'max_total_uses'              => 'Max total uses',
    'max_total_uses_placeholder'  => 'Unlimited',
    'max_total_uses_helper'       => 'Total redemptions across all tenants. Leave empty for unlimited.',
    'max_per_tenant'              => 'Max per tenant',
    'max_per_tenant_helper'       => 'How many times one tenant may redeem this code. 1 = first-time-only.',
    'applies_to_plans_placeholder'=> 'All plans',
    'applies_to_plans_helper'     => 'Leave empty to apply to every plan.',

    // ----- Validity window section -----
    'starts_at_placeholder'       => 'Now',
    'starts_at_helper'            => 'Empty = active immediately.',
    'ends_at_placeholder'         => 'Never expires',
    'ends_at_helper'              => 'Empty = no expiry.',
    'is_active_helper'            => 'Inactive codes never validate, even within the date window.',

    // ----- Table columns -----
    'column_type'                 => 'Type',
    'column_value'                => 'Value',
    'column_uses'                 => 'Uses',
    'column_status'               => 'Status',
    'column_ends_at_placeholder'  => 'Never',
    'trial_days_suffix'           => 'trial days',

    // ----- Filters -----
    'filter_label_discount_type'  => 'Type',
    'filter_active'               => 'Active',
    'filter_active_yes'           => 'Yes',
    'filter_active_no'            => 'No',

    // ----- Field labels (form + table) -----
    'code'                        => 'Code',
    'description'                 => 'Description',
    'discount_type'               => 'Discount type',
    'discount_value'              => 'Discount value',
    'currency'                    => 'Currency',
    'applies_to_plans'            => 'Applies to plans',
    'starts_at'                   => 'Starts at',
    'ends_at'                     => 'Ends at',
    'is_active'                   => 'Active',
    'created_at'                  => 'Created at',

    // ----- Model labels -----
    'model_label'                 => 'Coupon',
    'plural_model_label'          => 'Coupons',

    // ----- Status badge labels (table column) -----
    'status_active'               => 'Active',
    'status_scheduled'            => 'Scheduled',
    'status_expired'              => 'Expired',
    'status_exhausted'            => 'Exhausted',
    'status_inactive'             => 'Inactive',

];
