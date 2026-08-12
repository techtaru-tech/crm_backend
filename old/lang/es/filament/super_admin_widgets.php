<?php

declare(strict_types=1);

return [

    // ─── TenantSignupsChart ─────────────────────────────────────────────
    'tenant_signups' => [
        'heading'        => 'Nuevos registros de inquilinos (últimos 12 meses)',
        'dataset_label'  => 'Registros',
    ],

    // ─── MrrTrendChart ──────────────────────────────────────────────────
    'mrr_trend' => [
        'heading'        => 'Ingresos recaudados (últimos 12 meses)',
        'dataset_label'  => 'Ingresos',
    ],

    // ─── LeadSourceMixChart ─────────────────────────────────────────────
    'lead_source_mix' => [
        'heading'        => 'Las 5 mejores fuentes de leads (todos los inquilinos)',
        'dataset_label'  => 'Leads',
    ],

    // ─── SubscriptionStatusChart ────────────────────────────────────────
    'subscription_status' => [
        'heading'                => 'Distribución de estado de suscripciones',
        'status_active'          => 'Activa',
        'status_trial'           => 'Prueba',
        'status_trial_expired'   => 'Prueba expirada',
        'status_cancelled'       => 'Cancelada',
        'status_expired'         => 'Expirada',
        'status_suspended'       => 'Suspendida',
        'status_past_due'        => 'Vencida',
    ],

    // ─── SuperAdminSetupChecklist ───────────────────────────────────────
    'setup_checklist' => [
        'item_smtp'       => 'Configure SMTP para que los correos se envíen realmente',
        'item_gateway'    => 'Habilite al menos una pasarela de pago',
        'item_brand'      => 'Establezca el nombre y los colores de su marca',
        'item_landing'    => 'Edite el texto principal de la página de destino',
        'item_workspace'  => 'Incorpore su primer espacio de trabajo',
    ],

];
