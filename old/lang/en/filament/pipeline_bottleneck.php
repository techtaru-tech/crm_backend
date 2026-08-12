<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| PipelineBottleneckReport — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/pipeline_bottleneck.<key>').
*/

return [

    // ----- Navigation / page header -----
    'nav_label' => 'Pipeline Bottlenecks',
    'title'     => 'Pipeline Bottlenecks',

    // ----- Section headings -----
    'stages_at_risk'    => 'Stages at Risk',
    'section_subtitle'  => "Leads spending more than the stage's expected duration. Critical = 1.5x expected.",

    // ----- Table / cells -----
    'empty'             => 'No active pipeline stages with leads.',
    'col_pipeline'      => 'Pipeline',
    'col_stage'         => 'Stage',
    'col_leads'         => 'Leads',
    'col_avg_days'      => 'Avg Days',
    'col_expected'      => 'Expected',
    'col_overdue'       => 'Overdue',
    'col_status'        => 'Status',
    'days_short'        => ':count d',

    // ----- Status badge labels -----
    'status_healthy'  => 'Healthy',
    'status_warning'  => 'Warning',
    'status_critical' => 'Critical',
];
