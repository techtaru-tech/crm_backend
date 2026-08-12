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
    'nav_label'  => 'Proveedores de mensajería',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Proveedores de mensajería',

    // --- Section descriptions ------------------------------------------
    'whatsapp_description' => 'Envíe y reciba mensajes a través de la Meta Graph API.',
    'sms_description'      => 'SMS bidireccional mediante Twilio Programmable Messaging.',
    'telegram_description' => 'Mensajería entrante y saliente basada en bot.',
    'voice_description'    => 'Llamadas con un clic mediante Twilio Programmable Voice. Reutiliza el Account SID + Auth Token de SMS de arriba; establezca un identificador de llamada distinto a continuación si su número de voz difiere del remitente de SMS.',
    'viber_description'    => 'Viber Business Messages (solo cuentas de servicio aprobadas).',

    // --- Generic field labels ------------------------------------------
    'enabled' => 'Habilitado',

    // --- WhatsApp fields -----------------------------------------------
    'whatsapp_access_token'                => 'Token de acceso permanente',
    'whatsapp_access_token_placeholder'    => 'EAA... (déjelo sin cambios para mantener el actual)',
    'whatsapp_phone_number_id'             => 'ID del número de teléfono',
    'whatsapp_phone_number_id_placeholder' => 'p. ej. 106540352242922',
    'whatsapp_display_phone'               => 'Número de teléfono visible',
    'whatsapp_display_phone_placeholder'   => 'p. ej. +1-415-555-0199',

    // --- Twilio SMS fields ---------------------------------------------
    'twilio_sid'                    => 'Account SID',
    'twilio_sid_placeholder'        => 'AC...',
    'twilio_auth_token'             => 'Auth Token',
    'twilio_auth_token_placeholder' => 'déjelo sin cambios para mantener el actual',
    'twilio_sender_number'          => 'Número del remitente',
    'twilio_sender_placeholder'     => '+14155550199',

    // --- Telegram fields -----------------------------------------------
    'telegram_bot_token'                => 'Token del bot',
    'telegram_bot_token_placeholder'    => '123456:ABC... (déjelo sin cambios para mantener el actual)',
    'telegram_bot_username'             => 'Nombre de usuario del bot',
    'telegram_bot_username_placeholder' => '@su_bot',

    // --- Viber fields --------------------------------------------------
    'viber_auth_token'             => 'Auth Token',
    'viber_auth_token_placeholder' => 'déjelo sin cambios para mantener el actual',
    'viber_sender_name'            => 'Nombre del remitente',
    'viber_sender_placeholder'     => 'Su marca',

    // --- Voice fields --------------------------------------------------
    'voice_caller_id'              => 'Número de identificador de llamada de voz',
    'voice_caller_id_placeholder'  => '+14155550199',
    'voice_caller_id_helper'       => 'Puede diferir del «Número del remitente» de SMS.',
    'voice_record_calls'           => 'Grabar llamadas',
    'voice_record_calls_helper'    => 'Twilio graba ambos extremos de la llamada. Asegúrese de tener consentimiento según la legislación local.',
    'voice_auto_transcribe'        => 'Auto-transcripción + resumen IA',
    'voice_auto_transcribe_helper' => 'Requiere clave de OpenAI API en la configuración de la aplicación / del espacio de trabajo.',
    'voice_webhook_urls'           => 'URLs de Webhook de voz (cópielas en la consola de Twilio)',
    'voice_webhook_unsaved'        => 'Guarde la configuración una vez para generar las URLs de Webhook de Twilio.',
    'twiml_url_label'              => 'TwiML URL: ',
    'status_label'                 => ' | Estado: ',
    'recording_label'              => ' | Grabación: ',

    // --- Inbound webhook URL field -------------------------------------
    'inbound_webhook_url'     => 'URL de Webhook entrante',
    'inbound_webhook_unsaved' => 'Guarde la configuración una vez para generar la URL de Webhook entrante.',

    // --- Header actions ------------------------------------------------
    'save_settings'               => 'Guardar configuración',
    'send_test_message'           => 'Enviar mensaje de prueba',
    'send_test_modal_description' => 'Despacha un mensaje a través del proveedor configurado. El teléfono / chat ID destino recibirá un mensaje real — use su propio contacto.',

    // --- Test send form fields -----------------------------------------
    'test_channel'            => 'Canal',
    'test_target'             => 'Teléfono o Chat ID',
    'test_target_placeholder' => '+15551234567 o telegram_chat_id',
    'test_message'            => 'Mensaje',
    'test_message_default'    => 'Mensaje de prueba desde LeadHub.',

    // --- Test send notifications ---------------------------------------
    'test_send_scheduled' => 'Envío de prueba programado — compruebe su teléfono.',
    'test_send_failed'    => 'El envío de prueba falló: :error',

];
