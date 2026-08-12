<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — NotificationPreferencesPage translation strings
|--------------------------------------------------------------------------
|
| Save-action notification for the user notification preferences page.
| Consumed via __('filament/notification_preferences.<key>').
|
*/

return [
    'nav_label'   => 'Notifications',
    'notif_saved' => 'Notification preferences saved.',

    // ----- Section headings -----
    'section_preferences'                => 'Notification Preferences',
    'section_browser_push'               => 'Browser Push Notifications',

    // ----- Page lede / helper text -----
    'lede'                               => 'Choose how you receive each type of notification. Email frequency applies only to the Email channel.',

    // ----- Table headers -----
    'th_notification_type'               => 'Notification Type',
    'th_in_app'                          => 'In-App',
    'th_email'                           => 'Email',
    'th_email_frequency'                 => 'Email Frequency',
    'th_browser_push'                    => 'Browser Push',

    // ----- Email frequency options -----
    'freq_immediate'                     => 'Immediate',
    'freq_hourly'                        => 'Hourly Digest',
    'freq_off'                           => 'Off',

    // ----- Save button -----
    'save_preferences'                   => 'Save Preferences',

    // ----- Push subscription (shared) -----
    'push_lede'                          => 'Allow :app to send push notifications even when the tab is in the background.',
    'push_lede_legacy'                   => 'Allow :app to send browser notifications even when the tab is in the background.',
    'push_unsupported'                   => 'Browser push notifications are not supported in this browser.',
    'push_subscribing'                   => 'Subscribing...',
    'push_enable_btn'                    => 'Enable Push Notifications',
    'push_enabled'                       => 'Push notifications enabled',

    // ----- OneSignal status messages -----
    'msg_onesignal_not_loaded'           => 'OneSignal not loaded yet. Try again.',
    'msg_push_enabled'                   => 'Push notifications enabled!',
    'msg_permission_denied'              => 'Permission denied by browser.',
    'msg_error_prefix'                   => 'Error: ',
    'msg_subscribe_failed'               => 'Failed to subscribe.',
];
