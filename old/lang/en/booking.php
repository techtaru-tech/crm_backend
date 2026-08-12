<?php

return [
    'confirm_booking' => 'Confirm booking',
    'error_generic'   => 'Something went wrong. Please try again.',
    'error_network'   => 'Network error. Please try again.',

    // Location-type labels for match() in show.blade.php
    'location_google_meet'  => 'Google Meet',
    'location_zoom'         => 'Zoom',
    'location_phone'        => 'Phone call',
    'location_in_person'    => 'In person',
    'location_details_below'=> 'Details below',
    'location_label'        => 'Location:',

    // Show page headings + form labels
    'duration_minutes'             => ':minutes minutes',
    'select_date_and_time'         => 'Select a date & time',
    'aria_previous_month'          => 'Previous month',
    'aria_next_month'              => 'Next month',
    'dow_mon'                      => 'Mon',
    'dow_tue'                      => 'Tue',
    'dow_wed'                      => 'Wed',
    'dow_thu'                      => 'Thu',
    'dow_fri'                      => 'Fri',
    'dow_sat'                      => 'Sat',
    'dow_sun'                      => 'Sun',
    'confirm_your_booking_heading' => 'Confirm your booking',
    'your_name_label'              => 'Your name',
    'email_label'                  => 'Email',
    'phone_optional_label'         => 'Phone (optional)',
    'notes_label'                  => "Anything you'd like to share?",
    'back'                         => 'Back',

    // ─── Confirmation page ────────────────────────────────────
    'cancel'                       => 'Cancel',
    'confirm_cancel_meeting'       => 'Cancel this meeting?',

    // ─── Reschedule page ──────────────────────────────────────
    'cancel_existing_booking'      => 'Cancel existing booking',
    'confirm_cancel_existing'      => 'Cancel the existing booking?',

    // Reschedule view body
    'reschedule_title_prefix'      => 'Reschedule — :name',
    'reschedule_heading'           => 'Reschedule ":name"',
    'reschedule_currently_for'     => 'Currently scheduled for',
    'reschedule_note'              => 'To reschedule, please cancel your current booking and then pick a new time from the booking page. (Keeps things simple for you and the host.)',
    'reschedule_pick_new_time'     => 'Pick a new time',
    'reschedule_back_to_booking'   => 'Back to booking',
    'reschedule_reason_value'      => 'Rescheduling',

    // ─── JS runtime strings (show.js — read via data-* attributes) ─
    'loading_times'                => 'Loading times…',
    'no_available_times'           => 'No available times on this day. Try another date.',
    'could_not_load_times'         => 'Could not load times. Please try again.',
    'at_time_separator'            => ' at ',
    'booking_in_progress'          => 'Booking…',

    // ─── Confirmation page ────────────────────────────────────
    'confirmation_title_confirmed' => 'Booking confirmed — :name',
    'confirmation_title_cancelled' => 'Booking cancelled — :name',
    'confirmation_aria_confirmed'  => 'Booking confirmed',
    'confirmation_aria_cancelled'  => 'Booking cancelled',
    'confirmation_heading_confirmed' => 'You are confirmed',
    'confirmation_heading_cancelled' => 'This booking has been cancelled',
    'confirmation_sub_confirmed'   => "We've sent a calendar invite to :email.",
    'confirmation_sub_cancelled'   => 'This meeting will not take place.',
    'meeting_label_row'            => 'Meeting',
    'host_label_row'               => 'Host',
    'when_label_row'               => 'When',
    'guest_label_row'              => 'Guest',
    'location_label_row'           => 'Location',
    'location_see_details'         => 'See details',
    'add_to_calendar_ics'          => 'Add to calendar (.ics)',
    'reschedule_action'            => 'Reschedule',
    'book_new_time'                => 'Book a new time',

    // ─── Booking show page meta ─────────────────────────────────
    'page_title_book_with'         => ':meeting — Book with :host',
    'meta_default_schedule_with'   => 'Schedule a meeting with :host',
];
