<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PublicBookingController — JSON error strings
|--------------------------------------------------------------------------
|
| Server-side validation/error messages returned from public booking
| JSON endpoints (availability, store, cancel, reschedule).
| Consumed via __('public_booking.<key>').
|
*/

return [

    // ─── Availability endpoint ────────────────────────────────────────
    'invalid_date'   => 'Invalid date',

    // ─── iCalendar (.ics) export ──────────────────────────────────────
    // Renders inside external calendar clients (Google Calendar, Apple
    // Calendar, Outlook). Keep strings short and locale-safe.
    'ical_meeting_fallback' => 'Meeting',
    'ical_host_fallback'    => 'Host',
    'ical_description'      => 'Meeting with :host. Reschedule or cancel: :url',
];
