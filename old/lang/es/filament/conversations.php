<?php

declare(strict_types=1);

return [

    // ─── Navigation / title ──────────────────────────────────────────
    'nav_label'                      => 'Conversaciones',
    'title'                          => 'Conversaciones',

    // ─── Send-message validation ──────────────────────────────────────
    'notif_type_message_first'       => 'Escriba primero un mensaje.',
    'notif_select_conversation'      => 'Seleccione primero una conversación.',
    'notif_lead_not_found'           => 'Lead no encontrado.',

    // ─── Channel not enabled ──────────────────────────────────────────
    'notif_channel_not_enabled'      => ':channel no está habilitado para este espacio de trabajo.',
    'notif_channel_not_enabled_body' => 'Vaya a Configuración → Proveedores de mensajería para habilitarlo.',

    // ─── Success ──────────────────────────────────────────────────────
    'notif_message_queued'           => 'Mensaje en cola.',

    // ─── Blade view — left panel ──────────────────────────────────────
    'panel_conversations'            => 'Conversaciones',
    'empty_no_conversations_p1'      => 'Aún no hay conversaciones.',
    'empty_no_conversations_p2'      => 'Inicie una enviando un mensaje desde la página de un lead.',
    'out_marker'                     => 'Usted: ',

    // ─── Blade view — right panel ─────────────────────────────────────
    'lead_prefix'                    => 'Lead n.º ',
    'open_lead'                      => 'Abrir lead →',
    'media_label'                    => '[media]',
    'compose_placeholder'            => 'Escriba un mensaje…',
    'compose_via'                    => 'vía :channel',
    'compose_send'                   => 'Enviar',
    'compose_sending'                => 'Enviando…',
    'empty_thread'                   => 'Aún no hay mensajes en esta conversación.',
    'empty_select_msg'               => 'Seleccione una conversación a la izquierda para ver los mensajes.',
    'empty_select_sub'               => 'O envíe un mensaje desde el perfil de cualquier lead para iniciar un nuevo hilo.',
    'warning_no_channel'             => 'No hay ningún canal de mensajería disponible para este lead. Habilite WhatsApp / SMS / Telegram en',
    'warning_no_channel_link'        => 'Configuración → Proveedores de mensajería',
    'warning_no_channel_suffix'      => 'y asegúrese de que el lead tenga un número de teléfono.',
];
