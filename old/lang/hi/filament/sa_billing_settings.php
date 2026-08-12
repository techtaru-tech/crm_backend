<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin BillingSettingsPage — Filament admin strings
|------------------------------------------------------------
*/

return [

    // ----- Page title -----
    'title'                       => 'पेमेंट गेटवे और जीवनचक्र सेटिंग्स',
    'navigation_label'            => 'पेमेंट और जीवनचक्र',
    'tabs_outer'                  => 'गेटवे',

    // ----- One-time cron setup -----
    'cron_section_heading'        => '⚙️ एक बार का सर्वर सेटअप',
    'cron_section_description'    => 'ट्रायल रिमाइंडर, समाप्ति ईमेल और दैनिक बैकअप वास्तव में चलने के लिए, आपके सर्वर को एक cron एंट्री की आवश्यकता होती है। इसे एक बार जोड़ें और स्क्रिप्ट का प्रत्येक निर्धारित कार्य स्वचालित रूप से सक्रिय हो जाता है।',
    'cron_setup_step_label'       => 'इस लाइन को अपने होस्टिंग पैनल के Cron Jobs (cPanel / Plesk / DirectAdmin) में, या अपने सर्वर के crontab में जोड़ें:',
    'cron_setup_step_thats_it'    => 'बस इतना ही — एक बार सेट करें और भूल जाएँ। स्क्रिप्ट बाकी सब कुछ आंतरिक रूप से संभालती है (कब प्रत्येक ईमेल भेजना है, कब ट्रायल समाप्त करना है, कब बैकअप लेना है, आदि)।',
    'cron_setup_step_support'     => 'यदि आप सुनिश्चित नहीं हैं कि cron एंट्री कैसे जोड़ें, तो आपके होस्टिंग प्रदाता का सपोर्ट यह आपके लिए एक मिनट से भी कम समय में कर सकता है — बस उन्हें ऊपर दी गई लाइन भेजें।',

    // ----- Trial & lifecycle section -----
    'trial_lifecycle_description' => 'नियंत्रित करता है कि ट्रायल कितने समय तक चलते हैं, समाप्ति से पहले / बाद में रिमाइंडर कब भेजे जाते हैं, और क्या समाप्त हो चुके टेनेंट्स को छूट अवधि के बाद स्वतः निलंबित किया जाता है। ProcessSubscriptionLifecycle प्रत्येक cron रन पर इन्हें पढ़ता है — ऐप रिबूट की कोई आवश्यकता नहीं।',
    'trial_days_label'            => 'डिफ़ॉल्ट ट्रायल अवधि (दिन)',
    'trial_days_suffix'           => 'दिन',
    'trial_days_helper'           => 'जब चुने गए प्लान में कोई प्रति-प्लान trial_days न हो तब उपयोग किया जाता है। प्रत्येक प्लान अभी भी इसे प्लान्स पृष्ठ से ओवरराइड कर सकता है।',
    'trial_reminder_days_label'   => 'ट्रायल रिमाइंडर लय (ट्रायल समाप्त होने से पहले के दिन)',
    'reminder_placeholder'        => 'दिन की संख्या टाइप करें और Enter दबाएँ',
    'trial_reminder_days_helper'  => 'जैसे 7, 3, 1 → trial_ends_at से 7, 3 और 1 दिन पहले ईमेल भेजे जाते हैं। प्रत्येक पूर्णांक एक रिमाइंडर है। खाली = कोई रिमाइंडर नहीं।',
    'post_expiry_reminder_days_label' => 'समाप्ति-पश्चात ड्रिप लय (समाप्ति के बाद के दिन)',
    'post_expiry_reminder_days_helper' => 'जैसे 3, 7, 14 → लैप्स हो चुके टेनेंट्स को पुनः प्राप्त करने के लिए समाप्ति के 3, 7 और 14 दिन बाद ड्रिप ईमेल भेजे जाते हैं। खाली = कोई ड्रिप नहीं।',
    'auto_suspend_after_label'    => 'इसके बाद स्वतः निलंबित करें (समाप्ति के बाद के दिन)',
    'auto_suspend_after_helper'   => '0 = कभी स्वतः निलंबित न करें। अन्यथा, समाप्त टेनेंट्स को उनकी समाप्ति तिथि के इतने दिनों के बाद active=false पर पलट दिया जाता है और अंतिम सूचना ईमेल की जाती है।',

    // ----- Enabled gateways section -----
    'enabled_gateways_description'=> 'केवल वे गेटवे जिन्हें आप यहाँ चेक करते हैं (और जिनके क्रेडेंशियल्स नीचे भरे हुए हैं) मूल्य निर्धारण पृष्ठ पर टेनेंट्स को प्रदान किए जाएँगे।',
    'field_enabled_gateways_label'=> 'सक्षम गेटवे',
    'gateway_stripe'              => 'Stripe (कार्ड)',
    'gateway_paypal'              => 'PayPal',
    'gateway_razorpay'            => 'Razorpay',
    'gateway_paystack'            => 'Paystack',
    'gateway_manual'              => 'बैंक ट्रांसफर (मैन्युअल)',

    // ----- Stripe tab -----
    'tab_stripe'                  => 'Stripe',
    'test_mode'                   => 'टेस्ट मोड',
    'stripe_publishable_key'      => 'पब्लिशेबल की',
    'stripe_secret_key'           => 'सीक्रेट की',
    'webhook_signing_secret'      => 'Webhook साइनिंग सीक्रेट',
    'stripe_webhook_helper'       => 'वैकल्पिक लेकिन अनुशंसित। अपने Stripe Webhook को :url पर इंगित करें',

    // ----- PayPal tab -----
    'tab_paypal'                  => 'PayPal',
    'sandbox_mode'                => 'सैंडबॉक्स मोड',
    'paypal_client_id'            => 'क्लाइंट ID',
    'paypal_client_secret'        => 'क्लाइंट सीक्रेट',
    'paypal_webhook_id'           => 'Webhook ID',
    'paypal_webhook_helper'       => 'Webhook एंडपॉइंट: :url',

    // ----- Razorpay tab -----
    'tab_razorpay'                => 'Razorpay',
    'razorpay_key_id'             => 'की ID',
    'razorpay_key_secret'         => 'की सीक्रेट',
    'razorpay_webhook_secret'     => 'Webhook सीक्रेट',
    'razorpay_webhook_helper'     => 'Webhook एंडपॉइंट: :url',

    // ----- Paystack tab -----
    'tab_paystack'                => 'Paystack',
    'paystack_public_key'         => 'पब्लिक की',
    'paystack_secret_key'         => 'सीक्रेट की',

    // ----- Manual bank transfer tab -----
    'tab_manual_bank'             => 'मैन्युअल बैंक ट्रांसफर',
    'manual_bank_name'            => 'बैंक का नाम',
    'manual_account_name'         => 'खाता धारक का नाम',
    'manual_account_number'       => 'खाता संख्या',
    'manual_iban'                 => 'IBAN',
    'manual_swift'                => 'SWIFT / BIC',
    'manual_extra_instructions'   => 'अतिरिक्त निर्देश',
    'manual_extra_helper'         => 'टेनेंट के इस गेटवे को चुनने के बाद ट्रांसफर-निर्देश पृष्ठ पर दिखाया जाता है।',

    // ----- Notifications -----
    'settings_saved'              => 'सेटिंग्स सहेजी गईं।',
    'no_gateway_configured'       => 'अभी तक कोई भी गेटवे सक्षम और पूर्ण रूप से कॉन्फ़िगर नहीं है।',
    'active_gateways'             => 'सक्रिय गेटवे: :labels',
    'stripe_mismatch_title'       => 'Stripe टेस्ट-मोड बेमेल',
    'stripe_mismatch_body'        => '«टेस्ट मोड» टॉगल :toggle है लेकिन सीक्रेट की का उपसर्ग :prefix दर्शाता है। Stripe टॉगल से नहीं, की उपसर्ग के द्वारा रूटिंग करता है — दोनों को मेल खाने के लिए किसी एक को बदलें।',
    'toggle_on'                   => 'चालू',
    'toggle_off'                  => 'बंद',

    // ----- Header actions -----
    'save_settings'               => 'सेटिंग्स सहेजें',

    // ----- Hero -----
    'hero_eyebrow'                => 'बिलिंग',
    'hero_title'                  => 'पेमेंट गेटवे कॉन्फ़िगर करें',
    'body_intro'                  => 'एक साथ एक या अधिक गेटवे लॉन्च करें। प्रत्येक टेनेंट मूल्य निर्धारण पृष्ठ पर अपनी पसंदीदा विधि चुनता है। Stripe और PayPal अधिकांश वैश्विक ट्रैफ़िक को कवर करते हैं; Razorpay और Paystack क्रमशः भारत और अफ्रीका के लिए उत्कृष्ट हैं; मैन्युअल बैंक ट्रांसफर कहीं भी काम करता है और एंटरप्राइज़ इनवॉइसिंग के लिए आदर्श है।',

    // Affiliate program
    'affiliate_section_heading'     => 'एफिलिएट प्रोग्राम',
    'affiliate_section_description' => 'नए भुगतान करने वाले वर्कस्पेस रेफ़र करने वाले टेनेंट को दी जाने वाली कमीशन। रेफ़र किए गए वर्कस्पेस के प्रत्येक आवर्ती भुगतान पर लागू होती है; कमीशन की समीक्षा और भुगतान बिलिंग → एफिलिएट कमीशन के अंतर्गत करें।',
    'affiliate_commission_label'    => 'कमीशन दर',
    'affiliate_commission_helper'   => 'प्रत्येक रेफ़र किए गए भुगतान का वह प्रतिशत जो एफिलिएट कमीशन के रूप में दर्ज होता है। एफिलिएट प्रोग्राम बंद करने के लिए 0 सेट करें।',
];
