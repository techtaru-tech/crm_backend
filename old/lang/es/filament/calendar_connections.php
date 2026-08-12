<?php

declare(strict_types=1);

return [
    'title'                  => 'Sincronización de calendario',
    'heading'                => 'Sincronización de calendario',
    'subheading'             => 'Conecte su calendario de Google o Outlook para que las reuniones reservadas en el CRM aparezcan allí automáticamente — y los eventos externos fluyan de vuelta a su línea de tiempo de reservas.',
    'navigation_label'       => 'Calendario',

    // Etiquetas del proveedor
    'provider_google'        => 'Google Calendar',
    'provider_outlook'       => 'Outlook Calendar',

    // ─── Tarjetas de conexión ──────────────────────────────────────
    'reconnect'              => 'Volver a conectar',
    'disconnect'             => 'Desconectar',
    'confirm_disconnect'     => '¿Desconectar :provider? Los eventos sincronizados anteriormente permanecen en el CRM; los nuevos eventos dejarán de aparecer.',
    'not_connected_note'     => 'No conectado. Las reservas creadas aquí no aparecerán en su :provider hasta que vincule su cuenta.',

    // ─── Indicador de estado + pie ─────────────────────────────────
    'last_synced_prefix'     => 'Última sincronización',
    'last_synced'            => 'Última sincronización :when',
    'connect_prefix'         => 'Conectar',
    'coming_soon'            => 'Próximamente',
    'footnote'               => 'La sincronización se ejecuta cada 15 minutos. Los envíos salientes ocurren al instante al crear una reserva. Nunca leemos su calendario fuera del alcance events.*.',
];
