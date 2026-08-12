<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Shared Blade components (resources/views/components/*.blade.php)
|------------------------------------------------------------
| Accessed via __('components.<key>').
*/

return [

    // ─── report-date-range component ────────────────────────────────
    'rdr_date_range_label'   => 'Date Range',
    'rdr_range_last_7_days'  => 'Last 7 days',
    'rdr_range_last_30_days' => 'Last 30 days',
    'rdr_range_last_60_days' => 'Last 60 days',
    'rdr_range_last_90_days' => 'Last 90 days',
    'rdr_range_this_month'   => 'This month',
    'rdr_range_last_month'   => 'Last month',
    'rdr_range_all_time'     => 'All time',
    'rdr_range_custom'       => 'Custom range…',
    'rdr_from_label'         => 'From',
    'rdr_to_label'           => 'To',
    'rdr_date_placeholder'   => 'YYYY-MM-DD',

    // ─── Flatpickr month / weekday labels (calendar popup) ──────────
    //
    // The vendored `flatpickr.min.js` ships only the English `default`
    // locale.  Without these injected labels, the calendar popup in
    // <x-report-date-range /> renders "Sunday/Mon ... January/Jan" in
    // every active app locale.  We pass them in as a literal locale
    // object so flatpickr does not need its locale pack vendored.
    'fp_weekdays_long'  => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    'fp_weekdays_short' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    'fp_months_long'    => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'fp_months_short'   => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    'fp_first_day_of_week' => 0, // Sunday-first (0) for en; ar/es typically Monday (1)
];
