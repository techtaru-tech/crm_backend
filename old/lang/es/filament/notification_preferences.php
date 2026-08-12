<?php

declare(strict_types=1);

return [
    'nav_label'   => 'Notificaciones',
    'notif_saved' => 'Preferencias de notificación guardadas.',

    // ----- Section headings -----
    'section_preferences'                => 'Preferencias de notificación',
    'section_browser_push'               => 'Notificaciones push del navegador',

    // ----- Page lede / helper text -----
    'lede'                               => 'Elija cómo recibe cada tipo de notificación. La frecuencia por correo se aplica solo al canal de correo electrónico.',

    // ----- Table headers -----
    'th_notification_type'               => 'Tipo de notificación',
    'th_in_app'                          => 'En la aplicación',
    'th_email'                           => 'Correo',
    'th_email_frequency'                 => 'Frecuencia de correo',
    'th_browser_push'                    => 'Push del navegador',

    // ----- Email frequency options -----
    'freq_immediate'                     => 'Inmediata',
    'freq_hourly'                        => 'Resumen cada hora',
    'freq_off'                           => 'Desactivada',

    // ----- Save button -----
    'save_preferences'                   => 'Guardar preferencias',

    // ----- Push subscription (shared) -----
    'push_lede'                          => 'Permita que :app envíe notificaciones push incluso cuando la pestaña esté en segundo plano.',
    'push_lede_legacy'                   => 'Permita que :app envíe notificaciones del navegador incluso cuando la pestaña esté en segundo plano.',
    'push_unsupported'                   => 'Las notificaciones push del navegador no son compatibles con este navegador.',
    'push_subscribing'                   => 'Suscribiendo...',
    'push_enable_btn'                    => 'Habilitar notificaciones push',
    'push_enabled'                       => 'Notificaciones push habilitadas',

    // ----- OneSignal status messages -----
    'msg_onesignal_not_loaded'           => 'OneSignal aún no cargado. Inténtelo de nuevo.',
    'msg_push_enabled'                   => '¡Notificaciones push habilitadas!',
    'msg_permission_denied'              => 'Permiso denegado por el navegador.',
    'msg_error_prefix'                   => 'Error: ',
    'msg_subscribe_failed'               => 'No se pudo suscribir.',
];
