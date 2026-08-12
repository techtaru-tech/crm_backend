<?php

declare(strict_types=1);

return [
    'titles' => [
        'lead_volume'        => 'Lead Volume Report',
        'source_performance' => 'Source Performance Report',
        'pipeline_funnel'    => 'Pipeline Funnel Report',
        'agent_performance'  => 'Agent Performance Report',
        'automation_stats'   => 'Automation Stats Report',
        'form_analytics'     => 'Form Analytics Report',
        'response_time'      => 'Response Time Report',
    ],
    'filter_keys' => [
        'date_range'  => 'Date Range',
        'source'      => 'Source',
        'agent_id'    => 'Agent ID',
        'group_by'    => 'Group By',
        'pipeline_id' => 'Pipeline ID',
    ],
    'group_by_values' => [
        'day'   => 'Day',
        'week'  => 'Week',
        'month' => 'Month',
    ],
    'chart_axes' => [
        'leads'          => 'Leads',
        'total_runs'     => 'Total Runs',
        'success_runs'   => 'Success Runs',
        'submissions'    => 'Submissions',
        'assigned_leads' => 'Assigned Leads',
    ],
    'columns' => [
        // lead-volume
        'period'              => 'Period',
        'lead_count'          => 'Lead Count',
        // source-performance
        'source'              => 'Source',
        'total_leads'         => 'Total Leads',
        'vs_prev_period_pct'  => 'vs Prev Period %',
        'converted'           => 'Converted',
        'conversion_rate_pct' => 'Conversion Rate %',
        'avg_score'           => 'Avg Score',
        // pipeline-funnel
        'stage'               => 'Stage',
        'drop_off_pct'        => 'Drop-off %',
        'avg_days_in_stage'   => 'Avg Days in Stage',
        // agent-performance
        'agent'               => 'Agent',
        'assigned_leads'      => 'Assigned Leads',
        'won'                 => 'Won',
        'win_rate_pct'        => 'Win Rate %',
        'avg_response_min'    => 'Avg Response (min)',
        'avg_close_days'      => 'Avg Close (days)',
        'activities'          => 'Activities',
        // automation-stats
        'automation'          => 'Automation',
        'trigger'             => 'Trigger',
        'total_runs'          => 'Total Runs',
        'success_runs'        => 'Success Runs',
        'success_rate_pct'    => 'Success Rate %',
        'avg_run_time_s'      => 'Avg Run Time (s)',
        'enabled'             => 'Enabled',
        // form-analytics
        'form_name'           => 'Form Name',
        'submissions'         => 'Submissions',
        'active'              => 'Active',
        // response-time
        'bucket'              => 'Bucket',
        'pct_of_total'        => '% of Total',
    ],
    'cells' => [
        'yes' => 'Yes',
        'no'  => 'No',
    ],
    'summary' => [
        'median_response' => 'Median Response',
        'p90_response'    => 'P90 Response',
        'total_analysed'  => 'Total Analysed',
        'minutes_suffix'  => ' min',
    ],
];
