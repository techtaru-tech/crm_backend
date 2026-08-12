<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| MeetingTypeResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/meeting_types.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'                   => 'Meeting Types',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'                 => 'Meeting Type',
    'plural_model_label'          => 'Meeting Types',

    // ----- Form fields -----
    'name'                        => 'Name',
    'slug'                        => 'Slug',
    'description'                 => 'Description',
    'color'                       => 'Color',
    'is_active'                   => 'Active',

    // ----- Action notifications -----
    'booking_link_copied'         => 'Booking link copied',

    // ----- Meeting Type fields -----
    'slug_help'                   => 'Part of the public booking URL. Cannot be changed after creation.',
    'duration'                    => 'Duration',
    'buffer_after_meeting'        => 'Buffer after meeting (minutes)',
    'advance_notice_hours'        => 'Minimum advance notice (hours)',
    'future_days_max'             => 'How far into the future (days)',

    // ----- Location -----
    'location_type'               => 'Location type',
    'location_details'            => 'Location details',
    'location_details_help'       => 'Address, call-in number, or any specific instructions.',

    // ----- Location-type Select options -----
    // The :brand placeholder is interpolated at the call site with the
    // literal brand string (e.g. "Google Meet", "Zoom") so the brand
    // name never gets accidentally translated by a downstream locale.
    'location_option_custom'      => 'Custom (details below)',
    'location_option_google_meet' => ':brand link (provided later)',
    'location_option_zoom'        => ':brand link (provided later)',
    'location_option_phone'       => 'Phone call',
    'location_option_in_person'   => 'In person',

    // ----- Lead Routing -----
    'pipeline'                    => 'Pipeline',
    'stage'                       => 'Stage',

    // ----- Share -----
    'public_booking_link'         => 'Public booking link',

    // ----- Table -----
    'col_duration'                => 'Duration',
    // :n is the numeric duration in minutes (e.g. ":n min" → "30 min").
    'minutes_short'               => ':n min',
    'col_bookings'                => 'Bookings',
    'col_active'                  => 'Active',
    'col_owner'                   => 'Owner',
    'col_link'                    => 'Link',

    // ----- Actions -----
    'share'                       => 'Share',
    'share_modal_heading'         => 'Share ":name"',
    'share_modal_close'           => 'Close',

    // ----- Booking link / embed-snippet panel (form Placeholder content) -----
    'save_first_for_link'         => 'Save first to get a public link.',
    'embed_snippet_label'         => 'Embed snippet:',

    // ----- Share modal labels -----
    'public_url_label'            => 'Public URL',
    'embed_snippet_heading'       => 'Embed snippet',

];
