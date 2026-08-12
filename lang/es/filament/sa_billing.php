<?php

declare(strict_types=1);

return [

    // ----- Navigation / title -----
    'nav_label' => 'Facturación',
    'title'     => 'Facturación e ingresos',

    // ─── Hero KPI cards ───
    'kpi_mrr_label'             => 'Ingresos recurrentes mensuales',
    'kpi_arr_label'             => 'Tasa anual proyectada',
    'kpi_arr_sub'               => 'MRR × 12',
    'kpi_arpu_label'            => 'Ingreso medio por cliente',
    'kpi_arpu_sub'              => 'ARPU · mensual · :currency',
    'kpi_churn_label'           => 'Tasa de cancelación (30 d)',
    'kpi_churn_sub'             => 'cancelados o caducados / total',
    'kpi_mrr_sub_singular'      => 'de :count inquilino de pago',
    'kpi_mrr_sub_plural'        => 'de :count inquilinos de pago',

    // ─── Status breakdown ───
    'section_status_breakdown'  => 'Desglose del estado de suscripción',
    'status_active'             => 'Activa',
    'status_trial'              => 'Prueba',
    'status_trial_expired'      => 'Prueba caducada',
    'status_cancelled'          => 'Cancelada',
    'status_expired'            => 'Caducada',

    // ─── Revenue by plan ───
    'section_revenue_by_plan'   => 'Ingresos por plan',
    'col_plan'                  => 'Plan',
    'col_price'                 => 'Precio',
    'col_customers'             => 'Clientes',
    'col_mrr'                   => 'MRR',
    'free_badge'                => 'gratis',

    // ─── Tenant growth ───
    'section_tenant_growth'     => 'Crecimiento de inquilinos (6 meses)',
    'tenant_growth_title'       => ':total totales · :new nuevos',

    // ─── Recent activity ───
    'section_recent_activity'   => 'Actividad reciente de suscripciones',
    'empty_recent_events'       => 'Sin eventos recientes.',
    'col_tenant'                => 'Inquilino',
    'col_status'                => 'Estado',
    'col_updated'               => 'Actualizado',

];
