<?php

declare(strict_types=1);

return [
    'channel_not_enabled'  => ':channel غير مفعَّل لهذه المساحة. فعّله في الإعدادات ← مزودو المراسلة.',
    'unsupported_channel'  => 'قناة غير مدعومة: :channel',
    'whatsapp_missing'     => 'إعدادات WhatsApp مفقودة (whatsapp_business_token / whatsapp_phone_number_id)',
    'whatsapp_api_error'   => 'خطأ في WhatsApp API: :body',
    'twilio_missing'       => 'إعدادات Twilio مفقودة (twilio_sid / twilio_token / twilio_from)',
    'twilio_api_error'     => 'خطأ في Twilio API: :body',
    'telegram_missing'     => 'إعدادات Telegram مفقودة (telegram_bot_token) أو chat_id غير معيَّن على العميل المحتمل',
    'telegram_api_error'   => 'خطأ في Telegram API: :body',
    'viber_missing'        => 'إعدادات Viber مفقودة (viber_auth_token)',
    'viber_api_error'      => 'خطأ في Viber API: :body',
];
