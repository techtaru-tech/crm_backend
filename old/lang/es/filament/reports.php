<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Tenant Reports — shared Filament strings (es)
|------------------------------------------------------------
| Accessed via __('filament/reports.<key>').
*/

return [
    // Report titles + navigation labels
    'agent_performance_title'      => 'Informe de rendimiento del agente',
    'agent_performance_nav'        => 'Rendimiento del agente',
    'automation_stats_title'       => 'Estadísticas de automatización',
    'automation_stats_nav'         => 'Estadísticas de automatización',
    'form_analytics_title'         => 'Análisis de formularios',
    'form_analytics_nav'           => 'Análisis de formularios',
    'lead_volume_title'            => 'Informe de volumen de clientes potenciales',
    'lead_volume_nav'              => 'Volumen de clientes potenciales',
    'pipeline_funnel_title'        => 'Embudo de conversión',
    'pipeline_funnel_nav'          => 'Embudo de conversión',
    'response_time_title'          => 'Informe de tiempo de respuesta',
    'response_time_nav'            => 'Tiempo de respuesta',
    'source_performance_title'     => 'Informe de rendimiento por origen',
    'source_performance_nav'       => 'Rendimiento por origen',

    // Common export actions
    'export_csv'                   => 'Exportar CSV',
    'export_pdf'                   => 'Exportar PDF',

    // Section / chart headings inside individual report views
    'agent_performance_section'    => 'Rendimiento del agente',
    'automation_performance'       => 'Rendimiento de la automatización',
    'sequence_performance'         => 'Rendimiento de la secuencia',
    'submission_trend'             => 'Tendencia de envíos',
    'top_forms'                    => 'Formularios principales',
    'breakdown'                    => 'Desglose',
    'pipeline_funnel_section'      => 'Embudo de conversión',
    'stage_details'                => 'Detalles de la etapa',
    'response_time_distribution'   => 'Distribución del tiempo de respuesta',
    'distribution_breakdown'       => 'Desglose de la distribución',
    'lead_volume_by_source'        => 'Volumen de clientes potenciales por origen',
    'source_breakdown'             => 'Desglose por origen',

    // ─── Form Analytics view ──
    'total_submissions_label'      => 'Total de envíos: :count',
    'fa_form_name_col'             => 'Nombre del formulario',
    'fa_status_col'                => 'Estado',
    'fa_submissions_col'           => 'Envíos',
    'fa_chart_dataset_label'       => 'Envíos',
    'fa_status_active'             => 'Activo',
    'fa_status_inactive'           => 'Inactivo',
    'fa_no_submissions_in_period'  => 'No hay envíos de formulario en este período',

    // ─── Agent Performance view ──
    'ap_section_sub'               => 'El minigráfico muestra los clientes potenciales asignados semanalmente (últimas 4 semanas)',
    'ap_agent_col'                 => 'Agente',
    'ap_assigned_col'              => 'Asignados',
    'ap_won_col'                   => 'Ganados',
    'ap_win_rate_col'              => 'Tasa de éxito',
    'ap_avg_response_col'          => 'Respuesta media',
    'ap_avg_close_col'             => 'Cierre medio',
    'ap_activities_col'            => 'Actividades',
    'ap_trend_col'                 => 'Tendencia',
    'ap_no_agents_in_period'       => 'No se encontraron agentes para este período',

    // ─── Automation Stats view ──
    'as_automation_col'            => 'Automatización',
    'as_trigger_col'               => 'Disparador',
    'as_status_col'                => 'Estado',
    'as_total_runs_col'            => 'Ejecuciones totales',
    'as_success_runs_col'          => 'Ejecuciones exitosas',
    'as_success_rate_col'          => 'Tasa de éxito',
    'as_avg_run_time_col'          => 'Tiempo medio de ejecución',
    'as_badge_active'              => 'Activo',
    'as_badge_disabled'            => 'Desactivado',
    'as_no_runs_in_period'         => 'No hay ejecuciones de automatización en este período',

    // ─── Email Sequences view ──
    'es_no_sequences'              => 'Aún no hay secuencias.',
    'es_sequence_col'              => 'Secuencia',
    'es_status_col'                => 'Estado',
    'es_enrolled_col'              => 'Inscritos',
    'es_completed_col'             => 'Completados',
    'es_replied_col'               => 'Respondidos',
    'es_reply_rate_col'            => 'Tasa de respuesta',
    'es_open_rate_col'             => 'Tasa de apertura',
    'es_status_active'             => 'Activa',
    'es_status_paused'             => 'En pausa',
    'es_status_draft'              => 'Borrador',
    'es_status_archived'           => 'Archivada',

    // ─── Lead Volume view ──
    'lv_source_label'              => 'Origen',
    'lv_source_placeholder'        => 'p. ej. facebook, api…',
    'lv_group_by_label'            => 'Agrupar por',
    'lv_group_by_day'              => 'Día',
    'lv_group_by_week'             => 'Semana',
    'lv_group_by_month'            => 'Mes',
    'lv_total_pill'                => 'Total: :count clientes potenciales',
    'lv_leads_over_time'           => 'Clientes potenciales a lo largo del tiempo',
    'lv_source_separator'          => '· Origen: :source',
    'lv_chart_dataset_label'       => 'Clientes potenciales',
    'lv_period_col'                => 'Período',
    'lv_leads_col'                 => 'Clientes potenciales',
    'lv_no_data_in_period'         => 'No hay datos para este período',

    // ─── Pipeline Funnel view ──
    'pf_pipeline_label'            => 'Embudo',
    'pf_leads_suffix'              => ':count clientes potenciales',
    'pf_dropoff_suffix'            => '↓ :pct% de abandono',
    'pf_top_of_funnel'             => 'Inicio del embudo',
    'pf_stage_col'                 => 'Etapa',
    'pf_lead_count_col'            => 'Recuento',
    'pf_drop_off_col'              => 'Abandono',
    'pf_avg_days_in_stage_col'     => 'Días medios en la etapa',
    'pf_days_suffix'               => ':count días',
    'pf_no_pipeline_data'          => 'No hay datos de embudo disponibles. Cree un embudo y asigne clientes potenciales a las etapas.',

    // ─── Response Time view ──
    'rt_source_label'              => 'Origen',
    'rt_source_placeholder'        => 'p. ej. facebook, sitio web…',
    'rt_agent_label'               => 'Agente',
    'rt_all_agents'                => 'Todos los agentes',
    'rt_total_leads_analysed'      => 'Total de clientes potenciales analizados',
    'rt_median_response_time'      => 'Tiempo de respuesta mediano',
    'rt_p90_label'                 => 'Percentil 90',
    'rt_filters_active'            => 'Filtros activos:',
    'rt_filter_source_label'       => 'Origen:',
    'rt_filter_agent_label'        => 'Agente:',
    'rt_chart_dataset_label'       => 'Clientes potenciales',
    'rt_time_bucket_col'           => 'Intervalo de tiempo',
    'rt_leads_col'                 => 'Clientes potenciales',
    'rt_percent_of_total_col'      => '% del total',

    // ─── Source Performance view ──
    'sp_chart_dataset_label'       => 'Clientes potenciales',
    'sp_section_sub'               => 'La columna Tendencia compara con el período anterior equivalente',
    'sp_source_col'                => 'Origen',
    'sp_total_leads_col'           => 'Total de clientes potenciales',
    'sp_vs_prev_period_col'        => 'vs período anterior',
    'sp_converted_col'             => 'Convertidos',
    'sp_conversion_rate_col'       => 'Tasa de conversión %',
    'sp_avg_score_col'             => 'Puntuación media',
    'sp_no_data_in_period'         => 'No hay datos para este período',

    // ─── Shared duration / unit suffixes ─────────────────────────────
    'duration_minutes_short'       => ':n min',
    'duration_hours_short'         => ':n h',
    'duration_seconds_short'       => ':n s',
    'duration_days_short'          => ':n d',

    // Histogram bucket labels (Response Time report).
    'rt_bucket_under_5m'           => '< 5 min',
    'rt_bucket_5_15m'              => '5-15 min',
    'rt_bucket_15_60m'             => '15-60 min',
    'rt_bucket_1_4h'               => '1-4 h',
    'rt_bucket_4_24h'              => '4-24 h',
    'rt_bucket_over_24h'           => '> 24 h',

    // Automation Stats — fallback humanised trigger label.
    'as_trigger_humanised_fallback' => ':label',

    // Integration Sync Logs — fallback humanised event label.
    'integration_sync_event_fallback' => ':label',
];
