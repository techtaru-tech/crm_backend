<?php

return [
    'confirm_booking' => 'Confirmar reserva',
    'error_generic'   => 'Algo salió mal. Inténtelo de nuevo.',
    'error_network'   => 'Error de red. Inténtelo de nuevo.',

    // Location-type labels for match() in show.blade.php
    'location_google_meet'  => 'Google Meet',
    'location_zoom'         => 'Zoom',
    'location_phone'        => 'Llamada telefónica',
    'location_in_person'    => 'En persona',
    'location_details_below'=> 'Detalles a continuación',
    'location_label'        => 'Ubicación:',

    // Show page headings + form labels
    'duration_minutes'             => ':minutes minutos',
    'select_date_and_time'         => 'Seleccione una fecha y hora',
    'aria_previous_month'          => 'Mes anterior',
    'aria_next_month'              => 'Mes siguiente',
    'dow_mon'                      => 'Lun',
    'dow_tue'                      => 'Mar',
    'dow_wed'                      => 'Mié',
    'dow_thu'                      => 'Jue',
    'dow_fri'                      => 'Vie',
    'dow_sat'                      => 'Sáb',
    'dow_sun'                      => 'Dom',
    'confirm_your_booking_heading' => 'Confirme su reserva',
    'your_name_label'              => 'Su nombre',
    'email_label'                  => 'Correo electrónico',
    'phone_optional_label'         => 'Teléfono (opcional)',
    'notes_label'                  => '¿Hay algo que le gustaría compartir?',
    'back'                         => 'Atrás',

    // ─── Confirmation page ────────────────────────────────────
    'cancel'                       => 'Cancelar',
    'confirm_cancel_meeting'       => '¿Cancelar esta reunión?',

    // ─── Reschedule page ──────────────────────────────────────
    'cancel_existing_booking'      => 'Cancelar reserva existente',
    'confirm_cancel_existing'      => '¿Cancelar la reserva existente?',

    // Reschedule view body
    'reschedule_title_prefix'      => 'Reprogramar — :name',
    'reschedule_heading'           => 'Reprogramar «:name»',
    'reschedule_currently_for'     => 'Actualmente programado para',
    'reschedule_note'              => 'Para reprogramar, cancele su reserva actual y luego elija una nueva hora desde la página de reservas. (Así es más sencillo tanto para usted como para el anfitrión.)',
    'reschedule_pick_new_time'     => 'Elegir una nueva hora',
    'reschedule_back_to_booking'   => 'Volver a la reserva',
    'reschedule_reason_value'      => 'Reprogramación',

    // ─── JS runtime strings (show.js — read via data-* attributes) ─
    'loading_times'                => 'Cargando horarios…',
    'no_available_times'           => 'No hay horarios disponibles este día. Pruebe otra fecha.',
    'could_not_load_times'         => 'No se pudieron cargar los horarios. Inténtelo de nuevo.',
    'at_time_separator'            => ' a las ',
    'booking_in_progress'          => 'Reservando…',

    // ─── Confirmation page ────────────────────────────────────
    'confirmation_title_confirmed' => 'Reserva confirmada — :name',
    'confirmation_title_cancelled' => 'Reserva cancelada — :name',
    'confirmation_aria_confirmed'  => 'Reserva confirmada',
    'confirmation_aria_cancelled'  => 'Reserva cancelada',
    'confirmation_heading_confirmed' => 'Su reserva está confirmada',
    'confirmation_heading_cancelled' => 'Esta reserva ha sido cancelada',
    'confirmation_sub_confirmed'   => 'Hemos enviado una invitación de calendario a :email.',
    'confirmation_sub_cancelled'   => 'Esta reunión no tendrá lugar.',
    'meeting_label_row'            => 'Reunión',
    'host_label_row'               => 'Anfitrión',
    'when_label_row'               => 'Cuándo',
    'guest_label_row'              => 'Invitado',
    'location_label_row'           => 'Ubicación',
    'location_see_details'         => 'Ver detalles',
    'add_to_calendar_ics'          => 'Añadir al calendario (.ics)',
    'reschedule_action'            => 'Reprogramar',
    'book_new_time'                => 'Reservar una nueva hora',

    // ─── Booking show page meta ─────────────────────────────────
    'page_title_book_with'         => ':meeting — Reserve con :host',
    'meta_default_schedule_with'   => 'Programe una reunión con :host',
];
