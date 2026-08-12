<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SendLeadMessage job — DB-persisted error_message strings
|------------------------------------------------------------
| Used by app/Jobs/SendLeadMessage.php.
| Strings persist to lead_messages.error_message and render
| in the lead conversation timeline. Translate at write-time
| against the dispatching user's current locale.
|
| Accessed via __('jobs/send_lead_message.<key>').
*/

return [
    'channel_not_enabled'  => ':channel is not enabled for this workspace. Enable it in Settings → Messaging Providers.',
    'unsupported_channel'  => 'Unsupported channel: :channel',
    'whatsapp_missing'     => 'WhatsApp settings missing (whatsapp_business_token / whatsapp_phone_number_id)',
    'whatsapp_api_error'   => 'WhatsApp API error: :body',
    'twilio_missing'       => 'Twilio settings missing (twilio_sid / twilio_token / twilio_from)',
    'twilio_api_error'     => 'Twilio API error: :body',
    'telegram_missing'     => 'Telegram settings missing (telegram_bot_token) or chat_id not set on lead',
    'telegram_api_error'   => 'Telegram API error: :body',
    'viber_missing'        => 'Viber settings missing (viber_auth_token)',
    'viber_api_error'      => 'Viber API error: :body',
];
