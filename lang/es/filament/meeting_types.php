<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| MeetingTypeResource — cadenas del panel de Filament (es)
|------------------------------------------------------------
| Acceso vía __('filament/meeting_types.<clave>').
*/

return [

    // ----- Navegación -----
    'nav_label'                   => 'Tipos de reunión',

    // ----- Etiquetas del modelo (rutas de navegación / títulos de página) -----
    'model_label'                 => 'Tipo de reunión',
    'plural_model_label'          => 'Tipos de reunión',

    // ----- Campos del formulario -----
    'name'                        => 'Nombre',
    'slug'                        => 'Slug',
    'description'                 => 'Descripción',
    'color'                       => 'Color',
    'is_active'                   => 'Activo',

    // ----- Notificaciones de acción -----
    'booking_link_copied'         => 'Enlace de reserva copiado',

    // ----- Campos del tipo de reunión -----
    'slug_help'                   => 'Parte de la URL pública de reserva. No puede cambiarse después de su creación.',
    'duration'                    => 'Duración',
    'buffer_after_meeting'        => 'Margen tras la reunión (minutos)',
    'advance_notice_hours'        => 'Antelación mínima (horas)',
    'future_days_max'             => 'Hasta cuántos días en el futuro',

    // ----- Ubicación -----
    'location_type'               => 'Tipo de ubicación',
    'location_details'            => 'Detalles de la ubicación',
    'location_details_help'       => 'Dirección, número de llamada o cualquier instrucción específica.',

    // ----- Opciones del Select de tipo de ubicación -----
    // El placeholder :brand se interpola en el sitio de llamada con la
    // cadena literal de la marca (p. ej. «Google Meet», «Zoom») para que
    // el nombre de marca no se traduzca accidentalmente en un Locale posterior.
    'location_option_custom'      => 'Personalizado (detalles abajo)',
    'location_option_google_meet' => 'Enlace de :brand (se facilita después)',
    'location_option_zoom'        => 'Enlace de :brand (se facilita después)',
    'location_option_phone'       => 'Llamada telefónica',
    'location_option_in_person'   => 'En persona',

    // ----- Enrutamiento de clientes potenciales -----
    'pipeline'                    => 'Embudo',
    'stage'                       => 'Etapa',

    // ----- Compartir -----
    'public_booking_link'         => 'Enlace público de reserva',

    // ----- Tabla -----
    'col_duration'                => 'Duración',
    // :n es la duración numérica en minutos (p. ej. «:n min» → «30 min»).
    'minutes_short'               => ':n min',
    'col_bookings'                => 'Reservas',
    'col_active'                  => 'Activo',
    'col_owner'                   => 'Propietario',
    'col_link'                    => 'Enlace',

    // ----- Acciones -----
    'share'                       => 'Compartir',
    'share_modal_heading'         => 'Compartir «:name»',
    'share_modal_close'           => 'Cerrar',

    // ----- Enlace de reserva / panel de fragmento embebido (contenido Placeholder del formulario) -----
    'save_first_for_link'         => 'Guarde primero para obtener un enlace público.',
    'embed_snippet_label'         => 'Fragmento para insertar:',

    // ----- Etiquetas del modal de compartir -----
    'public_url_label'            => 'URL pública',
    'embed_snippet_heading'       => 'Fragmento para insertar',

];
