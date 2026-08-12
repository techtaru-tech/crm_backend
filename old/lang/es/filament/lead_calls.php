<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadCallResource — cadenas del panel de Filament (es)
|------------------------------------------------------------
| Acceso vía __('filament/lead_calls.<clave>').
*/

return [

    // ----- Navegación -----
    'nav_label'   => 'Historial de llamadas',

    // ----- Etiquetas del modelo (rutas de navegación / títulos de página) -----
    'model_label'        => 'Llamada',
    'plural_model_label' => 'Llamadas',

    // ----- Infolist -----
    'lead'        => 'Cliente potencial',
    'agent'       => 'Agente',
    'from'        => 'De',
    'to'          => 'Para',
    'duration'    => 'Duración',
    'started'     => 'Inicio',
    'recording'   => 'Grabación',
    'ai_summary'  => 'Resumen con IA',
    'transcription' => 'Transcripción',

    // ----- Tabla -----
    'col_when'      => 'Cuándo',
    'col_direction' => 'Dirección',
    'col_status'    => 'Estado',

    // ----- Filtros -----
    'filter_agent'         => 'Agente',
    'filter_label_direction' => 'Dirección',
    'filter_label_status'  => 'Estado',

    // ─── Opciones de selección ────────────────────────────────────────────
    'option_inbound'      => 'Entrante',
    'option_outbound'     => 'Saliente',
    'option_initiated'    => 'Iniciada',
    'option_ringing'      => 'Llamando',
    'option_in_progress'  => 'En curso',
    'option_completed'    => 'Completada',
    'option_busy'         => 'Ocupado',
    'option_failed'       => 'Fallida',
    'option_no_answer'    => 'Sin respuesta',
    'option_canceled'     => 'Cancelada',

    // ─── Cadenas alternativas del infolist ────────────────────────────────
    'fallback_unknown'       => '(desconocido)',
    'fallback_not_available' => '(no disponible)',

    // ─── Etiquetas de dirección/estado (contenido Placeholder del infolist) ──
    'direction_inbound'   => 'Entrante',
    'direction_outbound'  => 'Saliente',
    'status_initiated'    => 'Iniciada',
    'status_ringing'      => 'Llamando',
    'status_in_progress'  => 'En curso',
    'status_completed'    => 'Completada',
    'status_busy'         => 'Ocupado',
    'status_failed'       => 'Fallida',
    'status_no_answer'    => 'Sin respuesta',
    'status_canceled'     => 'Cancelada',

    // ─── Reproductor de grabación (resources/views/filament/resources/lead-calls/recording-player.blade.php) ──
    'recording_unsupported' => 'Su navegador no admite la reproducción de audio.',
    'recording_download'    => 'Descargar MP3',

];
