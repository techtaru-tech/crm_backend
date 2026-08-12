<?php

declare(strict_types=1);

return [
    'title'                          => 'रीयल-टाइम और प्रसारण',
    'navigation_label'               => 'रीयल-टाइम',

    'driver_section_description'     => 'लाइव लीड और अधिसूचना अपडेट के लिए रीयल-टाइम प्रसारण प्रदाता चुनें।',
    'enable_realtime_label'          => 'रीयल-टाइम अपडेट सक्षम करें',
    'enable_realtime_helper'         => 'अक्षम होने पर, पैनल WebSockets के बजाय पोलिंग का उपयोग करता है।',
    'driver_label'                   => 'ड्राइवर',
    'driver_helper'                  => 'Reverb और Soketi स्व-होस्टेड Pusher-संगत सर्वर हैं।',
    'driver_option_pusher'           => 'Pusher / Soketi / Reverb (Pusher प्रोटोकॉल)',
    'driver_option_null'             => 'अक्षम (केवल पोलिंग)',

    'pusher_section_description'     => 'अपने Pusher, Reverb, या Soketi सर्वर के लिए क्रेडेंशियल्स प्रदान करें।',
    'pusher_app_id_label'            => 'App ID',
    'pusher_key_label'               => 'App कुंजी',
    'pusher_secret_label'            => 'App सीक्रेट',
    'pusher_cluster_label'           => 'क्लस्टर',
    'pusher_cluster_helper'          => 'स्व-होस्टेड Reverb/Soketi के लिए खाली छोड़ें।',
    'pusher_host_label'              => 'कस्टम होस्ट (Reverb/Soketi)',
    'pusher_host_helper'             => 'स्व-होस्टेड सर्वर के लिए ओवरराइड। होस्टेड Pusher के लिए खाली छोड़ें।',
    'pusher_port_label'              => 'पोर्ट',
    'pusher_scheme_label'            => 'योजना',
    'pusher_scheme_https'            => 'HTTPS (wss)',
    'pusher_scheme_http'             => 'HTTP (ws)',

    'status_content'                 => 'लागू करने के लिए सेटिंग्स सहेजें। प्रसारण क्रेडेंशियल्स प्रति-टेनेंट संग्रहीत किए जाते हैं और रनटाइम पर लागू किए जाते हैं। ड्राइवर बदलने के बाद कतार वर्कर पुनरारंभ करें।',

    'action_save'                    => 'सेटिंग्स सहेजें',

    'section_broadcasting_driver'       => 'प्रसारण ड्राइवर',
    'active_driver'                     => 'सक्रिय ड्राइवर',
    'connection_status'                 => 'कनेक्शन स्थिति',
    'status_connected'                  => 'कनेक्टेड',
    'status_error'                      => 'त्रुटि',
    'status_not_configured'             => 'कॉन्फ़िगर नहीं किया गया',
    'status_not_tested'                 => 'परीक्षण नहीं किया गया',
    'btn_test_connection'               => 'कनेक्शन परीक्षण',
    'btn_send_test_notification'        => 'परीक्षण अधिसूचना भेजें',
    'test_sent_message'                 => 'परीक्षण अधिसूचना भेजी गई! अपनी अधिसूचना घंटी देखें।',

    'section_configuration'             => 'कॉन्फ़िगरेशन',
    'config_description'                => 'रीयल-टाइम प्रसारण सक्षम करने के लिए निम्नलिखित पर्यावरण चर सेट करें:',
    'option_a_pusher'                   => 'विकल्प A: Pusher (होस्टेड)',
    'option_b_reverb'                   => 'विकल्प B: Laravel Reverb (स्व-होस्टेड)',
];
