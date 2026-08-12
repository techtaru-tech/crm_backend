<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — MeetingAvailabilityPage translation strings
|--------------------------------------------------------------------------
|
| Labels, helper copy and action labels for the user-facing meeting
| availability page at /admin/meeting-availability.
| Consumed via __('filament/meeting_availability.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'               => 'Meeting Availability',

    // ─── Section ──────────────────────────────────────────────────────
    'section_description'     => 'Times when guests can book meetings with you. All times are in your local timezone.',

    // ─── Repeater + form ──────────────────────────────────────────────
    'day'                     => 'Day',
    'add_time_slot'           => 'Add time slot',
    'field_start_time_label'  => 'Start time',
    'field_end_time_label'    => 'End time',

    // ─── Day-of-week options (Select) ─────────────────────────────────
    'day_sunday'              => 'Sunday',
    'day_monday'              => 'Monday',
    'day_tuesday'             => 'Tuesday',
    'day_wednesday'           => 'Wednesday',
    'day_thursday'            => 'Thursday',
    'day_friday'              => 'Friday',
    'day_saturday'            => 'Saturday',

    // ─── Header actions ───────────────────────────────────────────────
    'preset_weekdays_label'   => 'Weekdays 9-5',
    'save'                    => 'Save',
    'save_availability'       => 'Save availability',
];
