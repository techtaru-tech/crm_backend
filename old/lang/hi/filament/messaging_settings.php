<?php

declare(strict_types=1);

return [

    'nav_label'  => 'मैसेजिंग प्रदाता',

    'page_title' => 'मैसेजिंग प्रदाता',

    'whatsapp_description' => 'Meta Graph API के माध्यम से संदेश भेजें और प्राप्त करें।',
    'sms_description'      => 'Twilio Programmable Messaging के माध्यम से दो-तरफ़ा SMS।',
    'telegram_description' => 'बॉट-आधारित आवक और जावक संदेश।',
    'voice_description'    => 'Twilio Programmable Voice के माध्यम से क्लिक-टू-कॉल। ऊपर SMS Account SID + Auth Token का पुनः उपयोग करता है; यदि आपका वॉइस नंबर SMS प्रेषक से भिन्न है तो नीचे एक अलग कॉलर-ID सेट करें।',
    'viber_description'    => 'Viber Business Messages (केवल अनुमोदित सेवा खाते)।',

    'enabled' => 'सक्षम',

    'whatsapp_access_token'                => 'स्थायी एक्सेस टोकन',
    'whatsapp_access_token_placeholder'    => 'EAA... (वर्तमान बनाए रखने के लिए अपरिवर्तित छोड़ें)',
    'whatsapp_phone_number_id'             => 'फ़ोन नंबर ID',
    'whatsapp_phone_number_id_placeholder' => 'जैसे 106540352242922',
    'whatsapp_display_phone'               => 'प्रदर्शन फ़ोन नंबर',
    'whatsapp_display_phone_placeholder'   => 'जैसे +1-415-555-0199',

    'twilio_sid'                    => 'खाता SID',
    'twilio_sid_placeholder'        => 'AC...',
    'twilio_auth_token'             => 'Auth टोकन',
    'twilio_auth_token_placeholder' => 'वर्तमान बनाए रखने के लिए अपरिवर्तित छोड़ें',
    'twilio_sender_number'          => 'प्रेषक नंबर',
    'twilio_sender_placeholder'     => '+14155550199',

    'telegram_bot_token'                => 'बॉट टोकन',
    'telegram_bot_token_placeholder'    => '123456:ABC... (वर्तमान बनाए रखने के लिए अपरिवर्तित छोड़ें)',
    'telegram_bot_username'             => 'बॉट उपयोगकर्ता नाम',
    'telegram_bot_username_placeholder' => '@your_bot',

    'viber_auth_token'             => 'Auth टोकन',
    'viber_auth_token_placeholder' => 'वर्तमान बनाए रखने के लिए अपरिवर्तित छोड़ें',
    'viber_sender_name'            => 'प्रेषक का नाम',
    'viber_sender_placeholder'     => 'आपका ब्रांड',

    'voice_caller_id'              => 'वॉइस कॉलर-ID नंबर',
    'voice_caller_id_placeholder'  => '+14155550199',
    'voice_caller_id_helper'       => 'SMS "प्रेषक नंबर" से भिन्न हो सकता है।',
    'voice_record_calls'           => 'कॉल रिकॉर्ड करें',
    'voice_record_calls_helper'    => 'Twilio कॉल के दोनों चरण रिकॉर्ड करता है। सुनिश्चित करें कि आपके पास स्थानीय कानून के तहत सहमति है।',
    'voice_auto_transcribe'        => 'ऑटो-ट्रांसक्राइब + AI सारांश',
    'voice_auto_transcribe_helper' => 'ऐप कॉन्फ़िगरेशन / टेनेंट सेटिंग्स में OpenAI API कुंजी आवश्यक है।',
    'voice_webhook_urls'           => 'वॉइस webhook URL (Twilio कंसोल में कॉपी करें)',
    'voice_webhook_unsaved'        => 'Twilio webhook URL उत्पन्न करने के लिए एक बार सेटिंग्स सहेजें।',
    'twiml_url_label'              => 'TwiML URL: ',
    'status_label'                 => ' | स्थिति: ',
    'recording_label'              => ' | रिकॉर्डिंग: ',

    'inbound_webhook_url'     => 'आवक Webhook URL',
    'inbound_webhook_unsaved' => 'आवक webhook URL उत्पन्न करने के लिए एक बार सेटिंग्स सहेजें।',

    'save_settings'               => 'सेटिंग्स सहेजें',
    'send_test_message'           => 'परीक्षण संदेश भेजें',
    'send_test_modal_description' => 'आपके कॉन्फ़िगर किए गए प्रदाता के माध्यम से एक संदेश भेजता है। लक्षित फ़ोन / चैट ID को एक वास्तविक संदेश प्राप्त होगा — अपने स्वयं के संपर्क का उपयोग करें।',

    'test_channel'            => 'चैनल',
    'test_target'             => 'फ़ोन या चैट ID',
    'test_target_placeholder' => '+15551234567 या telegram_chat_id',
    'test_message'            => 'संदेश',
    'test_message_default'    => 'LeadHub से परीक्षण संदेश।',

    'test_send_scheduled' => 'परीक्षण भेजना निर्धारित — अपना फ़ोन देखें।',
    'test_send_failed'    => 'परीक्षण भेजना विफल: :error',

];
