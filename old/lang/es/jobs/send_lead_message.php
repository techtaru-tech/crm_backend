<?php

declare(strict_types=1);

return [
    'channel_not_enabled'  => ':channel no está activado para este espacio de trabajo. Actívalo en Ajustes → Proveedores de mensajería.',
    'unsupported_channel'  => 'Canal no admitido: :channel',
    'whatsapp_missing'     => 'Faltan ajustes de WhatsApp (whatsapp_business_token / whatsapp_phone_number_id)',
    'whatsapp_api_error'   => 'Error de la API de WhatsApp: :body',
    'twilio_missing'       => 'Faltan ajustes de Twilio (twilio_sid / twilio_token / twilio_from)',
    'twilio_api_error'     => 'Error de la API de Twilio: :body',
    'telegram_missing'     => 'Faltan ajustes de Telegram (telegram_bot_token) o no se ha definido chat_id en el lead',
    'telegram_api_error'   => 'Error de la API de Telegram: :body',
    'viber_missing'        => 'Faltan ajustes de Viber (viber_auth_token)',
    'viber_api_error'      => 'Error de la API de Viber: :body',
];
