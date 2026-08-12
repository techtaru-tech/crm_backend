<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Calendar sync service — DB-persisted error / fallback strings
|------------------------------------------------------------
| Used by:
|   - app/Services/Calendar/GoogleCalendarSyncService.php
|   - app/Services/Calendar/OutlookCalendarSyncService.php
|   - app/Models/CalendarConnection.php (markError / markNeedsReauth)
|
| Persisted to CalendarConnection.last_sync_error and surfaced
| in the Filament CalendarConnections list. Translate at
| write-time so the message renders in the user's locale.
|
| Accessed via __('services/calendar_sync.<key>').
*/

return [
    'no_refresh_token'        => 'No refresh_token — reconnect',
    'refresh_failed'          => 'Refresh failed: :body',
    'token_refresh_exception' => 'Token refresh exception: :error',
    'meeting_with_guest'      => 'Meeting with :guest',
    'meeting_title_fallback'  => 'LeadHub meeting',
    'push_failed'             => 'Push failed: HTTP :status :body',
    'push_exception'          => 'Push exception: :error',
    'pull_failed'             => 'Pull failed: HTTP :status',
    'pull_exception'          => 'Pull exception: :error',
    'refresh_token_revoked'   => 'Refresh token revoked',
];
