<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LiveLeadFeed — Filament tenant widget strings
|------------------------------------------------------------
| Accessed via __('filament/live_lead_feed.<key>').
*/

return [
    // ─── Header ───
    'title'        => 'Live Lead Feed',

    // ─── Empty state ───
    'empty'        => 'No leads yet. Leads will appear here in real-time as they arrive.',

    // ─── Table columns ───
    'col_name'     => 'Name',
    'col_email'    => 'Email',
    'col_source'   => 'Source',
    'col_status'   => 'Status',
    'col_received' => 'Received',

    // ─── Row badges & dynamic labels (rendered via Alpine x-text) ───
    'new_tag'      => 'New',

    // Realtime row fallbacks: the websocket broadcast (App\Events\LeadReceived)
    // delivers raw status/source slugs and an Echo callback unshifts them into
    // the Alpine array.  The handler resolves slug→label via the i18n dict
    // injected in the view, but when a slug has no entry we fall back to these
    // generic strings so non-English locales never see the English literal.
    'just_now'      => 'just now',
    'fallback_status_unknown' => 'Unknown',
    'fallback_source_manual'  => 'Manual Entry',
];
