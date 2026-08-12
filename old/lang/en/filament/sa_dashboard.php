<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Dashboard page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_dashboard.<key>').
*/

return [
    'title'                            => 'LeadHub Super Admin',

    // Activity feed headings
    'recent_workspaces'                => 'Recent workspaces',
    'recent_payments'                  => 'Recent payments',

    // Hero strip
    'hero_heading'                     => 'Welcome back, operator',
    'hero_subheading'                  => 'Live snapshot of every workspace, payment and lead source on this LeadHub install.',
    'refresh_badge'                    => 'Refreshes every 5 min',

    // Secondary KPI cards
    'card_total_leads'                 => 'Total Leads',
    'card_leads_this_month'            => 'Leads This Month',
    'card_new_tenants_30d'             => 'New Tenants (30d)',
    'card_avg_leads_per_tenant'        => 'Avg Leads / Tenant',

    // Activity feeds — links + empty states
    'view_all_tenants'                 => 'View all →',
    'view_billing'                     => 'View billing →',
    'feed_empty_workspaces'            => 'No workspaces yet.',
    'feed_empty_payments'              => 'No payments collected yet.',
    'seats_count'                      => ':used/:max seats',
    'removed_workspace'                => 'Removed workspace',
];
