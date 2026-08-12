<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SubscriptionRequired — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/subscription_required.<key>').
*/

return [
    'title'                    => 'Subscription Required',

    // Reasons - headings
    'heading_trial_expired'    => 'Your trial has ended',
    'heading_cancelled'        => 'Your subscription is cancelled',
    'heading_expired'          => 'Your subscription has expired',
    'heading_suspended'        => 'This workspace is suspended',
    'heading_default'          => 'A subscription is required',

    // Reasons - subheadings
    'subheading_trial_expired' => 'Your 14-day trial has ended. Choose a plan below to continue using LeadHub.',
    'subheading_cancelled'     => 'Your subscription has been cancelled. Reactivate to regain access.',
    'subheading_expired'       => 'Your subscription payment has lapsed. Please renew to regain access.',
    'subheading_suspended'     => 'Please contact your administrator.',
    'subheading_default'       => 'Please choose a plan to continue.',

    // Footer / actions
    'sign_out'                 => 'Sign out',

    // ─── Page body (resources/views/filament/pages/subscription-required.blade.php) ──
    'current_status_prefix'    => 'Current Status:',
    'most_popular_tag'         => 'Most Popular',
    'price_per_interval'       => '/:interval',
    'seats_unlimited'          => 'Unlimited team seats',
    'seats_count'              => ':count team seats',
    'leads_unlimited'          => 'Unlimited leads',
    'leads_count'              => ':count leads',
    'forms_unlimited'          => 'Unlimited forms',
    'forms_count'              => ':count forms',
    'feature_integrations'     => 'Integrations',
    'feature_api_access'       => 'API Access',
    'feature_custom_domain'    => 'Custom Domain',
    'pay_with_label'           => 'Pay with :gateway',
    'contact_sales_btn'        => 'Contact Sales — :plan',
    'switch_accounts_label'    => 'Need to switch accounts?',
    'sales_mailto_subject'     => 'Upgrade to :plan',
    'interval_month'           => 'month',
    'interval_year'            => 'year',
    'interval_week'            => 'week',
    'interval_day'             => 'day',
];
