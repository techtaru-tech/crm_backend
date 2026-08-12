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
    'credentials_missing'  => 'بيانات اعتماد Twilio غير مُكوَّنة',
    'no_phone'             => 'لا يملك العميل المحتمل رقم هاتف',
    'invalid_phone_format' => 'رقم هاتف العميل المحتمل ":phone" ليس بصيغة E.164',
    'empty_body'           => 'نص الرسالة فارغ',
    'twilio_rejected'      => 'رفضت Twilio: :error',
];
