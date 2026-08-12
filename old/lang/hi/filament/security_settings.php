<?php

declare(strict_types=1);

return [
    'title'                              => 'सुरक्षा सेटिंग्स',
    'navigation_label'                   => 'सुरक्षा',

    'auth_section_description'           => 'ये सेटिंग्स आपकी टीम के प्रत्येक उपयोगकर्ता पर लागू होती हैं और तुरंत प्रभावी होती हैं।',
    'enforce_2fa_label'                  => 'दो-कारक प्रमाणीकरण लागू करें',
    'enforce_2fa_helper_prefix'          => 'जब सक्षम होता है, तो आपकी टीम के प्रत्येक उपयोगकर्ता को अगले अनुरोध पर QR-कोड सेटअप पर पुनर्निर्देशित किया जाता है और जब तक वे नामांकन पूरा नहीं करते, पैनल का उपयोग नहीं कर सकते।',
    'enforce_2fa_helper_link'            => 'अभी अपना 2FA सेट करें →',
    'session_lifetime_label'             => 'सत्र जीवनकाल (मिनट)',
    'minutes_suffix'                     => 'मिनट',

    'max_login_attempts_label'           => 'अधिकतम विफल लॉगिन प्रयास',
    'lockout_duration_label'             => 'लॉकआउट अवधि (मिनट)',

    'ip_whitelist_section_description'   => 'केवल इन IP पतों से एडमिन पैनल तक पहुँच की अनुमति दें। सभी की अनुमति देने के लिए खाली छोड़ दें।',
    'ip_whitelist_label'                 => 'अनुमत IP पते',
    'ip_whitelist_placeholder'           => 'जैसे 192.168.1.1',
    'ip_whitelist_helper'                => 'IP (IPv4 या IPv6) या CIDR रेंज दर्ज करें और प्रत्येक को जोड़ने के लिए Enter दबाएँ।',

    'action_save'                        => 'सेटिंग्स सहेजें',
];
