<?php

return [
    'app'       => 'एप्लिकेशन',
    'dashboard' => 'डैशबोर्ड',
    'save'      => 'सहेजें',
    'cancel'    => 'रद्द करें',
    'delete'    => 'हटाएँ',
    'edit'      => 'संपादित करें',
    'create'    => 'बनाएँ',
    'search'    => 'खोजें',
    'loading'   => 'लोड हो रहा है…',
    'yes'       => 'हाँ',
    'no'        => 'नहीं',
    'back'      => 'वापस',
    'submit'    => 'सबमिट',
    'confirm'   => 'पुष्टि करें',
    'success'   => 'सफलता',
    'error'     => 'त्रुटि',
    'warning'   => 'चेतावनी',

    'billing_unknown_plan'          => 'अज्ञात योजना [:plan]।',
    'calendar_unsupported_provider' => 'असमर्थित प्रदाता: :provider',
    'registration_throttled'        => 'बहुत अधिक साइन-अप प्रयास हुए हैं। :seconds सेकंड बाद फिर से कोशिश करें।',

    // ─── DemoMode guard / abort copy (live demo lockdown) ───
    'demo_mode_title'            => '🛡️ डेमो मोड',
    'demo_action_disabled_body'  => 'यह क्रिया लाइव डेमो में अक्षम है। सब कुछ अनलॉक करने के लिए अपनी खुद की कॉपी प्राप्त करें।',
    'demo_get_leadhub'           => ':app प्राप्त करें',
    'demo_action_disabled_short' => 'यह क्रिया लाइव डेमो में अक्षम है।',

    // ─── License-required block screen (EnforceLicense middleware) ───
    'license_required_short'              => 'लाइसेंस आवश्यक है।',
    'license_required_title'              => ':app — लाइसेंस आवश्यक है',
    'license_required_heading'            => 'लाइसेंस आवश्यक है',
    'license_required_lead'               => 'व्यवस्थापक पैनल का उपयोग जारी रखने से पहले आपके :app लाइसेंस की पुनः सत्यापन की आवश्यकता है।',
    'license_required_reason_label'       => 'कारण',
    'license_required_step_codecanyon'    => 'अपने CodeCanyon खाते में साइन इन करें और Downloads खोलें।',
    'license_required_step_purchase_code' => 'लाइसेंस सर्टिफिकेट से खरीद कोड कॉपी करें।',
    'license_required_step_paste_settings' => 'इसे सुपर एडमिन → सेटिंग्स → लाइसेंस में पेस्ट करें और "सत्यापित करें" पर क्लिक करें।',
    'license_required_cta_settings'       => 'लाइसेंस सेटिंग्स खोलें',
    'license_required_cta_codecanyon'     => 'CodeCanyon पर जाएँ',
    'license_required_item_label'         => 'CodeCanyon आइटम',

    // ─── Enforce2Fa middleware (JSON 403 for mobile/API) ───
    'two_factor_required' => 'दो-कारक प्रमाणीकरण आवश्यक है। कृपया अपने खाता सेटिंग्स में 2FA सक्षम करें।',

    // ─── Billing controller errors ───
    'billing_checkout_failed'    => 'चेकआउट विफल रहा।',
    'billing_portal_stripe_only' => 'ग्राहक पोर्टल केवल Stripe के लिए उपलब्ध है।',
    'billing_portal_unavailable' => 'ग्राहक पोर्टल अनुपलब्ध है। कृपया पहले एक Stripe चेकआउट पूरा करें या सहायता से संपर्क करें।',

    // ─── Calendar OAuth errors ───
    'calendar_connection_not_found' => 'कनेक्शन नहीं मिला।',
    'calendar_oauth_no_session'     => 'कैलेंडर OAuth कॉलबैक के लिए एक प्रमाणित सत्र आवश्यक है।',
    'oauth_state_mismatch'          => 'OAuth स्थिति मेल नहीं खाती — संभावित CSRF प्रयास।',
    'calendar_disconnected_success' => 'कैलेंडर डिस्कनेक्ट किया गया।',

    // ─── Invitation errors ───
    'invitation_invalid_or_expired' => 'यह आमंत्रण अमान्य है या समाप्त हो चुका है।',

    // ─── Tenant scope errors ───
    'no_tenant_assigned'      => 'आपका खाता किसी भी कार्यक्षेत्र से जुड़ा नहीं है।',
    'session_revoked'         => 'आपका सत्र रद्द कर दिया गया है। कृपया फिर से लॉगिन करें।',
    'no_workspace_resolved'   => 'कोई कार्यक्षेत्र हल नहीं हुआ।',
    'no_workspace_found'      => ':host के लिए कोई कार्यक्षेत्र नहीं मिला।',

    // ─── Data export controller (GDPR Art. 20) ───
    'export_link_invalid'      => 'डाउनलोड लिंक समाप्त हो गया है या अमान्य है।',
    'export_link_expired'      => 'डाउनलोड लिंक समाप्त हो गया है।',
    'export_link_wrong_user'   => 'यह डाउनलोड लिंक किसी अन्य उपयोगकर्ता का है।',
    'export_file_unavailable'  => 'निर्यात फ़ाइल अब उपलब्ध नहीं है। कृपया नया निर्यात अनुरोध करें।',

    // ─── Portal (customer dashboard) ───
    'file_type_not_allowed'     => 'फ़ाइल प्रकार की अनुमति नहीं है।',
    'portal_magic_link_invalid' => 'यह लॉगिन लिंक अमान्य है, समाप्त हो गया है या पहले से उपयोग किया जा चुका है। कृपया नया लिंक अनुरोध करें।',
    'portal_file_uploaded'      => 'फ़ाइल अपलोड की गई।',

    // ─── Impersonation & super admin ───
    'only_super_admins_impersonate'   => 'केवल सुपर एडमिन ही प्रतिरूपण कर सकते हैं।',
    'impersonate_no_owner'            => 'इस कार्यक्षेत्र का कोई स्वामी नहीं है जिसका प्रतिरूपण किया जा सके।',
    'impersonate_already_active'      => 'आप पहले से ही प्रतिरूपण कर रहे हैं। पहले वर्तमान सत्र रोकें।',
    'access_denied_super_admin_only'  => 'पहुँच अस्वीकृत। केवल सुपर एडमिन।',
    'signed_in_as_super_admin_info'   => 'आप एक सुपर एडमिन के रूप में साइन इन हैं। किसी टेनेंट के कार्यक्षेत्र तक पहुँचने के लिए उस पर प्रतिरूपण क्रिया का उपयोग करें।',

    // ─── Security middleware ───
    'access_denied_ip_not_whitelisted' => 'पहुँच अस्वीकृत: आपका IP पता इस कार्यक्षेत्र के लिए श्वेतसूचीबद्ध नहीं है।',
    'forbidden_generic'                => 'निषिद्ध।',

    // ─── Lead attachment guard ───
    'attachment_disk_not_allowed' => 'अनुलग्नक डिस्क अनुमति सूची में नहीं है।',

    // ─── Public quote (customer-facing) ───
    'quote_already_accepted'                  => 'यह कोटेशन पहले ही स्वीकार किया जा चुका है।',
    'quote_already_accepted_cannot_decline'   => 'यह कोटेशन पहले ही स्वीकार किया जा चुका है और इसे अस्वीकार नहीं किया जा सकता।',
    'quote_response_recorded'                 => 'आपकी प्रतिक्रिया दर्ज कर ली गई है। धन्यवाद।',

    // ─── Public invoice (customer-facing) ───
    'invoice_already_paid'             => 'यह इनवॉइस पहले ही भुगतान किया जा चुका है।',
    'invoice_pay_manual_instructions'  => 'कृपया इस पृष्ठ पर दिए गए बैंक विवरण का उपयोग करके राशि स्थानांतरित करें। जैसे ही टेनेंट भुगतान का मिलान करेगा, आपका इनवॉइस भुगतान किया हुआ चिह्नित कर दिया जाएगा।',

    // ─── Integration OAuth (CRM/marketing) ───
    'integration_oauth_unavailable'    => ':type के लिए OAuth उपलब्ध नहीं है। कृपया पहले client_id और client_secret कॉन्फ़िगर करें।',
    'integration_oauth_state_mismatch' => 'OAuth स्थिति मेल नहीं खाती। कृपया फिर से कोशिश करें।',
    'integration_oauth_denied'         => 'OAuth अस्वीकृत: :reason',
    'integration_oauth_no_code'        => 'कोई प्राधिकरण कोड प्राप्त नहीं हुआ।',
    'integration_oauth_exchange_failed'=> 'टोकन एक्सचेंज विफल: :error',
    'integration_oauth_connected'      => ':label OAuth के माध्यम से सफलतापूर्वक कनेक्ट हो गया।',

    // ─── Lead-source OAuth connections ───
    'oauth_not_configured_for_source'  => ':source के लिए OAuth कॉन्फ़िगर नहीं है। कृपया पहले client_id और client_secret जोड़ें।',
    'oauth_session_expired'            => 'OAuth सत्र समाप्त हो गया। कृपया फिर से कोशिश करें।',
    'oauth_state_invalid'              => 'अमान्य OAuth स्थिति। कृपया फिर से कोशिश करें।',
    'oauth_authorization_denied'       => 'OAuth प्राधिकरण अस्वीकृत: :reason',
    'oauth_connection_not_found'       => 'कनेक्शन नहीं मिला या प्राधिकरण कोड अनुपलब्ध है।',
    'oauth_token_exchange_failed'      => 'टोकन एक्सचेंज विफल: :error',
    'oauth_token_retrieval_failed'     => 'एक्सेस टोकन प्राप्त करने में विफल।',
    'oauth_connected_success'          => 'OAuth के माध्यम से सफलतापूर्वक कनेक्ट हो गया।',

    // ─── Public widget submission ───
    'widget_not_found'         => 'विजेट नहीं मिला',
    'widget_submission_failed' => 'सबमिशन विफल',

    // ─── Public form (reCAPTCHA) ───
    'recaptcha_token_missing'      => 'reCAPTCHA टोकन अनुपलब्ध है।',
    'recaptcha_spam_check_failed'  => 'स्पैम जाँच विफल रही।',

    // ─── Public booking endpoints ───
    'booking_invalid_datetime'             => 'अमान्य दिनांक/समय।',
    'booking_time_advance_notice_failed'   => 'वह समय अब अग्रिम-सूचना आवश्यकता को पूरा नहीं करता।',
    'booking_time_too_far_future'          => 'वह समय भविष्य में बहुत दूर है।',
    'booking_slot_taken'                   => 'वह स्लॉट अभी-अभी बुक हो गया। कृपया कोई अन्य समय चुनें।',

    // ─── InvitationService: MAIL=log warning notification ───
    'email_logged_title' => 'ईमेल लॉग किया गया, भेजा नहीं गया',
    'email_logged_body'  => "मेल ड्राइवर 'log' पर सेट है, इसलिए आमंत्रण ईमेल :email तक नहीं पहुँचेगा। इस हस्ताक्षरित लिंक की प्रतिलिपि बनाएँ और इसे मैन्युअल रूप से साझा करें:\n\n:url",

    // ─── PasswordSetupController: post-setup welcome notification ───
    'password_set_title' => 'पासवर्ड सेट हो गया',
    'password_set_body'  => 'स्वागत है! आपका खाता तैयार है।',

    // ─── InvitationController: post-accept welcome notification ───
    'welcome_to_app'         => ':app में आपका स्वागत है',
    'joined_workspace_body'  => 'आप :workspace में शामिल हो गए हैं। यह रहा आपका डैशबोर्ड।',
    'workspace_fallback'     => 'आपका कार्यक्षेत्र',

    'auth' => [
        'email'     => 'ईमेल',
        'password'  => 'पासवर्ड',
        'sign_in'   => 'साइन इन',
        'sign_out'  => 'साइन आउट',
        'register'  => 'खाता बनाएँ',
    ],

    'onboarding' => [
        'subjects' => [
            'day_1'    => ':workspace में आपका स्वागत है — एक लीड के साथ शुरुआत करें',
            'day_3'    => ':workspace में चीज़ें कैसी चल रही हैं?',
            'day_5'    => 'पहले हफ्ते में हर टीम जो ३ ऑटोमेशन चालू करती है',
            'day_7'    => 'त्वरित जाँच: क्या :workspace मूल्य दे रहा है?',
            'fallback' => 'आपके CRM से एक नोट',
        ],
    ],
];
