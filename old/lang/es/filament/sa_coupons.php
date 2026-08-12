<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin CouponResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_coupons.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_coupons.php.
*/

return [

    // ----- Navigation -----
    'nav_label'                   => 'Cupones',

    // ----- Code & description section -----
    'code_helper'                 => 'Los clientes lo escriben en la caja. No distingue mayúsculas — se almacena en mayúsculas.',
    'description_helper'          => 'Nota interna — para qué sirve este código, a qué campaña pertenece, recuento previsto de canjes.',

    // ----- Discount section -----
    'discount_type_helper'        => 'Porcentaje quita un %. Fijo descuenta un importe en una moneda concreta. La extensión de prueba añade días al período de prueba del inquilino — no hay descuento monetario.',
    'discount_value_suffix_days'  => 'días',
    'discount_value_helper_percent' => '0–100. 100 significa «gratis durante la ventana de descuento».',
    'discount_value_helper_fixed' => 'Importe en moneda entera, p. ej. 20 para 20 €.',
    'discount_value_helper_trial' => 'Días añadidos al trial_ends_at del inquilino al canjear.',
    'currency_helper'             => 'Déjelo vacío para aplicar a cualquier moneda.',

    // ----- Limits & targeting section -----
    'max_total_uses'              => 'Máximo de usos totales',
    'max_total_uses_placeholder'  => 'Ilimitado',
    'max_total_uses_helper'       => 'Canjes totales en todos los inquilinos. Déjelo vacío para ilimitado.',
    'max_per_tenant'              => 'Máximo por inquilino',
    'max_per_tenant_helper'       => 'Cuántas veces puede un inquilino canjear este código. 1 = solo la primera vez.',
    'applies_to_plans_placeholder'=> 'Todos los planes',
    'applies_to_plans_helper'     => 'Déjelo vacío para aplicar a todos los planes.',

    // ----- Validity window section -----
    'starts_at_placeholder'       => 'Ahora',
    'starts_at_helper'            => 'Vacío = activo de inmediato.',
    'ends_at_placeholder'         => 'Nunca caduca',
    'ends_at_helper'              => 'Vacío = sin caducidad.',
    'is_active_helper'            => 'Los códigos inactivos nunca se validan, incluso dentro de la ventana de fechas.',

    // ----- Table columns -----
    'column_type'                 => 'Tipo',
    'column_value'                => 'Valor',
    'column_uses'                 => 'Usos',
    'column_status'               => 'Estado',
    'column_ends_at_placeholder'  => 'Nunca',
    'trial_days_suffix'           => 'días de prueba',

    // ----- Filters -----
    'filter_label_discount_type'  => 'Tipo',
    'filter_active'               => 'Activo',
    'filter_active_yes'           => 'Sí',
    'filter_active_no'            => 'No',

    // ----- Field labels (form + table) -----
    'code'                        => 'Código',
    'description'                 => 'Descripción',
    'discount_type'               => 'Tipo de descuento',
    'discount_value'              => 'Valor del descuento',
    'currency'                    => 'Moneda',
    'applies_to_plans'            => 'Se aplica a los planes',
    'starts_at'                   => 'Comienza el',
    'ends_at'                     => 'Termina el',
    'is_active'                   => 'Activo',
    'created_at'                  => 'Creado el',

    // ----- Model labels -----
    'model_label'                 => 'Cupón',
    'plural_model_label'          => 'Cupones',

    // ----- Status badge labels (table column) -----
    'status_active'               => 'Activo',
    'status_scheduled'            => 'Programado',
    'status_expired'              => 'Caducado',
    'status_exhausted'            => 'Agotado',
    'status_inactive'             => 'Inactivo',

];
