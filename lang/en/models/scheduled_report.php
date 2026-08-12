<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| ScheduledReport model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/scheduled_report.<key>').
*/

return [
    // ─── REPORT TYPES ──────────────────────────────
    'report_type_dashboard_stats'       => 'Dashboard Summary',
    'report_type_leads_by_source'       => 'Leads by Source',
    'report_type_pipeline_distribution' => 'Pipeline Distribution',
    'report_type_agent_performance'     => 'Agent Performance',
    'report_type_source_performance'    => 'Source Performance',
    'report_type_automation_stats'      => 'Automation Stats',
    'report_type_form_analytics'        => 'Form Analytics',

    // ─── FREQUENCIES ───────────────────────────────
    'frequency_daily'   => 'Daily (every day at 7 AM)',
    'frequency_weekly'  => 'Weekly (every Monday at 7 AM)',
    'frequency_monthly' => 'Monthly (1st of each month at 7 AM)',
];
