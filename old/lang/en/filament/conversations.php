<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — Conversations translation strings
|--------------------------------------------------------------------------
|
| Notification copy for the inbox-like conversations page at
| /admin/conversations.
| Consumed via __('filament/conversations.<key>').
|
*/

return [

    // ─── Navigation / title ──────────────────────────────────────────
    'nav_label'                      => 'Conversations',
    'title'                          => 'Conversations',

    // ─── Send-message validation ──────────────────────────────────────
    'notif_type_message_first'       => 'Type a message first.',
    'notif_select_conversation'      => 'Select a conversation first.',
    'notif_lead_not_found'           => 'Lead not found.',

    // ─── Channel not enabled ──────────────────────────────────────────
    'notif_channel_not_enabled'      => ':channel is not enabled for this workspace.',
    'notif_channel_not_enabled_body' => 'Visit Settings → Messaging Providers to enable it.',

    // ─── Success ──────────────────────────────────────────────────────
    'notif_message_queued'           => 'Message queued.',

    // ─── Blade view — left panel ──────────────────────────────────────
    'panel_conversations'            => 'Conversations',
    'empty_no_conversations_p1'      => 'No conversations yet.',
    'empty_no_conversations_p2'      => "Start one by sending a message from a lead's page.",
    'out_marker'                     => 'You: ',

    // ─── Blade view — right panel ─────────────────────────────────────
    'lead_prefix'                    => 'Lead #',
    'open_lead'                      => 'Open lead →',
    'media_label'                    => '[media]',
    'compose_placeholder'            => 'Type a message…',
    'compose_via'                    => 'via :channel',
    'compose_send'                   => 'Send',
    'compose_sending'                => 'Sending…',
    'empty_thread'                   => 'No messages in this conversation yet.',
    'empty_select_msg'               => 'Select a conversation on the left to view messages.',
    'empty_select_sub'               => "Or send a message from any lead's profile to start a new thread.",
    'warning_no_channel'             => 'No messaging channel is available for this lead. Enable WhatsApp / SMS / Telegram in',
    'warning_no_channel_link'        => 'Settings → Messaging Providers',
    'warning_no_channel_suffix'      => 'and make sure the lead has a phone number.',
];
