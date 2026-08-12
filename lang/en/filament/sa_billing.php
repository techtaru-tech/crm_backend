<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Billing dashboard — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_billing.<key>').
*/

return [

    // ----- Navigation / title -----
    'nav_label' => 'Billing',
    'title'     => 'Billing & Revenue',

    // ─── Hero KPI cards ───
    'kpi_mrr_label'             => 'Monthly Recurring Revenue',
    'kpi_arr_label'             => 'Annual Run Rate',
    'kpi_arr_sub'               => 'MRR × 12',
    'kpi_arpu_label'            => 'Average Revenue / Customer',
    'kpi_arpu_sub'              => 'ARPU · monthly · :currency',
    'kpi_churn_label'           => 'Churn Rate (30d)',
    'kpi_churn_sub'             => 'cancelled or expired / total',
    'kpi_mrr_sub_singular'      => 'from :count paying tenant',
    'kpi_mrr_sub_plural'        => 'from :count paying tenants',

    // ─── Status breakdown ───
    'section_status_breakdown'  => 'Subscription Status Breakdown',
    'status_active'             => 'Active',
    'status_trial'              => 'Trial',
    'status_trial_expired'      => 'Trial Expired',
    'status_cancelled'          => 'Cancelled',
    'status_expired'            => 'Expired',

    // ─── Revenue by plan ───
    'section_revenue_by_plan'   => 'Revenue by Plan',
    'col_plan'                  => 'Plan',
    'col_price'                 => 'Price',
    'col_customers'             => 'Customers',
    'col_mrr'                   => 'MRR',
    'free_badge'                => 'free',

    // ─── Tenant growth ───
    'section_tenant_growth'     => 'Tenant Growth (6 months)',
    'tenant_growth_title'       => ':total total · :new new',

    // ─── Recent activity ───
    'section_recent_activity'   => 'Recent Subscription Activity',
    'empty_recent_events'       => 'No recent events.',
    'col_tenant'                => 'Tenant',
    'col_status'                => 'Status',
    'col_updated'               => 'Updated',

];
