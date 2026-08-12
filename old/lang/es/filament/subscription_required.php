<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SubscriptionRequired — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/subscription_required.<key>').
*/

return [
    'title'                    => 'Suscripción requerida',

    // Reasons - headings
    'heading_trial_expired'    => 'Su prueba ha finalizado',
    'heading_cancelled'        => 'Su suscripción está cancelada',
    'heading_expired'          => 'Su suscripción ha caducado',
    'heading_suspended'        => 'Este espacio de trabajo está suspendido',
    'heading_default'          => 'Se requiere una suscripción',

    // Reasons - subheadings
    'subheading_trial_expired' => 'Su prueba de 14 días ha terminado. Elija un plan a continuación para seguir usando LeadHub.',
    'subheading_cancelled'     => 'Su suscripción ha sido cancelada. Reactívela para recuperar el acceso.',
    'subheading_expired'       => 'El pago de su suscripción ha caducado. Renuévela para recuperar el acceso.',
    'subheading_suspended'     => 'Contacte con su administrador.',
    'subheading_default'       => 'Elija un plan para continuar.',

    // Footer / actions
    'sign_out'                 => 'Cerrar sesión',

    // ─── Page body (resources/views/filament/pages/subscription-required.blade.php) ──
    'current_status_prefix'    => 'Estado actual:',
    'most_popular_tag'         => 'Más popular',
    'price_per_interval'       => '/:interval',
    'seats_unlimited'          => 'Plazas de equipo ilimitadas',
    'seats_count'              => ':count plazas de equipo',
    'leads_unlimited'          => 'Clientes potenciales ilimitados',
    'leads_count'              => ':count clientes potenciales',
    'forms_unlimited'          => 'Formularios ilimitados',
    'forms_count'              => ':count formularios',
    'feature_integrations'     => 'Integraciones',
    'feature_api_access'       => 'Acceso a la API',
    'feature_custom_domain'    => 'Dominio personalizado',
    'pay_with_label'           => 'Pagar con :gateway',
    'contact_sales_btn'        => 'Contactar con ventas — :plan',
    'switch_accounts_label'    => '¿Necesita cambiar de cuenta?',
    'sales_mailto_subject'     => 'Actualizar a :plan',
    'interval_month'           => 'mes',
    'interval_year'            => 'año',
    'interval_week'            => 'semana',
    'interval_day'             => 'día',
];
