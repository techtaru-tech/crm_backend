<?php

declare(strict_types=1);

return [

    // ─── Navegación ───────────────────────────────────────────────────
    'nav_label'                         => 'Registros de Webhook',

    // ─── Etiquetas de modelo ──────────────────────────────────────────
    'model_label'                       => 'Registro de Webhook',
    'plural_model_label'                => 'Registros de Webhook',

    // ─── Columnas de tabla ────────────────────────────────────────────
    'col_source'                        => 'Origen',
    'col_status'                        => 'Estado',

    // ─── Formulario ────────────────────────────────────────────────────
    'raw_payload'                       => 'Carga útil sin procesar',
    'error_message'                     => 'Mensaje de error',

    // ─── Columnas de tabla ─────────────────────────────────────────────
    'leads'                             => 'Leads',
    'ip'                                => 'IP',
    'error'                             => 'Error',
    'received_at'                       => 'Recibido el',
    'processed_at'                      => 'Procesado el',

    // ─── Etiquetas de filtro ───────────────────────────────────────────
    'filter_label_source'               => 'Origen',
    'filter_label_status'               => 'Estado',

    // ─── Filtros de estado ─────────────────────────────────────────────
    'status_success'                    => 'Éxito',
    'status_failed'                     => 'Fallido',
    'status_pending'                    => 'Pendiente',
    'status_invalid_signature'          => 'Firma inválida',

    // ─── Acciones ──────────────────────────────────────────────────────
    'action_retry'                      => 'Reintentar',
];
