<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| BillingPortal — Filament tenant strings (hi)
|------------------------------------------------------------
| Accessed via __('filament/billing_portal.<key>').
*/

return [
    'title'                          => 'बिलिंग और सदस्यता',
    'heading'                        => 'बिलिंग और सदस्यता',
    'navigation_label'               => 'बिलिंग',

    // Subscription state subheadings
    'subheading_on_trial'            => 'आप मुफ़्त ट्रायल पर हैं। किसी भी समय अपग्रेड करें — आपका डेटा साथ चलता है।',
    'subheading_active_paid'         => 'आपकी सदस्यता सक्रिय है।',
    'subheading_trial_expired'       => 'आपका ट्रायल समाप्त हो गया है। जारी रखने के लिए कोई योजना चुनें।',
    'subheading_expired'             => 'आपकी सदस्यता समाप्त हो गई है। पूर्ण पहुँच पुनः प्राप्त करने के लिए नवीनीकरण करें।',
    'subheading_cancelled'           => 'आपकी सदस्यता रद्द कर दी गई है। किसी भी समय पुनः सक्रिय करें।',
    'subheading_suspended'           => 'यह कार्यक्षेत्र निलंबित है। समर्थन से संपर्क करें।',

    // View data defaults
    'state_unknown_label'            => 'अज्ञात',

    // Deletion flow
    'type_delete_title'              => 'पुष्टि करने के लिए DELETE लिखें।',
    'type_delete_body'               => 'कार्यक्षेत्र हटाना विनाशकारी है। आगे बढ़ने के लिए पुष्टिकरण फ़ील्ड में DELETE शब्द लिखें।',
    'no_workspace_title'             => 'कोई कार्यक्षेत्र संदर्भ नहीं।',
    'auth_required_title'            => 'प्रमाणीकरण आवश्यक है।',
    'owner_only_title'               => 'केवल स्वामी।',
    'owner_only_body'                => 'केवल कार्यक्षेत्र का स्वामी ही हटाने की समय-निर्धारण कर सकता है।',
    'password_mismatch_title'        => 'पासवर्ड मेल नहीं खाया।',
    'password_mismatch_body'         => 'कार्यक्षेत्र हटाने की पुष्टि के लिए अपना खाता पासवर्ड पुनः दर्ज करें।',
    'totp_mismatch_title'            => 'दो-कारक कोड मेल नहीं खाया।',
    'totp_mismatch_body'             => 'अपने ऑथेंटिकेटर ऐप से एक नया 6-अंकीय कोड दर्ज करें।',
    'deletion_scheduled_title'       => 'कार्यक्षेत्र हटाने की समय-निर्धारित किया गया',
    'deletion_scheduled_body'        => 'यह कार्यक्षेत्र :days दिनों में स्थायी रूप से हटा दिया जाएगा। तब तक आप इस पृष्ठ या गोपनीयता और डेटा पृष्ठ से रद्द कर सकते हैं।',
    'deletion_cancelled_title'       => 'हटाना रद्द किया गया',
    'deletion_cancelled_body'        => 'आपका कार्यक्षेत्र सक्रिय रहेगा।',

    // Billing details save
    'review_details_title'           => 'कृपया बिलिंग विवरण की समीक्षा करें।',
    'review_details_default_body'    => 'कुछ फ़ील्ड अमान्य हैं।',
    'billing_country_regex'          => 'देश ISO-3166-1 alpha-2 कोड होना चाहिए (जैसे US, DE, GB)।',
    'no_changes_title'               => 'सहेजने के लिए कोई बदलाव नहीं।',
    'billing_details_saved_title'    => 'बिलिंग विवरण सहेजे गए।',
    'billing_details_saved_body'     => 'ये हर भविष्य की रसीद पर दिखाई देंगे।',

    // Subscription event descriptors
    'event_trial_ends'               => 'ट्रायल समाप्त होता है',
    'event_next_renewal'             => 'अगला नवीनीकरण',
    'event_trial_ended'              => 'ट्रायल समाप्त हुआ',
    'event_subscription_ended'       => 'सदस्यता समाप्त हुई',
    'event_access_ends'              => 'पहुँच समाप्त होती है',

    // Gateway labels
    'gateway_stripe'                 => 'Stripe',
    'gateway_paypal'                 => 'PayPal',
    'gateway_razorpay'               => 'Razorpay',
    'gateway_paystack'               => 'Paystack',
    'gateway_manual'                 => 'बैंक हस्तांतरण',

    // ─── Blade view — billing portal page ─────────────────────────────
    'error_no_workspace'             => 'हम आपके कार्यक्षेत्र को हल नहीं कर सके। कृपया लॉग आउट करें और फिर से लॉग इन करें।',
    'cta_choose_plan'                => 'योजना चुनें',
    'section_current_plan'           => 'वर्तमान योजना',
    'price_free'                     => 'मुफ़्त',
    'seat_team_seats'                => 'टीम सीटें',
    'seat_limit_reached'             => 'आप अपनी सीट सीमा तक पहुँच गए हैं। अधिक सदस्यों को आमंत्रित करने के लिए अपग्रेड करें।',
    'features_whats_included'        => 'क्या शामिल है',

    // ─── Feature labels (Pass 22) ─────────────────────────────────────
    'feature_integrations'           => 'एकीकरण',
    'feature_automations'            => 'स्वचालन',
    'feature_api_access'             => 'API पहुँच',
    'feature_custom_domain'          => 'कस्टम डोमेन',
    'feature_white_label'            => 'व्हाइट लेबल',
    'feature_webhooks_outbound'      => 'आउटबाउंड Webhooks',
    'feature_reports_advanced'       => 'उन्नत रिपोर्ट',
    'feature_priority_support'       => 'प्राथमिकता समर्थन',
    'feature_marketplace_install'    => 'मार्केटप्लेस इंस्टॉल',
    'feature_team_collaboration'     => 'टीम सहयोग',
    'feature_unlimited_leads'        => 'असीमित लीड',
    'feature_sso'                    => 'सिंगल साइन-ऑन',
    'no_plan_information'            => 'कोई योजना जानकारी उपलब्ध नहीं।',
    'section_manage_subscription'    => 'सदस्यता प्रबंधित करें',
    'gateway_paying_via_prefix'      => 'के माध्यम से भुगतान',
    'action_change_plan'             => 'योजना बदलें',
    'action_update_payment_method'   => 'भुगतान विधि और इनवॉइस अपडेट करें',
    'action_cancel_subscription'     => 'सदस्यता रद्द करें',
    'support_hint'                   => 'मदद चाहिए? समर्थन से संपर्क करें — हम आपके लिए 24 घंटों के भीतर बिलिंग बदलाव संभाल लेंगे।',
    'section_recent_activity'        => 'हाल की गतिविधि',
    'event_subscription_activated'   => 'सदस्यता सक्रिय की गई',
    'event_subscription_cancelled'   => 'सदस्यता रद्द की गई',
    'event_payment_failed'           => 'भुगतान विफल',
    'event_plan_changed'             => 'योजना बदली गई',
    'event_notification_sent'        => 'सूचना भेजी गई',
    'event_workspace_suspended'      => 'कार्यक्षेत्र निलंबित',
    'event_workspace_reactivated'    => 'कार्यक्षेत्र पुनः सक्रिय',
    'event_auto_suspended'           => 'स्वतः निलंबित (समाप्ति के बाद)',
    'section_available_plans'        => 'उपलब्ध योजनाएँ',
    'toggle_monthly'                 => 'मासिक',
    'toggle_annual'                  => 'वार्षिक',
    'toggle_annual_save_badge'       => '20% तक बचाएँ',
    'plan_tag_recommended'           => 'अनुशंसित',
    'plan_tag_current'               => 'वर्तमान',
    'price_suffix_per_month'         => '/माह',
    'price_suffix_per_year'          => '/वर्ष',
    'plan_save_vs_monthly'           => 'मासिक की तुलना में :pct% बचाएँ',
    'preview_upgrade_strong'         => 'आज बदलें, केवल अंतर का भुगतान करें:',
    'preview_charge_now_label'       => 'अभी शुल्क:',
    'preview_credit_applied_label'   => 'क्रेडिट लागू:',
    'preview_prorated_days_one'      => 'वर्तमान योजना के :count दिन',
    'preview_prorated_days_other'    => 'वर्तमान योजना के :count दिन',
    'preview_downgrade_strong'       => 'क्रेडिट के साथ डाउनग्रेड:',
    'preview_account_credit_label'   => 'खाता क्रेडिट:',
    'preview_applied_next_invoice'   => 'आपके अगले इनवॉइस पर स्वचालित रूप से लागू।',
    'plan_action_switch'             => 'बदलें',
    'plan_action_switch_annual'      => 'बदलें (वार्षिक)',
    'plan_active_label'              => 'सक्रिय',
    'section_billing_details'        => 'बिलिंग विवरण',
    'billing_details_hint'           => 'हर रसीद PDF पर उपयोग किया जाता है। अधिकांश अधिकार-क्षेत्रों में AP / खरीद टीमों द्वारा आवश्यक।',
    'form_business_name_label'       => 'पंजीकृत व्यवसाय का नाम',
    'form_vat_number_label'          => 'VAT / GST नंबर',
    'form_country_label'             => 'देश (ISO-3166-1 alpha-2)',
    'form_country_placeholder'       => 'US',
    'form_billing_address_label'     => 'बिलिंग पता',
    'form_billing_email_label'       => 'बिलिंग ईमेल (AP / लेखा इनबॉक्स)',
    'form_billing_email_placeholder' => 'ap@example.com',
    'form_save_button'               => 'बिलिंग विवरण सहेजें',

    // ─── Connector + interval fallback ───
    'of_connector'                   => 'का',
    'interval_month_fallback'        => 'माह',

    // ─── Interval labels (slug→localized) ─────────────────────────────
    'interval_month'                 => 'माह',
    'interval_year'                  => 'वर्ष',
    'interval_week'                  => 'सप्ताह',
    'interval_day'                   => 'दिन',
];
