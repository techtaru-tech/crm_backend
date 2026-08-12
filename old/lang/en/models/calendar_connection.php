<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| CalendarConnection model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/calendar_connection.<key>').
*/

return [
    // ─── STATUSES ──────────────────────────────────
    'status_active'        => 'Active',
    'status_needs_reauth'  => 'Needs reconnect',
    'status_error'         => 'Error',
    'status_disabled'      => 'Disabled',
];
