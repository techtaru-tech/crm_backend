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
    'nav_label'                     => 'Bookings',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                   => 'Booking',
    'plural_model_label'            => 'Bookings',

    // ─── Table columns ─────────────────────────────────────────────────
    'guest_name'                    => 'Guest Name',
    'guest_email'                   => 'Guest Email',
    'status'                        => 'Status',
    'meeting_type'                  => 'Meeting Type',
    'type'                          => 'Type',
    'host'                          => 'Host',
    'starts'                        => 'Starts',
    'lead'                          => 'Lead',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_status'           => 'Status',

    // ─── Status filter options ─────────────────────────────────────────
    'status_confirmed'              => 'Confirmed',
    'status_cancelled'              => 'Cancelled',
    'status_completed'              => 'Completed',
    'status_no_show'                => 'No show',

    // ─── View page: infolist field labels ──────────────────────────────
    'field_guest_name_label'        => 'Guest name',
    'field_guest_email_label'       => 'Guest email',
    'field_guest_phone_label'       => 'Guest phone',
    'field_ends_at_label'           => 'Ends',
    'field_timezone_label'          => 'Timezone',
    'field_status_label'            => 'Status',
    'field_meeting_url_label'       => 'Meeting URL',
    'field_notes_label'             => 'Notes',
    'field_cancelled_at_label'      => 'Cancelled at',
    'field_cancellation_reason_label' => 'Cancellation reason',

    // ─── Date filters ──────────────────────────────────────────────────
    'filter_from'                   => 'From',
    'filter_until'                  => 'Until',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_cancel'                 => 'Cancel',
    'cancellation_reason'           => 'Reason (optional)',
    'action_mark_completed'         => 'Mark completed',
    'action_mark_no_show'           => 'Mark no-show',
];
