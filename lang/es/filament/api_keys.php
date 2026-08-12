<?php

declare(strict_types=1);

return [

    // ----- Navigation -----
    'nav_label'         => 'Claves de API',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'        => 'Clave de API',
    'plural_model_label' => 'Claves de API',

    // ----- Table column labels -----
    'col_name'           => 'Nombre',
    'suffix_per_hour'    => '/h',

    // ----- Form fields -----
    'key_name'          => 'Nombre de la clave',
    'key_name_placeholder' => 'p. ej. Integración con Zapier',
    'key_name_help'     => 'Una etiqueta descriptiva para identificar esta clave.',
    'permissions'       => 'Permisos',
    'permissions_help'  => 'Deje vacío para otorgar todos los permisos.',
    'rate_limit'        => 'Límite de tasa (sol./hora)',
    'expires_at'        => 'Caduca el',
    'expires_at_help'   => 'Deje vacío para que no caduque.',

    // ----- Table -----
    'col_key_prefix'    => 'Prefijo de la clave',
    'col_rate_limit'    => 'Límite de tasa',
    'col_last_used'     => 'Último uso',
    'col_expires'       => 'Caduca',
    'col_active'        => 'Activa',
    'col_created'       => 'Creada',
    'never'             => 'Nunca',

    // ----- Actions -----
    'revoke'            => 'Revocar',
    'new_api_key'       => 'Nueva clave de API',

    // ----- Empty state -----
    'empty_heading'     => 'Aún no hay claves de API',
    'empty_description' => 'Cree una clave de API para integrarse con servicios externos mediante la API REST.',

    // ----- Blade view: new-key banner -----
    'banner_title'        => 'Clave de API creada correctamente',
    'banner_msg_lede'     => 'Copie su nueva clave de API a continuación.',
    'banner_msg_only_once' => 'Esta es la única vez que se mostrará.',
    'banner_msg_store_safe' => 'Guárdela en un lugar seguro — no podrá verla de nuevo.',
    'banner_copy_button'  => 'Copiar clave',
    'banner_copied_label' => '¡Copiada!',

];
