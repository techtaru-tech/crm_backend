<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin PlanResource — Filament admin strings
|------------------------------------------------------------
*/

return [

    // ----- Navigation -----
    'nav_label'                     => 'Planes',
    'tabs_outer'                    => 'Plan',

    // ----- Tabs -----
    'tab_basics'                    => 'Básicos',
    'tab_limits'                    => 'Límites',
    'tab_features'                  => 'Funciones',
    'tab_gateway_ids'               => 'IDs de pasarela',

    // ----- Basics tab -----
    'plan_key'                      => 'Clave del Plan',
    'plan_key_helper'               => 'Identificador interno. Minúsculas, sin espacios. Se usa en URLs y en el código.',
    'name_helper'                   => 'Se muestra en la página de precios y en el panel de facturación.',

    // ----- Pricing section -----
    'monthly_price'                 => 'Precio mensual',
    'monthly_price_helper'          => 'Importe mensual recurrente.',
    'interval_monthly'              => 'Mensual',
    'interval_yearly'               => 'Anual',
    'interval_weekly'               => 'Semanal',
    'interval_daily'                => 'Diario',
    // Short interval labels (badge column on table — short, capitalized).
    'interval_month'                => 'Mes',
    'interval_year'                 => 'Año',
    'interval_helper'               => 'Con qué frecuencia se renueva el precio tras la prueba.',
    'trial_days'                    => 'Días de prueba',
    'trial_days_suffix'             => 'días',
    'trial_days_helper'             => 'Duración de la prueba gratuita cuando un espacio de trabajo comienza con este Plan. 0 = sin prueba — facturado desde el primer día.',
    'annual_price'                  => 'Precio anual (por adelantado)',
    'annual_price_helper'           => 'Opcional. Importe total por adelantado para 12 meses. Déjelo vacío si no ofrece facturación anual en este Plan.',
    'annual_discount_percent'       => 'Descuento anual %',
    'annual_discount_percent_helper'=> 'Solo visualización: alimenta el distintivo «Ahorra N%» en la página de precios (p. ej. 20 = «Ahorra 20% con facturación anual»).',

    // ----- Visibility section -----
    'active'                        => 'Activo',
    'active_helper'                 => 'Los Planes inactivos se ocultan en todas partes y no se pueden suscribir.',
    'public'                        => 'Público',
    'public_helper'                 => 'Mostrar en la página de precios pública.',
    'highlight'                     => 'Destacar',
    'highlight_helper'              => 'Marca este Plan como el recomendado (distintivo en la página de precios).',
    'sort_order'                    => 'Orden de clasificación',
    'sort_order_helper'             => 'Los números más bajos aparecen primero.',

    // ----- Limits tab -----
    'limits_description'            => 'Use -1 para ilimitado, 0 para deshabilitar una función por completo, o cualquier entero positivo para un límite estricto.',
    'limit_key'                     => 'Clave de límite',
    'limit_value'                   => 'Valor',
    'add_limit'                     => 'Añadir límite',

    // ----- Features tab -----
    'features_description'          => 'Active las funciones que este Plan desbloquea. Los valores deben ser «true» o «false».',
    'feature_key'                   => 'Clave de la función',
    'feature_enabled'               => 'Habilitada',
    'add_feature'                   => 'Añadir función',

    // ----- Gateway IDs tab -----
    'gateway_ids_description'       => 'Asocie este Plan al ID de producto/precio en cada pasarela de pago. Déjelo en blanco si la pasarela está deshabilitada.',
    'stripe_price_id'               => 'ID de precio de Stripe',
    'paddle_price_id'               => 'ID de precio de Paddle',
    'razorpay_plan_id'              => 'ID del Plan de Razorpay',
    'paystack_plan_code'            => 'Código del Plan de Paystack',

    // ----- Table columns -----
    'column_number'                 => '#',
    'column_active'                 => 'Activo',
    'column_public'                 => 'Público',
    'column_highlight'              => 'Destacar',

    // ----- Filters -----
    'filter_active_label'           => 'Activo',
    'filter_active'                 => 'Activo',
    'filter_inactive'               => 'Inactivo',
    'filter_label_interval'         => 'Intervalo',

    // ----- Field labels (form + table) -----
    'name'                          => 'Nombre',
    'description'                   => 'Descripción',
    'currency'                      => 'Moneda',
    'interval'                      => 'Intervalo',
    'limits'                        => 'Límites',
    'features'                      => 'Funciones',
    'price'                         => 'Precio',
    'updated_at'                    => 'Actualizado el',

    // ----- Model labels -----
    'model_label'                   => 'Plan',
    'plural_model_label'            => 'Planes',

];
