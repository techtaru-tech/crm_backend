<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Dashboard widgets — Filament admin strings
|------------------------------------------------------------
| Headings + dataset labels for the 5 SuperAdmin dashboard
| widgets (TenantSignupsChart, MrrTrendChart, LeadSourceMixChart,
| SubscriptionStatusChart, SuperAdminSetupChecklist).
|
| Accessed via __('filament/super_admin_widgets.<section>.<key>').
| Buyers translate by copying this file to
| lang/<locale>/filament/super_admin_widgets.php.
*/

return [

    // ─── TenantSignupsChart ─────────────────────────────────────────────
    'tenant_signups' => [
        'heading'        => 'New tenant signups (last 12 months)',
        'dataset_label'  => 'Signups',
    ],

    // ─── MrrTrendChart ──────────────────────────────────────────────────
    'mrr_trend' => [
        'heading'        => 'Revenue collected (last 12 months)',
        'dataset_label'  => 'Revenue',
    ],

    // ─── LeadSourceMixChart ─────────────────────────────────────────────
    'lead_source_mix' => [
        'heading'        => 'Top 5 lead sources (all tenants)',
        'dataset_label'  => 'Leads',
    ],

    // ─── SubscriptionStatusChart ────────────────────────────────────────
    // status_<key> entries match BillingMetricsService::statusBreakdown()
    // canonical statuses. Missing keys fall through to the ucfirst() form
    // at the call site, so unknown future statuses never render raw
    // snake_case in the chart legend.
    'subscription_status' => [
        'heading'                => 'Subscription status mix',
        'status_active'          => 'Active',
        'status_trial'           => 'Trial',
        'status_trial_expired'   => 'Trial expired',
        'status_cancelled'       => 'Cancelled',
        'status_expired'         => 'Expired',
        'status_suspended'       => 'Suspended',
        'status_past_due'        => 'Past due',
    ],

    // ─── SuperAdminSetupChecklist ───────────────────────────────────────
    'setup_checklist' => [
        'item_smtp'       => 'Configure SMTP so emails actually send',
        'item_gateway'    => 'Enable at least one payment gateway',
        'item_brand'      => 'Set your brand name + colours',
        'item_landing'    => 'Edit landing-page hero copy',
        'item_workspace'  => 'Onboard your first workspace',
    ],

];
