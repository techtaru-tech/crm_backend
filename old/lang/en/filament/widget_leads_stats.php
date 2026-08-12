<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadsStatsOverview widget — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/widget_leads_stats.<key>').
*/

return [

    'no_data_for_comparison' => 'No data for comparison',

    // ─── Stat labels (8 cards) ───
    'total_leads'         => 'Total Leads',
    'new_today'           => 'New Today',
    'new_this_week'       => 'New This Week',
    'this_month'          => 'This Month',
    'conversion_rate'     => 'Conversion Rate',
    'active_in_pipeline'  => 'Active in Pipeline',
    'avg_response_time'   => 'Avg Response Time',
    'won_deals'           => 'Won Deals',

    // ─── Trend description templates ───
    'trend_no_prior'      => 'New — no prior :label data',
    'trend_vs'            => ':sign:pct% vs :label',

    // ─── Comparison labels used inside trend phrases ───
    'vs_label_prev_30_days' => 'prev 30 days',
    'vs_label_yesterday'    => 'yesterday',
    'vs_label_last_week'    => 'last week',
    'vs_label_last_30_days' => 'last 30 days',
    'vs_label_prior_period' => 'prior period',

    // ─── Suffix annotations ───
    'suffix_new_leads'       => '(new leads)',
    'suffix_lower_is_better' => '(lower is better)',
    'suffix_of_all_leads'    => ':pct% of all leads',

    // ─── Misc bodies ───
    'time_to_first_activity' => 'Time to first activity',
    'no_leads_yet'           => 'No leads yet',
    'conversion_trend'       => ':sign:pct% vs last 30 days',

    // ─── Duration unit suffixes (avg response time) ───
    'minute_short'           => 'm',
    'hour_short'             => 'h',
];
