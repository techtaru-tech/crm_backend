<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — SaasStatsOverview (Super Admin) translation strings
|--------------------------------------------------------------------------
|
| Stat labels, descriptions and trend copy for the Super Admin dashboard
| overview widget.
| Consumed via __('filament/widget_saas_stats.<key>').
|
*/

return [

    'total_tenants'              => 'Total Workspaces',
    'total_tenants_description'  => 'All registered tenants',
    'active_subs'                => 'Active Subscriptions',
    'active_subs_description'    => 'Paying & in good standing',
    'mrr'                        => 'MRR',
    'mrr_description'            => 'Monthly recurring revenue',
    'signups_month'              => 'Signups This Month',

    // ─── Trend description fragments ─────────────────────────────────
    'vs_last_month'              => 'last month',
    'no_data_for_comparison'     => 'No data for comparison',
    'new_no_prior_data'          => 'New — no prior :label data',
    'trend_vs'                   => ':sign:pct% vs :label',
];
