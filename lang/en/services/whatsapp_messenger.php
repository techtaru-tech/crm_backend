<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| WhatsApp messenger — outbound send failure strings
|------------------------------------------------------------
| Used by app/Services/Messaging/WhatsAppMessenger.php.
| Failure strings surface in automation run logs and the
| SendWhatsAppAction return value, so translate at write-time
| against the dispatcher's current locale.
|
| Accessed via __('services/whatsapp_messenger.<key>').
*/

return [
    'credentials_missing'  => 'Twilio credentials not configured',
    'no_phone'             => 'Lead has no phone number',
    'invalid_phone_format' => "Lead phone ':phone' is not E.164-format",
    'empty_body'           => 'Empty message body',
    'twilio_rejected'      => 'Twilio rejected: :error',
];
