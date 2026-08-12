<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin PlanResource — Filament admin strings
|------------------------------------------------------------
*/

return [

    // ----- Navigation -----
    'nav_label'                     => 'प्लान्स',
    'tabs_outer'                    => 'प्लान',

    // ----- Tabs -----
    'tab_basics'                    => 'मूल बातें',
    'tab_limits'                    => 'सीमाएँ',
    'tab_features'                  => 'विशेषताएँ',
    'tab_gateway_ids'               => 'गेटवे IDs',

    // ----- Basics tab -----
    'plan_key'                      => 'प्लान की',
    'plan_key_helper'               => 'आंतरिक पहचानकर्ता। लोअरकेस, बिना स्पेस। URLs और कोड में उपयोग किया जाता है।',
    'name_helper'                   => 'मूल्य निर्धारण पृष्ठ और बिलिंग डैशबोर्ड पर दिखाया जाता है।',

    // ----- Pricing section -----
    'monthly_price'                 => 'मासिक मूल्य',
    'monthly_price_helper'          => 'आवर्ती मासिक राशि।',
    'interval_monthly'              => 'मासिक',
    'interval_yearly'               => 'वार्षिक',
    'interval_weekly'               => 'साप्ताहिक',
    'interval_daily'                => 'दैनिक',
    // Short interval labels (badge column on table — short, capitalized).
    'interval_month'                => 'महीना',
    'interval_year'                 => 'वर्ष',
    'interval_helper'               => 'ट्रायल के बाद मूल्य कितनी बार आवर्ती होता है।',
    'trial_days'                    => 'ट्रायल दिन',
    'trial_days_suffix'             => 'दिन',
    'trial_days_helper'             => 'इस प्लान पर वर्कस्पेस शुरू होने पर मुफ्त ट्रायल की अवधि। 0 = कोई ट्रायल नहीं — पहले दिन से बिलिंग।',
    'annual_price'                  => 'वार्षिक मूल्य (अग्रिम)',
    'annual_price_helper'           => 'वैकल्पिक। 12 महीनों के लिए कुल अग्रिम राशि। यदि आप इस प्लान पर वार्षिक बिलिंग प्रदान नहीं करते हैं तो खाली छोड़ दें।',
    'annual_discount_percent'       => 'वार्षिक छूट %',
    'annual_discount_percent_helper'=> 'केवल प्रदर्शन — मूल्य निर्धारण पृष्ठ पर «N% बचाएँ» बैज को फीड करता है (जैसे 20 = «वार्षिक बिलिंग के साथ 20% बचाएँ»)।',

    // ----- Visibility section -----
    'active'                        => 'सक्रिय',
    'active_helper'                 => 'निष्क्रिय प्लान्स हर जगह छिपे रहते हैं और इनकी सदस्यता नहीं ली जा सकती।',
    'public'                        => 'सार्वजनिक',
    'public_helper'                 => 'सार्वजनिक मूल्य निर्धारण पृष्ठ पर दिखाएँ।',
    'highlight'                     => 'हाइलाइट',
    'highlight_helper'              => 'इस प्लान को अनुशंसित के रूप में चिह्नित करता है (मूल्य निर्धारण पृष्ठ पर बैज)।',
    'sort_order'                    => 'क्रमबद्धन क्रम',
    'sort_order_helper'             => 'कम संख्याएँ पहले दिखाई देती हैं।',

    // ----- Limits tab -----
    'limits_description'            => 'असीमित के लिए -1 का उपयोग करें, किसी सुविधा को पूरी तरह से अक्षम करने के लिए 0, या किसी भी सकारात्मक पूर्णांक का उपयोग कड़ी सीमा के लिए करें।',
    'limit_key'                     => 'सीमा की',
    'limit_value'                   => 'मान',
    'add_limit'                     => 'सीमा जोड़ें',

    // ----- Features tab -----
    'features_description'          => 'टॉगल करें कि यह प्लान कौन-सी सुविधाएँ अनलॉक करता है। मान «true» या «false» होने चाहिए।',
    'feature_key'                   => 'विशेषता की',
    'feature_enabled'               => 'सक्षम',
    'add_feature'                   => 'विशेषता जोड़ें',

    // ----- Gateway IDs tab -----
    'gateway_ids_description'       => 'इस प्लान को प्रत्येक पेमेंट गेटवे में उत्पाद/मूल्य ID से मैप करें। यदि गेटवे अक्षम है तो खाली छोड़ दें।',
    'stripe_price_id'               => 'Stripe प्राइस ID',
    'paddle_price_id'               => 'Paddle प्राइस ID',
    'razorpay_plan_id'              => 'Razorpay प्लान ID',
    'paystack_plan_code'            => 'Paystack प्लान कोड',

    // ----- Table columns -----
    'column_number'                 => '#',
    'column_active'                 => 'सक्रिय',
    'column_public'                 => 'सार्वजनिक',
    'column_highlight'              => 'हाइलाइट',

    // ----- Filters -----
    'filter_active_label'           => 'सक्रिय',
    'filter_active'                 => 'सक्रिय',
    'filter_inactive'               => 'निष्क्रिय',
    'filter_label_interval'         => 'अंतराल',

    // ----- Field labels (form + table) -----
    'name'                          => 'नाम',
    'description'                   => 'विवरण',
    'currency'                      => 'मुद्रा',
    'interval'                      => 'अंतराल',
    'limits'                        => 'सीमाएँ',
    'features'                      => 'विशेषताएँ',
    'price'                         => 'मूल्य',
    'updated_at'                    => 'अद्यतन',

    // ----- Model labels -----
    'model_label'                   => 'प्लान',
    'plural_model_label'            => 'प्लान्स',

];
