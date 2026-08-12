<?php

declare(strict_types=1);

return [
    'titles' => [
        'lead_volume'        => 'Informe de volumen de clientes potenciales',
        'source_performance' => 'Informe de rendimiento por origen',
        'pipeline_funnel'    => 'Informe de embudo de ventas',
        'agent_performance'  => 'Informe de rendimiento de agentes',
        'automation_stats'   => 'Informe de estadísticas de automatización',
        'form_analytics'     => 'Informe de analítica de formularios',
        'response_time'      => 'Informe de tiempo de respuesta',
    ],
    'filter_keys' => [
        'date_range'  => 'Intervalo de fechas',
        'source'      => 'Origen',
        'agent_id'    => 'ID de agente',
        'group_by'    => 'Agrupar por',
        'pipeline_id' => 'ID de embudo',
    ],
    'group_by_values' => [
        'day'   => 'Día',
        'week'  => 'Semana',
        'month' => 'Mes',
    ],
    'chart_axes' => [
        'leads'          => 'Clientes potenciales',
        'total_runs'     => 'Ejecuciones totales',
        'success_runs'   => 'Ejecuciones con éxito',
        'submissions'    => 'Envíos',
        'assigned_leads' => 'Clientes potenciales asignados',
    ],
    'columns' => [
        // lead-volume
        'period'              => 'Período',
        'lead_count'          => 'Recuento de clientes potenciales',
        // source-performance
        'source'              => 'Origen',
        'total_leads'         => 'Total de clientes potenciales',
        'vs_prev_period_pct'  => '% vs. período anterior',
        'converted'           => 'Convertidos',
        'conversion_rate_pct' => '% de tasa de conversión',
        'avg_score'           => 'Puntuación media',
        // pipeline-funnel
        'stage'               => 'Etapa',
        'drop_off_pct'        => '% de abandono',
        'avg_days_in_stage'   => 'Días medios en etapa',
        // agent-performance
        'agent'               => 'Agente',
        'assigned_leads'      => 'Clientes potenciales asignados',
        'won'                 => 'Ganados',
        'win_rate_pct'        => '% de tasa de éxito',
        'avg_response_min'    => 'Respuesta media (min)',
        'avg_close_days'      => 'Cierre medio (días)',
        'activities'          => 'Actividades',
        // automation-stats
        'automation'          => 'Automatización',
        'trigger'             => 'Disparador',
        'total_runs'          => 'Ejecuciones totales',
        'success_runs'        => 'Ejecuciones con éxito',
        'success_rate_pct'    => '% de tasa de éxito',
        'avg_run_time_s'      => 'Tiempo medio de ejecución (s)',
        'enabled'             => 'Habilitada',
        // form-analytics
        'form_name'           => 'Nombre del formulario',
        'submissions'         => 'Envíos',
        'active'              => 'Activo',
        // response-time
        'bucket'              => 'Tramo',
        'pct_of_total'        => '% del total',
    ],
    'cells' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],
    'summary' => [
        'median_response' => 'Respuesta mediana',
        'p90_response'    => 'Respuesta P90',
        'total_analysed'  => 'Total analizado',
        'minutes_suffix'  => ' min',
    ],
];
