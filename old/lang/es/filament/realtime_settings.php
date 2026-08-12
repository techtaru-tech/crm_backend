<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| RealtimeSettingsPage — cadenas para inquilinos de Filament (es)
|------------------------------------------------------------
| Acceso vía __('filament/realtime_settings.<clave>').
*/

return [
    'title'                          => 'Tiempo real y emisión',
    'navigation_label'               => 'Tiempo real',

    // Sección de driver
    'driver_section_description'     => 'Elija el proveedor de emisión en tiempo real para actualizaciones de clientes potenciales y notificaciones.',
    'enable_realtime_label'          => 'Activar actualizaciones en tiempo real',
    'enable_realtime_helper'         => 'Cuando está desactivado, el panel usa sondeo en lugar de WebSockets.',
    'driver_label'                   => 'Driver',
    'driver_helper'                  => 'Reverb y Soketi son servidores autoalojados compatibles con el protocolo de Pusher.',
    'driver_option_pusher'           => 'Pusher / Soketi / Reverb (protocolo Pusher)',
    'driver_option_null'             => 'Desactivado (solo sondeo)',

    // Sección de Pusher
    'pusher_section_description'     => 'Proporcione credenciales para su servidor de Pusher, Reverb o Soketi.',
    'pusher_app_id_label'            => 'App ID',
    'pusher_key_label'               => 'App Key',
    'pusher_secret_label'            => 'App Secret',
    'pusher_cluster_label'           => 'Clúster',
    'pusher_cluster_helper'          => 'Déjelo en blanco para Reverb/Soketi autoalojados.',
    'pusher_host_label'              => 'Host personalizado (Reverb/Soketi)',
    'pusher_host_helper'             => 'Sobrescriba para servidores autoalojados. Déjelo en blanco para Pusher gestionado.',
    'pusher_port_label'              => 'Puerto',
    'pusher_scheme_label'            => 'Esquema',
    'pusher_scheme_https'            => 'HTTPS (wss)',
    'pusher_scheme_http'             => 'HTTP (ws)',

    // Sección de estado
    'status_content'                 => 'Guarde la configuración para aplicarla. Las credenciales de emisión se almacenan por inquilino y se aplican en tiempo de ejecución. Reinicie el trabajador de cola tras cambiar de driver.',

    // Acciones de cabecera
    'action_save'                    => 'Guardar configuración',

    // ----- Tarjeta de driver (Blade) -----
    'section_broadcasting_driver'       => 'Driver de emisión',
    'active_driver'                     => 'Driver activo',
    'connection_status'                 => 'Estado de la conexión',
    'status_connected'                  => 'Conectado',
    'status_error'                      => 'Error',
    'status_not_configured'             => 'No configurado',
    'status_not_tested'                 => 'Sin probar',
    'btn_test_connection'               => 'Probar conexión',
    'btn_send_test_notification'        => 'Enviar notificación de prueba',
    'test_sent_message'                 => '¡Notificación de prueba enviada! Revise su campana de notificaciones.',

    // ----- Sección de configuración (Blade) -----
    'section_configuration'             => 'Configuración',
    'config_description'                => 'Establezca las siguientes variables de entorno para activar la emisión en tiempo real:',
    'option_a_pusher'                   => 'Opción A: Pusher (gestionado)',
    'option_b_reverb'                   => 'Opción B: Laravel Reverb (autoalojado)',
];
