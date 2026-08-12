<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — SessionsPage translation strings
|--------------------------------------------------------------------------
|
| Page title and header action for the active sessions page.
| Consumed via __('filament/sessions.<key>').
|
*/

return [
    'nav_label'             => 'Active Sessions',
    'title'                 => 'Active Sessions',
    'revoke_all_others'     => 'Revoke All Other Sessions',

    // ─── Blade view ───────────────────────────────────────────────────
    'section_heading'       => 'Your Active Sessions',
    'pill_current'          => 'Current',
    'meta_browser_on'       => ':browser on :platform',
    'meta_last_active'      => ':ip · Last active :ago',
    'btn_revoke'            => 'Revoke',
    'confirm_revoke'        => 'Are you sure you want to revoke this session?',
    'empty_no_sessions'     => 'No active sessions found.',

    // ─── Browser/platform fallbacks (written to DB by UserSession::parseDeviceInfo) ───
    'browser_unknown'       => 'Unknown',
    'platform_unknown'      => 'Unknown',
];
