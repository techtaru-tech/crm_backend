<?php

declare(strict_types=1);

return [

    'nav_label'                         => 'टीम सदस्य',

    'model_label'                       => 'टीम सदस्य',
    'plural_model_label'                => 'टीम सदस्य',

    'name'                              => 'नाम',
    'email'                             => 'ईमेल',
    'password'                          => 'पासवर्ड',
    'avatar'                            => 'अवतार',
    'created_at'                        => 'बनाया गया',

    'password_helper'                   => 'वर्तमान पासवर्ड रखने के लिए खाली छोड़ दें।',
    'role'                              => 'भूमिका',
    'two_factor_enabled'                => '2FA सक्षम',

    'two_factor_short'                  => '2FA',
    'status'                            => 'स्थिति',
    'status_suspended'                  => 'निलंबित',
    'status_active'                     => 'सक्रिय',

    'action_meeting_link'               => 'मीटिंग लिंक',
    'booking_links_suffix'              => ' की बुकिंग लिंक',
    'modal_close'                       => 'बंद करें',

    'action_send_password_reset'        => 'पासवर्ड रीसेट भेजें',
    'reset_modal_heading_prefix'        => 'रीसेट लिंक भेजें ',
    'reset_modal_heading_suffix'        => ' को?',
    'reset_modal_description'           => 'उपयोगकर्ता को नया पासवर्ड चुनने के लिए लिंक के साथ एक ईमेल प्राप्त होगा। लिंक 60 मिनट में समाप्त हो जाता है।',
    'reset_sent_title'                  => 'रीसेट लिंक भेजा गया',
    'reset_sent_body_prefix'            => 'ईमेल भेजा गया ',
    'reset_sent_body_suffix'            => ' को — 60 मिनट के लिए मान्य।',
    'reset_failed_title'                => 'रीसेट लिंक नहीं भेजा जा सका',

    'action_suspend'                    => 'निलंबित करें',
    'suspend_modal_heading_prefix'      => 'निलंबित करें ',
    'suspend_modal_heading_suffix'      => ' को?',
    'suspend_modal_description'         => 'वे तुरंत पहुँच खो देंगे। अनसस्पेंड क्रिया के साथ किसी भी समय पुनः सक्रिय करें।',
    'suspend_notification_title'        => 'उपयोगकर्ता निलंबित',
    'suspend_notification_body_suffix'  => ' अब साइन इन नहीं कर सकते।',

    'action_unsuspend'                  => 'अनसस्पेंड करें',
    'unsuspend_notification_title'      => 'उपयोगकर्ता पुनः सक्रिय',
    'unsuspend_notification_body_suffix' => ' पुनः साइन इन कर सकते हैं।',

    'invite_action_label'               => 'टीम सदस्य को आमंत्रित करें',
    'invite_email_label'                => 'ईमेल पता',
    'invite_failed_title'               => 'आमंत्रण नहीं भेजा जा सका',
    'invite_sent_title'                 => ':email को आमंत्रण भेजा गया',

    'create_failed_title'               => 'उपयोगकर्ता बनाने में विफल',
    'create_seat_limit_title'           => 'सीट सीमा तक पहुँच गई',
    'create_email_taken_title'          => 'ईमेल पता पहले से उपयोग में है',

    'create_invite_title'               => 'टीम सदस्य को आमंत्रित करें',
    'create_invite_heading'             => 'एक नए टीम सदस्य को आमंत्रित करें',
    'create_invite_subheading'          => 'उन्हें अपना नाम और पासवर्ड सेट करने के लिए लिंक के साथ एक ईमेल प्राप्त होगा।',
    'create_no_workspace_title'         => 'कोई कार्यक्षेत्र संदर्भ नहीं',
    'create_no_workspace_body'          => 'हम आपके कार्यक्षेत्र को हल नहीं कर सके। साइन आउट करें और वापस साइन इन करें, फिर पुनः प्रयास करें।',
    'create_invite_sent_title'          => 'आमंत्रण भेजा गया',
    'create_invite_sent_body'           => 'सेटअप लिंक के साथ एक ईमेल :email को भेजा गया है।',

    'option_role_manager'               => 'मैनेजर',
    'option_role_member'                => 'सदस्य',

    'booking_links_minutes_suffix'      => 'मिनट',

    'subheading_role_permissions_title' => 'भूमिका अनुमतियाँ',
    'subheading_intro'                  => 'टीम सदस्यों के लिए दो स्तर। दोनों टेनेंट-स्कोप्ड हैं — वे इस कार्यक्षेत्र के बाहर डेटा कभी नहीं देखते।',
    'subheading_manager_title'          => 'मैनेजर',
    'subheading_manager_desc'           => 'पूर्ण लीड्स + पाइपलाइन + ऑटोमेशन + फॉर्म। टीम को आमंत्रित और निलंबित कर सकते हैं। एडमिन को हटा नहीं सकते।',
    'subheading_member_title'           => 'सदस्य',
    'subheading_member_desc'            => 'मानक उपयोगकर्ता। लीड्स के साथ काम करें, फॉर्म और रिपोर्ट देखें। कोई टीम प्रबंधन नहीं, कोई सेटिंग्स नहीं।',
];
