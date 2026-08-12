<?php

declare(strict_types=1);

return [

    'nav_label'                         => 'लीड स्रोत',

    'model_label'                       => 'लीड स्रोत',
    'plural_model_label'                => 'लीड स्रोत',

    'source'                            => 'स्रोत',
    'active'                            => 'सक्रिय',
    'col_name'                          => 'नाम',
    'col_source'                        => 'स्रोत',
    'col_status'                        => 'स्थिति',

    'connection_name'                   => 'कनेक्शन का नाम',
    'connection_name_helper'            => 'जैसे, "Facebook – मुख्य पृष्ठ"',

    'your_webhook_url'                  => 'आपका Webhook URL',
    'webhook_url_placeholder_default'   => 'URL प्राप्त करने के लिए पहले कनेक्शन सहेजें',
    'webhook_url_helper'                => 'इस URL को कॉपी करें और स्रोत प्लेटफ़ॉर्म की Webhook सेटिंग्स में पेस्ट करें।',

    'oauth_description'                 => 'पहले कनेक्शन सहेजें, फिर पहुँच को प्राधिकृत करने के लिए सूची से "OAuth के माध्यम से कनेक्ट करें" पर क्लिक करें। क्रेडेंशियल्स (एक्सेस टोकन, रिफ्रेश टोकन) स्वचालित रूप से संग्रहीत किए जाएंगे।',
    'oauth_instruction_text'            => 'इस कनेक्शन को सहेजने के बाद, प्राधिकृत करने के लिए कनेक्शन सूची में "OAuth के माध्यम से कनेक्ट करें" बटन का उपयोग करें। पुनर्निर्देशन से पहले आपका App ID / Client ID और App Secret / Client Secret आवश्यक हैं।',

    'credentials_description'           => 'इस स्रोत द्वारा आवश्यक क्रेडेंशियल्स भरें। एन्क्रिप्टेड संग्रहीत।',
    'credentials_select_source'         => 'आवश्यक क्रेडेंशियल्स देखने के लिए ऊपर से एक स्रोत चुनें।',
    'credentials_none_required'         => 'इस स्रोत के लिए कोई क्रेडेंशियल आवश्यक नहीं।',

    'message_source_description'        => 'कॉन्फ़िगर करें कि लीड के रूप में संदेश कैसे कैप्चर किए जाते हैं।',
    'qualification_keywords'            => 'योग्यता कीवर्ड',
    'qualification_keywords_helper'     => 'जब कोई इन शब्दों में से किसी एक को शामिल करने वाला संदेश भेजता है तो एक लीड बनाई जाती है। सभी संदेश कैप्चर करने के लिए खाली छोड़ें।',
    'qualification_keywords_placeholder' => 'कीवर्ड जोड़ें…',

    'meta_page_description'             => 'OAuth के माध्यम से कनेक्ट करने के बाद, लीड प्राप्ति के लिए उपयोग करने हेतु Facebook/Instagram पृष्ठ चुनें।',
    'active_page'                       => 'सक्रिय पृष्ठ',
    'active_page_helper'                => 'इस पृष्ठ के एक्सेस टोकन का उपयोग Meta Lead Ads API से लीड्स प्राप्त करने के लिए किया जाएगा।',

    'field_mapping_description'         => 'स्रोत फ़ॉर्म फ़ील्ड्स को LeadHub लीड फ़ील्ड्स (first_name, last_name, email, phone) में मैप करें।',
    'field_mapping'                     => 'फ़ील्ड मैपिंग',
    'source_field_name'                 => 'स्रोत फ़ील्ड नाम',
    'leadhub_field_value'               => 'LeadHub फ़ील्ड (first_name / last_name / email / phone)',

    'leads'                             => 'लीड्स',
    'last_lead'                         => 'अंतिम लीड',
    'webhook_url'                       => 'Webhook URL',

    'filter_label_source'               => 'स्रोत',
    'filter_label_status'               => 'स्थिति',
    'status_connected'                  => 'कनेक्टेड',
    'status_disconnected'               => 'डिस्कनेक्टेड',
    'status_error'                      => 'त्रुटि',

    'action_connect_oauth'              => 'OAuth के माध्यम से कनेक्ट करें',
    'action_connect_oauth_tooltip'      => 'क्रेडेंशियल्स ऑटो-फ़िल करने के लिए OAuth के माध्यम से पहुँच प्राधिकृत करें',
    'action_test'                       => 'परीक्षण',
    'test_failed_message'               => 'परीक्षण कनेक्शन विफल',

    'empty_heading'                     => 'कोई लीड स्रोत कनेक्ट नहीं',
    'empty_description'                 => 'लीड्स कैप्चर करना शुरू करने के लिए एक लीड स्रोत कनेक्ट करें।',
];
