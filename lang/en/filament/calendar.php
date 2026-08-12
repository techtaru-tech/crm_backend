<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Calendar (task) page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/calendar.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label' => 'Task Calendar',

    // ─── Filter labels ───
    'filter_date_range'   => 'Date Range',
    'filter_assigned_to'  => 'Assigned To',
    'filter_status'       => 'Status',

    // ─── Date range options ───
    'range_this_week'     => 'This Week',
    'range_this_month'    => 'This Month',
    'range_next_30'       => 'Next 30 Days',

    // ─── Assigned-to options ───
    'all_users'           => 'All Users',

    // ─── Status options ───
    'status_all'          => 'All',
    'status_pending'      => 'Pending',
    'status_completed'    => 'Completed',
    'status_overdue'      => 'Overdue',

    // ─── Legend ───
    'legend_upcoming'     => 'Upcoming',
    'legend_overdue'      => 'Overdue',
    'legend_completed'    => 'Completed',

    // ─── FullCalendar button labels (passed via JS as buttonText overrides) ───
    'btn_today'           => 'today',
    'btn_month'           => 'month',
    'btn_week'            => 'week',
    'btn_list'            => 'list',
];
