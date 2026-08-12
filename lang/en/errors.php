<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Error pages (resources/views/errors/*.blade.php)
|------------------------------------------------------------
| Accessed via __('errors.<key>').
*/

return [

    // Shared layout (resources/views/errors/layout.blade.php)
    'error_code_prefix' => 'Error :code',
    'back_to_home'      => 'Back to home',

    // ─── 403 Forbidden ───
    '403_heading' => 'Access denied',
    '403_message' => "You don't have permission to view this page. If you think this is a mistake, please contact your workspace owner.",

    // ─── 404 Not Found ───
    '404_heading' => 'Page not found',
    '404_message' => "We couldn't find the page you were looking for. The link may be broken or the page may have moved.",

    // ─── 419 Page Expired ───
    '419_heading' => 'Page expired',
    '419_message' => 'Your session timed out. Please refresh and try again — your form data is safe.',

    // ─── 500 Server Error ───
    '500_heading' => 'Something went wrong',
    '500_message' => "We hit an unexpected error. The team has been notified and we're looking into it.",

    // ─── 503 Service Unavailable ───
    '503_heading' => 'Down for maintenance',
    '503_message' => "We're updating things right now. Please check back in a few minutes.",
];
