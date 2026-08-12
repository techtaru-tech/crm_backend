<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin PlanResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_plans.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_plans.php.
*/

return [

    // ----- Navigation -----
    'nav_label'                     => 'Plans',
    'tabs_outer'                    => 'Plan',

    // ----- Tabs -----
    'tab_basics'                    => 'Basics',
    'tab_limits'                    => 'Limits',
    'tab_features'                  => 'Features',
    'tab_gateway_ids'               => 'Gateway IDs',

    // ----- Basics tab -----
    'plan_key'                      => 'Plan Key',
    'plan_key_helper'               => 'Internal identifier. Lowercase, no spaces. Used in URLs and code.',
    'name_helper'                   => 'Shown on the pricing page and billing dashboard.',

    // ----- Pricing section -----
    'monthly_price'                 => 'Monthly price',
    'monthly_price_helper'          => 'Recurring monthly amount.',
    'interval_monthly'              => 'Monthly',
    'interval_yearly'               => 'Yearly',
    'interval_weekly'               => 'Weekly',
    'interval_daily'                => 'Daily',
    // Short interval labels (badge column on table — short, capitalized).
    'interval_month'                => 'Month',
    'interval_year'                 => 'Year',
    'interval_helper'               => 'How often the price recurs after the trial.',
    'trial_days'                    => 'Trial days',
    'trial_days_suffix'             => 'days',
    'trial_days_helper'             => 'Free trial length when a workspace starts on this plan. 0 = no trial — billed from day one.',
    'annual_price'                  => 'Annual price (upfront)',
    'annual_price_helper'           => 'Optional. Total upfront amount for 12 months. Leave empty if you don\'t offer annual billing on this plan.',
    'annual_discount_percent'       => 'Annual discount %',
    'annual_discount_percent_helper'=> 'Display-only — feeds the "Save N%" badge on the pricing page (e.g. 20 = "Save 20% with annual billing").',

    // ----- Visibility section -----
    'active'                        => 'Active',
    'active_helper'                 => 'Inactive plans are hidden everywhere and cannot be subscribed to.',
    'public'                        => 'Public',
    'public_helper'                 => 'Show on the public pricing page.',
    'highlight'                     => 'Highlight',
    'highlight_helper'              => 'Marks this plan as the recommended one (badge on pricing page).',
    'sort_order'                    => 'Sort Order',
    'sort_order_helper'             => 'Lower numbers appear first.',

    // ----- Limits tab -----
    'limits_description'            => 'Use -1 for unlimited, 0 to disable a feature entirely, or any positive integer for a hard cap.',
    'limit_key'                     => 'Limit Key',
    'limit_value'                   => 'Value',
    'add_limit'                     => 'Add Limit',

    // ----- Features tab -----
    'features_description'          => 'Toggle which features this plan unlocks. Values must be "true" or "false".',
    'feature_key'                   => 'Feature Key',
    'feature_enabled'               => 'Enabled',
    'add_feature'                   => 'Add Feature',

    // ----- Gateway IDs tab -----
    'gateway_ids_description'       => 'Map this plan to the product/price ID in each payment gateway. Leave blank if the gateway is disabled.',
    'stripe_price_id'               => 'Stripe Price ID',
    'paddle_price_id'               => 'Paddle Price ID',
    'razorpay_plan_id'              => 'Razorpay Plan ID',
    'paystack_plan_code'            => 'Paystack Plan Code',

    // ----- Table columns -----
    'column_number'                 => '#',
    'column_active'                 => 'Active',
    'column_public'                 => 'Public',
    'column_highlight'              => 'Highlight',

    // ----- Filters -----
    'filter_active_label'           => 'Active',
    'filter_active'                 => 'Active',
    'filter_inactive'               => 'Inactive',
    'filter_label_interval'         => 'Interval',

    // ----- Field labels (form + table) -----
    'name'                          => 'Name',
    'description'                   => 'Description',
    'currency'                      => 'Currency',
    'interval'                      => 'Interval',
    'limits'                        => 'Limits',
    'features'                      => 'Features',
    'price'                         => 'Price',
    'updated_at'                    => 'Updated at',

    // ----- Model labels -----
    'model_label'                   => 'Plan',
    'plural_model_label'            => 'Plans',

];
