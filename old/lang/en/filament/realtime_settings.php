<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| RealtimeSettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/realtime_settings.<key>').
*/

return [
    'title'                          => 'Realtime & Broadcasting',
    'navigation_label'               => 'Realtime',

    // Driver section
    'driver_section_description'     => 'Choose the realtime broadcast provider for live lead and notification updates.',
    'enable_realtime_label'          => 'Enable Realtime Updates',
    'enable_realtime_helper'         => 'When disabled, the panel uses polling instead of WebSockets.',
    'driver_label'                   => 'Driver',
    'driver_helper'                  => 'Reverb and Soketi are self-hosted Pusher-compatible servers.',
    'driver_option_pusher'           => 'Pusher / Soketi / Reverb (Pusher Protocol)',
    'driver_option_null'             => 'Disabled (polling only)',

    // Pusher section
    'pusher_section_description'     => 'Provide credentials for your Pusher, Reverb, or Soketi server.',
    'pusher_app_id_label'            => 'App ID',
    'pusher_key_label'               => 'App Key',
    'pusher_secret_label'            => 'App Secret',
    'pusher_cluster_label'           => 'Cluster',
    'pusher_cluster_helper'          => 'Leave blank for self-hosted Reverb/Soketi.',
    'pusher_host_label'              => 'Custom Host (Reverb/Soketi)',
    'pusher_host_helper'             => 'Override for self-hosted servers. Leave blank for hosted Pusher.',
    'pusher_port_label'              => 'Port',
    'pusher_scheme_label'            => 'Scheme',
    'pusher_scheme_https'            => 'HTTPS (wss)',
    'pusher_scheme_http'             => 'HTTP (ws)',

    // Status section
    'status_content'                 => 'Save settings to apply. Broadcasting credentials are stored per-tenant and applied at runtime. Restart the queue worker after changing the driver.',

    // Header actions
    'action_save'                    => 'Save Settings',

    // ----- Driver card (Blade) -----
    'section_broadcasting_driver'       => 'Broadcasting Driver',
    'active_driver'                     => 'Active Driver',
    'connection_status'                 => 'Connection Status',
    'status_connected'                  => 'Connected',
    'status_error'                      => 'Error',
    'status_not_configured'             => 'Not configured',
    'status_not_tested'                 => 'Not tested',
    'btn_test_connection'               => 'Test Connection',
    'btn_send_test_notification'        => 'Send Test Notification',
    'test_sent_message'                 => 'Test notification sent! Check your notification bell.',

    // ----- Configuration section (Blade) -----
    'section_configuration'             => 'Configuration',
    'config_description'                => 'Set the following environment variables to enable real-time broadcasting:',
    'option_a_pusher'                   => 'Option A: Pusher (hosted)',
    'option_b_reverb'                   => 'Option B: Laravel Reverb (self-hosted)',
];
