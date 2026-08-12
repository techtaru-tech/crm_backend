<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — MeetingBookingResource translation strings
|--------------------------------------------------------------------------
|
| Labels, filter copy and action copy for the Bookings resource at
| /admin/meeting-bookings.
| Consumed via __('filament/meeting_bookings.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                     => 'Reservas',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                   => 'Reserva',
    'plural_model_label'            => 'Reservas',

    // ─── Table columns ─────────────────────────────────────────────────
    'guest_name'                    => 'Nombre del invitado',
    'guest_email'                   => 'Correo del invitado',
    'status'                        => 'Estado',
    'meeting_type'                  => 'Tipo de reunión',
    'type'                          => 'Tipo',
    'host'                          => 'Anfitrión',
    'starts'                        => 'Comienza',
    'lead'                          => 'Cliente potencial',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_status'           => 'Estado',

    // ─── Status filter options ─────────────────────────────────────────
    'status_confirmed'              => 'Confirmada',
    'status_cancelled'              => 'Cancelada',
    'status_completed'              => 'Completada',
    'status_no_show'                => 'No asistió',

    // ─── View page: infolist field labels ──────────────────────────────
    'field_guest_name_label'        => 'Nombre del invitado',
    'field_guest_email_label'       => 'Correo del invitado',
    'field_guest_phone_label'       => 'Teléfono del invitado',
    'field_ends_at_label'           => 'Finaliza',
    'field_timezone_label'          => 'Zona horaria',
    'field_status_label'            => 'Estado',
    'field_meeting_url_label'       => 'URL de la reunión',
    'field_notes_label'             => 'Notas',
    'field_cancelled_at_label'      => 'Cancelada el',
    'field_cancellation_reason_label' => 'Motivo de cancelación',

    // ─── Date filters ──────────────────────────────────────────────────
    'filter_from'                   => 'Desde',
    'filter_until'                  => 'Hasta',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_cancel'                 => 'Cancelar',
    'cancellation_reason'           => 'Motivo (opcional)',
    'action_mark_completed'         => 'Marcar como completada',
    'action_mark_no_show'           => 'Marcar como no asistió',
];
