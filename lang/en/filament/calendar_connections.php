<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| CalendarConnectionsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/calendar_connections.<key>').
*/

return [
    'title'                  => 'Calendar Sync',
    'heading'                => 'Calendar Sync',
    'subheading'             => 'Connect your Google or Outlook calendar so meetings booked in the CRM appear there automatically — and external events flow back into your booking timeline.',
    'navigation_label'       => 'Calendar',

    // Provider labels
    'provider_google'        => 'Google Calendar',
    'provider_outlook'       => 'Outlook Calendar',

    // ─── Connection cards (resources/views/filament/pages/settings/calendar-connections.blade.php) ──
    'reconnect'              => 'Reconnect',
    'disconnect'             => 'Disconnect',
    'confirm_disconnect'     => 'Disconnect :provider? Past synced events stay in the CRM; new events will stop appearing.',
    'not_connected_note'     => "Not connected. Bookings created here won't appear on your :provider until you link your account.",

    // ─── Status pill + footer ──────────────────────────────────────
    'last_synced_prefix'     => 'Last synced',
    'last_synced'            => 'Last synced :when',
    'connect_prefix'         => 'Connect',
    'coming_soon'            => 'Coming soon',
    'footnote'               => 'Sync runs every 15 minutes. Outbound pushes happen instantly when you create a booking. We never read your calendar outside the events.* scope.',
];
