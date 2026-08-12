<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Tenant Reports — shared Filament strings
|------------------------------------------------------------
| Accessed via __('filament/reports.<key>').
*/

return [
    // Report titles + navigation labels
    'agent_performance_title'      => 'Agent Performance Report',
    'agent_performance_nav'        => 'Agent Performance',
    'automation_stats_title'       => 'Automation Stats',
    'automation_stats_nav'         => 'Automation Stats',
    'form_analytics_title'         => 'Form Analytics',
    'form_analytics_nav'           => 'Form Analytics',
    'lead_volume_title'            => 'Lead Volume Report',
    'lead_volume_nav'              => 'Lead Volume',
    'pipeline_funnel_title'        => 'Pipeline Funnel',
    'pipeline_funnel_nav'          => 'Pipeline Funnel',
    'response_time_title'          => 'Response Time Report',
    'response_time_nav'            => 'Response Time',
    'source_performance_title'     => 'Source Performance Report',
    'source_performance_nav'       => 'Source Performance',

    // Common export actions
    'export_csv'                   => 'Export CSV',
    'export_pdf'                   => 'Export PDF',

    // Section / chart headings inside individual report views
    'agent_performance_section'    => 'Agent Performance',
    'automation_performance'       => 'Automation Performance',
    'sequence_performance'         => 'Sequence Performance',
    'submission_trend'             => 'Submission Trend',
    'top_forms'                    => 'Top Forms',
    'breakdown'                    => 'Breakdown',
    'pipeline_funnel_section'      => 'Pipeline Funnel',
    'stage_details'                => 'Stage Details',
    'response_time_distribution'   => 'Response Time Distribution',
    'distribution_breakdown'       => 'Distribution Breakdown',
    'lead_volume_by_source'        => 'Lead Volume by Source',
    'source_breakdown'             => 'Source Breakdown',

    // ─── Form Analytics view (resources/views/filament/pages/reports/form-analytics-report.blade.php) ──
    'total_submissions_label'      => 'Total Submissions: :count',
    'fa_form_name_col'             => 'Form Name',
    'fa_status_col'                => 'Status',
    'fa_submissions_col'           => 'Submissions',
    'fa_chart_dataset_label'       => 'Submissions',
    'fa_status_active'             => 'Active',
    'fa_status_inactive'           => 'Inactive',
    'fa_no_submissions_in_period'  => 'No form submissions in this period',

    // ─── Agent Performance view (resources/views/filament/pages/reports/agent-performance-report.blade.php) ──
    'ap_section_sub'               => 'Sparkline shows weekly assigned leads (last 4 weeks)',
    'ap_agent_col'                 => 'Agent',
    'ap_assigned_col'              => 'Assigned',
    'ap_won_col'                   => 'Won',
    'ap_win_rate_col'              => 'Win Rate',
    'ap_avg_response_col'          => 'Avg Response',
    'ap_avg_close_col'             => 'Avg Close',
    'ap_activities_col'            => 'Activities',
    'ap_trend_col'                 => 'Trend',
    'ap_no_agents_in_period'       => 'No agents found for this period',

    // ─── Automation Stats view (resources/views/filament/pages/reports/automation-stats-report.blade.php) ──
    'as_automation_col'            => 'Automation',
    'as_trigger_col'               => 'Trigger',
    'as_status_col'                => 'Status',
    'as_total_runs_col'            => 'Total Runs',
    'as_success_runs_col'          => 'Success Runs',
    'as_success_rate_col'          => 'Success Rate',
    'as_avg_run_time_col'          => 'Avg Run Time',
    'as_badge_active'              => 'Active',
    'as_badge_disabled'            => 'Disabled',
    'as_no_runs_in_period'         => 'No automation runs in this period',

    // ─── Email Sequences view (resources/views/filament/pages/reports/email-sequences-report.blade.php) ──
    'es_no_sequences'              => 'No sequences yet.',
    'es_sequence_col'              => 'Sequence',
    'es_status_col'                => 'Status',
    'es_enrolled_col'              => 'Enrolled',
    'es_completed_col'             => 'Completed',
    'es_replied_col'               => 'Replied',
    'es_reply_rate_col'            => 'Reply Rate',
    'es_open_rate_col'             => 'Open Rate',
    'es_status_active'             => 'Active',
    'es_status_paused'             => 'Paused',
    'es_status_draft'              => 'Draft',
    'es_status_archived'           => 'Archived',

    // ─── Lead Volume view (resources/views/filament/pages/reports/lead-volume-report.blade.php) ──
    'lv_source_label'              => 'Source',
    'lv_source_placeholder'        => 'e.g. facebook, api…',
    'lv_group_by_label'            => 'Group By',
    'lv_group_by_day'              => 'Day',
    'lv_group_by_week'             => 'Week',
    'lv_group_by_month'            => 'Month',
    'lv_total_pill'                => 'Total: :count leads',
    'lv_leads_over_time'           => 'Leads Over Time',
    'lv_source_separator'          => '· Source: :source',
    'lv_chart_dataset_label'       => 'Leads',
    'lv_period_col'                => 'Period',
    'lv_leads_col'                 => 'Leads',
    'lv_no_data_in_period'         => 'No data for this period',

    // ─── Pipeline Funnel view (resources/views/filament/pages/reports/pipeline-funnel-report.blade.php) ──
    'pf_pipeline_label'            => 'Pipeline',
    'pf_leads_suffix'              => ':count leads',
    'pf_dropoff_suffix'            => '↓ :pct% drop-off',
    'pf_top_of_funnel'             => 'Top of funnel',
    'pf_stage_col'                 => 'Stage',
    'pf_lead_count_col'            => 'Lead Count',
    'pf_drop_off_col'              => 'Drop-off',
    'pf_avg_days_in_stage_col'     => 'Avg Days in Stage',
    'pf_days_suffix'               => ':count days',
    'pf_no_pipeline_data'          => 'No pipeline data available. Create a pipeline and assign leads to stages.',

    // ─── Response Time view (resources/views/filament/pages/reports/response-time-report.blade.php) ──
    'rt_source_label'              => 'Source',
    'rt_source_placeholder'        => 'e.g. facebook, website…',
    'rt_agent_label'               => 'Agent',
    'rt_all_agents'                => 'All agents',
    'rt_total_leads_analysed'      => 'Total Leads Analysed',
    'rt_median_response_time'      => 'Median Response Time',
    'rt_p90_label'                 => '90th Percentile',
    'rt_filters_active'            => 'Filters active:',
    'rt_filter_source_label'       => 'Source:',
    'rt_filter_agent_label'        => 'Agent:',
    'rt_chart_dataset_label'       => 'Leads',
    'rt_time_bucket_col'           => 'Time Bucket',
    'rt_leads_col'                 => 'Leads',
    'rt_percent_of_total_col'      => '% of Total',

    // ─── Source Performance view (resources/views/filament/pages/reports/source-performance-report.blade.php) ──
    'sp_chart_dataset_label'       => 'Leads',
    'sp_section_sub'               => 'Trend column compares to the equivalent prior period',
    'sp_source_col'                => 'Source',
    'sp_total_leads_col'           => 'Total Leads',
    'sp_vs_prev_period_col'        => 'vs Prev Period',
    'sp_converted_col'             => 'Converted',
    'sp_conversion_rate_col'       => 'Conversion %',
    'sp_avg_score_col'             => 'Avg Score',
    'sp_no_data_in_period'         => 'No data for this period',

    // ─── Shared duration / unit suffixes ─────────────────────────────
    // Used by Response Time, Agent Performance, Automation Stats reports
    // so the "15m" / "2.5h" / "30s" / "4d" KPI cells aren't a literal
    // English abbreviation. Tenant locales can ship their own suffix
    // (e.g. "мин" / "ч" / "с" / "д" in Russian).
    'duration_minutes_short'       => ':n m',
    'duration_hours_short'         => ':n h',
    'duration_seconds_short'       => ':n s',
    'duration_days_short'          => ':n d',

    // Histogram bucket labels (Response Time report). Routed through __()
    // so the chart x-axis + breakdown table render in tenant locale.
    'rt_bucket_under_5m'           => '< 5m',
    'rt_bucket_5_15m'              => '5-15m',
    'rt_bucket_15_60m'             => '15-60m',
    'rt_bucket_1_4h'               => '1-4h',
    'rt_bucket_4_24h'              => '4-24h',
    'rt_bucket_over_24h'           => '> 24h',

    // Automation Stats — fallback humanised trigger label when an
    // automation row references a slug not in Automation::triggerLabels().
    'as_trigger_humanised_fallback' => ':label',

    // Integration Sync Logs — fallback humanised event label when an
    // event isn't in integration_sync_logs.event.* lang keys.
    'integration_sync_event_fallback' => ':label',
];
