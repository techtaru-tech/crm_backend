<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Filament dashboard widgets — shared translation strings
|------------------------------------------------------------
| Accessed via __('filament/widgets.<key>').
|
| Covers chart headings and Chart.js dataset labels for the
| four tenant dashboard chart widgets:
|   - LeadStatusChart
|   - LeadsBySourceChart
|   - LeadsOverTimeChart
|   - PipelineDistributionChart
|
| Date-range filter labels are shared with the report-date-range
| Blade component — see lang/en/components.php (rdr_range_*).
*/

return [

    // ─── Chart headings ───
    'lead_status_chart_heading'           => 'توزيع حالات العملاء المحتملين',
    'leads_by_source_chart_heading'       => 'العملاء المحتملون حسب المصدر',
    'leads_over_time_chart_heading'       => 'العملاء المحتملون عبر الزمن',
    'pipeline_distribution_chart_heading' => 'توزيع مراحل خط الأنابيب',

    // ─── Chart.js dataset labels ───
    'dataset_leads' => 'العملاء المحتملون',
];
