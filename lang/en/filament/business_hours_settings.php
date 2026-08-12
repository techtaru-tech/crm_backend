<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| BusinessHoursSettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/business_hours_settings.<key>').
*/

return [
    'title'                      => 'Business Hours',
    'navigation_label'           => 'Business Hours',

    'section_description'        => 'Restrict automations and SLA alerts to working hours.',
    'timezone_label'             => 'Timezone',
    'weekly_schedule_label'      => 'Weekly schedule',
    'day_label'                  => 'Day',
    'open_label'                 => 'Open',
    'start_label'                => 'Start',
    'end_label'                  => 'End',

    // Days of week
    'day_sunday'                 => 'Sunday',
    'day_monday'                 => 'Monday',
    'day_tuesday'                => 'Tuesday',
    'day_wednesday'              => 'Wednesday',
    'day_thursday'               => 'Thursday',
    'day_friday'                 => 'Friday',
    'day_saturday'               => 'Saturday',

    // Notifications
    'no_tenant'                  => 'No tenant context.',
    'saved'                      => 'Business hours saved.',

    'action_save'                => 'Save Settings',
];
