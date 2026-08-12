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
    'nav_label'  => 'مزودو المراسلة',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'مزودو المراسلة',

    // --- Section descriptions ------------------------------------------
    'whatsapp_description' => 'أرسل واستقبل الرسائل عبر Meta Graph API.',
    'sms_description'      => 'SMS ثنائي الاتجاه عبر Twilio Programmable Messaging.',
    'telegram_description' => 'مراسلة واردة وصادرة عبر البوت.',
    'voice_description'    => 'الاتصال بنقرة واحدة عبر Twilio Programmable Voice. يعيد استخدام Account SID + Auth Token من SMS أعلاه؛ عيّن caller-ID مختلفًا أدناه إذا كان رقم الصوت يختلف عن مرسل SMS.',
    'viber_description'    => 'رسائل Viber للأعمال (حسابات الخدمة المعتمدة فقط).',

    // --- Generic field labels ------------------------------------------
    'enabled' => 'مُفعَّل',

    // --- WhatsApp fields -----------------------------------------------
    'whatsapp_access_token'                => 'رمز الوصول الدائم',
    'whatsapp_access_token_placeholder'    => 'EAA... (اتركه دون تغيير للحفاظ على الحالي)',
    'whatsapp_phone_number_id'             => 'معرّف رقم الهاتف',
    'whatsapp_phone_number_id_placeholder' => 'مثل: 106540352242922',
    'whatsapp_display_phone'               => 'رقم الهاتف المعروض',
    'whatsapp_display_phone_placeholder'   => 'مثل: +1-415-555-0199',

    // --- Twilio SMS fields ---------------------------------------------
    'twilio_sid'                    => 'Account SID',
    'twilio_sid_placeholder'        => 'AC...',
    'twilio_auth_token'             => 'Auth Token',
    'twilio_auth_token_placeholder' => 'اتركه دون تغيير للحفاظ على الحالي',
    'twilio_sender_number'          => 'رقم المرسل',
    'twilio_sender_placeholder'     => '+14155550199',

    // --- Telegram fields -----------------------------------------------
    'telegram_bot_token'                => 'رمز البوت',
    'telegram_bot_token_placeholder'    => '123456:ABC... (اتركه دون تغيير للحفاظ على الحالي)',
    'telegram_bot_username'             => 'اسم مستخدم البوت',
    'telegram_bot_username_placeholder' => '@your_bot',

    // --- Viber fields --------------------------------------------------
    'viber_auth_token'             => 'Auth Token',
    'viber_auth_token_placeholder' => 'اتركه دون تغيير للحفاظ على الحالي',
    'viber_sender_name'            => 'اسم المرسل',
    'viber_sender_placeholder'     => 'علامتك التجارية',

    // --- Voice fields --------------------------------------------------
    'voice_caller_id'              => 'رقم Voice Caller-ID',
    'voice_caller_id_placeholder'  => '+14155550199',
    'voice_caller_id_helper'       => 'قد يختلف عن «رقم المرسل» الخاص بـ SMS.',
    'voice_record_calls'           => 'تسجيل المكالمات',
    'voice_record_calls_helper'    => 'يسجّل Twilio طرفي المكالمة. تأكد من الحصول على الموافقة بموجب القانون المحلي.',
    'voice_auto_transcribe'        => 'النسخ التلقائي + ملخص الذكاء الاصطناعي',
    'voice_auto_transcribe_helper' => 'يتطلب مفتاح OpenAI API في إعدادات التطبيق / المستأجر.',
    'voice_webhook_urls'           => 'روابط Voice webhook (انسخها إلى وحدة تحكم Twilio)',
    'voice_webhook_unsaved'        => 'احفظ الإعدادات مرة واحدة لتوليد روابط Twilio webhook.',
    'twiml_url_label'              => 'TwiML URL: ',
    'status_label'                 => ' | الحالة: ',
    'recording_label'              => ' | التسجيل: ',

    // --- Inbound webhook URL field -------------------------------------
    'inbound_webhook_url'     => 'رابط Webhook الوارد',
    'inbound_webhook_unsaved' => 'احفظ الإعدادات مرة واحدة لتوليد رابط webhook الوارد.',

    // --- Header actions ------------------------------------------------
    'save_settings'               => 'حفظ الإعدادات',
    'send_test_message'           => 'إرسال رسالة اختبارية',
    'send_test_modal_description' => 'يرسل رسالة عبر مزودك المُهيَّأ. سيستلم الهاتف / chat ID المستهدف رسالة حقيقية — استخدم جهة الاتصال الخاصة بك.',

    // --- Test send form fields -----------------------------------------
    'test_channel'            => 'القناة',
    'test_target'             => 'الهاتف أو Chat ID',
    'test_target_placeholder' => '+15551234567 أو telegram_chat_id',
    'test_message'            => 'الرسالة',
    'test_message_default'    => 'رسالة اختبارية من LeadHub.',

    // --- Test send notifications ---------------------------------------
    'test_send_scheduled' => 'تم جدولة إرسال الاختبار — تحقق من هاتفك.',
    'test_send_failed'    => 'فشل إرسال الاختبار: :error',

];
