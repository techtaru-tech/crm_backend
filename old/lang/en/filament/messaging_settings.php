<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| MessagingSettingsPage strings — accessed via __('filament/messaging_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: configures per-tenant messaging providers
| (WhatsApp, Twilio SMS, Telegram, Viber, Twilio Voice).
|
| Buyers translate or adapt by editing this file or copying it to
| lang/<locale>/filament/messaging_settings.php and translating values.
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Messaging Providers',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Messaging Providers',

    // --- Section descriptions ------------------------------------------
    'whatsapp_description' => 'Send and receive messages through the Meta Graph API.',
    'sms_description'      => 'Two-way SMS via Twilio Programmable Messaging.',
    'telegram_description' => 'Bot-based inbound and outbound messaging.',
    'voice_description'    => 'Click-to-call via Twilio Programmable Voice. Reuses the SMS Account SID + Auth Token above; set a different caller-ID below if your voice number differs from the SMS sender.',
    'viber_description'    => 'Viber Business Messages (approved service accounts only).',

    // --- Generic field labels ------------------------------------------
    'enabled' => 'Enabled',

    // --- WhatsApp fields -----------------------------------------------
    'whatsapp_access_token'                => 'Permanent Access Token',
    'whatsapp_access_token_placeholder'    => 'EAA... (leave unchanged to keep current)',
    'whatsapp_phone_number_id'             => 'Phone Number ID',
    'whatsapp_phone_number_id_placeholder' => 'e.g. 106540352242922',
    'whatsapp_display_phone'               => 'Display Phone Number',
    'whatsapp_display_phone_placeholder'   => 'e.g. +1-415-555-0199',

    // --- Twilio SMS fields ---------------------------------------------
    'twilio_sid'                    => 'Account SID',
    'twilio_sid_placeholder'        => 'AC...',
    'twilio_auth_token'             => 'Auth Token',
    'twilio_auth_token_placeholder' => 'leave unchanged to keep current',
    'twilio_sender_number'          => 'Sender Number',
    'twilio_sender_placeholder'     => '+14155550199',

    // --- Telegram fields -----------------------------------------------
    'telegram_bot_token'                => 'Bot Token',
    'telegram_bot_token_placeholder'    => '123456:ABC... (leave unchanged to keep current)',
    'telegram_bot_username'             => 'Bot Username',
    'telegram_bot_username_placeholder' => '@your_bot',

    // --- Viber fields --------------------------------------------------
    'viber_auth_token'             => 'Auth Token',
    'viber_auth_token_placeholder' => 'leave unchanged to keep current',
    'viber_sender_name'            => 'Sender Name',
    'viber_sender_placeholder'     => 'Your Brand',

    // --- Voice fields --------------------------------------------------
    'voice_caller_id'              => 'Voice Caller-ID Number',
    'voice_caller_id_placeholder'  => '+14155550199',
    'voice_caller_id_helper'       => 'May differ from the SMS "Sender Number".',
    'voice_record_calls'           => 'Record calls',
    'voice_record_calls_helper'    => 'Twilio records both legs of the call. Ensure you have consent under local law.',
    'voice_auto_transcribe'        => 'Auto-transcribe + AI summary',
    'voice_auto_transcribe_helper' => 'Requires OpenAI API key in app config / tenant settings.',
    'voice_webhook_urls'           => 'Voice webhook URLs (copy into Twilio console)',
    'voice_webhook_unsaved'        => 'Save settings once to generate Twilio webhook URLs.',
    'twiml_url_label'              => 'TwiML URL: ',
    'status_label'                 => ' | Status: ',
    'recording_label'              => ' | Recording: ',

    // --- Inbound webhook URL field -------------------------------------
    'inbound_webhook_url'     => 'Inbound Webhook URL',
    'inbound_webhook_unsaved' => 'Save settings once to generate the inbound webhook URL.',

    // --- Header actions ------------------------------------------------
    'save_settings'               => 'Save Settings',
    'send_test_message'           => 'Send Test Message',
    'send_test_modal_description' => 'Dispatches a message via your configured provider. The target phone / chat ID will receive a real message — use your own contact.',

    // --- Test send form fields -----------------------------------------
    'test_channel'            => 'Channel',
    'test_target'             => 'Phone or Chat ID',
    'test_target_placeholder' => '+15551234567 or telegram_chat_id',
    'test_message'            => 'Message',
    'test_message_default'    => 'Test message from LeadHub.',

    // --- Test send notifications ---------------------------------------
    'test_send_scheduled' => 'Test send scheduled — check your phone.',
    'test_send_failed'    => 'Test send failed: :error',

];
