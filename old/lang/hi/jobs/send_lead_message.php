<?php

declare(strict_types=1);

return [
    'channel_not_enabled'  => ':channel इस कार्यक्षेत्र के लिए सक्षम नहीं है। इसे Settings → Messaging Providers में सक्षम करें।',
    'unsupported_channel'  => 'असमर्थित चैनल: :channel',
    'whatsapp_missing'     => 'WhatsApp सेटिंग्स गायब हैं (whatsapp_business_token / whatsapp_phone_number_id)',
    'whatsapp_api_error'   => 'WhatsApp API त्रुटि: :body',
    'twilio_missing'       => 'Twilio सेटिंग्स गायब हैं (twilio_sid / twilio_token / twilio_from)',
    'twilio_api_error'     => 'Twilio API त्रुटि: :body',
    'telegram_missing'     => 'Telegram सेटिंग्स गायब हैं (telegram_bot_token) या लीड पर chat_id सेट नहीं है',
    'telegram_api_error'   => 'Telegram API त्रुटि: :body',
    'viber_missing'        => 'Viber सेटिंग्स गायब हैं (viber_auth_token)',
    'viber_api_error'      => 'Viber API त्रुटि: :body',
];
