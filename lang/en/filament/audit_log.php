<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| AuditLogPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/audit_log.<key>').
*/

return [
    'title'                  => 'Audit Log',
    'navigation_label'       => 'Audit Log',

    // Columns
    'col_time'               => 'Time',
    'col_user'               => 'User',
    'col_user_default'       => 'System',
    'col_action'             => 'Action',
    'col_model'              => 'Model',
    'col_record'             => 'Record',
    'col_ip'                 => 'IP',
    'col_url'                => 'URL',

    // Filters
    'filter_user'            => 'User',
    'filter_action'          => 'Action',
    'filter_date_range'      => 'Date Range',
    'filter_from'            => 'From',
    'filter_to'              => 'To',

    // Action filter options
    'action_created'         => 'Created',
    'action_updated'         => 'Updated',
    'action_deleted'         => 'Deleted',
    'action_login'           => 'Login',
    'action_logout'          => 'Logout',

    // Search
    'search_placeholder'     => 'Search by user or IP…',
];
