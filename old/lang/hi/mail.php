<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| ईमेल विषय पंक्तियाँ और साझा कॉपी (Hindi)
|------------------------------------------------------------
| __('mail.<key>', [...]) के माध्यम से पहुँचा जाता है।
|
| snake_case कुंजियाँ, Mailable / Blade फ़ाइल के अनुसार समूहित।
| resources/views/emails/* और app/Mail/*::envelope() में
| उपयोगकर्ता को दिखाई देने वाले सभी अंग्रेज़ी स्ट्रिंग्स यहाँ
| से होकर जाती हैं ताकि CodeCanyon Item 1 (कोई हार्डकोडेड
| उपयोगकर्ता-दृश्य पाठ नहीं) का अनुपालन बना रहे।
*/

return [

    // ─── Shared layout (resources/views/emails/layout.blade.php) ──
    'layout_default_title'        => 'LeadHub',
    'layout_preheader_fallback'   => ':app अधिसूचना',
    'layout_footer_default'       => 'आपको यह ईमेल इसलिए मिला है क्योंकि आप :app के उपयोगकर्ता हैं।',

    // ─── Meeting booked (resources/views/emails/meeting/booked.blade.php) ──
    'meeting_booked_subject_host'        => 'नई बुकिंग: :name के साथ :guest, दिनांक :when',
    'meeting_booked_subject_guest'       => 'आपकी मीटिंग पुष्ट हो गई है: :name, दिनांक :when',
    'meeting_booked_default_name'        => 'मीटिंग',
    'meeting_booked_title'               => 'मीटिंग पुष्ट',
    'meeting_booked_heading_host'        => 'नई बुकिंग प्राप्त हुई',
    'meeting_booked_heading_guest'       => 'आपकी मीटिंग पुष्ट हो गई है',
    'meeting_booked_label_when'          => 'कब',
    'meeting_booked_label_guest'         => 'अतिथि',
    'meeting_booked_label_phone'         => 'फ़ोन',
    'meeting_booked_label_host'          => 'होस्ट',
    'meeting_booked_label_location'      => 'स्थान',
    'meeting_booked_label_notes'         => 'टिप्पणियाँ',
    'meeting_booked_location_google_meet' => 'Google Meet (लिंक शीघ्र ही भेजा जाएगा)',
    'meeting_booked_location_zoom'       => 'Zoom (लिंक शीघ्र ही भेजा जाएगा)',
    'meeting_booked_location_phone'      => 'फ़ोन कॉल',
    'meeting_booked_location_in_person'  => 'व्यक्तिगत रूप से',
    'meeting_booked_location_default'    => 'विवरण नीचे',
    'meeting_booked_btn_reschedule'      => 'पुनर्निर्धारित करें',
    'meeting_booked_btn_cancel'          => 'रद्द करें',
    'meeting_booked_ics_note'            => 'कैलेंडर आमंत्रण (.ics) संलग्न है — इसे खोलकर अपने कैलेंडर में जोड़ें।',

    // ─── Meeting cancelled (resources/views/emails/meeting/cancelled.blade.php) ──
    'meeting_cancelled_subject'   => 'मीटिंग रद्द: :name, दिनांक :when',
    'meeting_cancelled_default_name' => 'मीटिंग',
    'meeting_cancelled_title'     => 'मीटिंग रद्द',
    'meeting_cancelled_body'      => 'मूल रूप से :when (:tz) के लिए निर्धारित मीटिंग रद्द कर दी गई है।',
    'meeting_cancelled_reason'    => 'कारण:',
    'meeting_cancelled_book_again_intro' => 'क्या आपको दूसरा समय चाहिए?',
    'meeting_cancelled_book_again_link'  => 'फिर से बुक करें',

    // ─── Portal magic link (resources/views/emails/portal-magic-link.blade.php) ──
    'portal_magic_link_subject'   => 'आपका :app पोर्टल लॉगिन लिंक',
    'portal_magic_link_greeting'  => 'नमस्ते :name,',
    'portal_magic_link_default_name' => 'आदरणीय',
    'portal_magic_link_body'      => 'यह आपका सुरक्षित साइन-इन लिंक है। अपने खाते तक पहुँचने के लिए नीचे दिए गए बटन पर क्लिक करें। यह लिंक 30 मिनट तक मान्य है और केवल एक बार उपयोग किया जा सकता है।',
    'portal_magic_link_button'    => 'साइन इन करें',
    'portal_magic_link_ignore'    => 'यदि आपने यह लिंक नहीं माँगा था, तो आप इस ईमेल को बिना किसी चिंता के अनदेखा कर सकते हैं।',
    'portal_magic_link_fallback'  => 'लिंक काम नहीं कर रहा? इसे अपने ब्राउज़र में पेस्ट करें:',

    // ─── Tenant welcome (resources/views/emails/tenant-welcome.blade.php) ──
    'tenant_welcome_subject'      => 'आपका :workspace कार्यक्षेत्र तैयार है',
    'tenant_welcome_hello'        => 'नमस्ते,',
    'tenant_welcome_intro'        => 'आपका कार्यक्षेत्र :workspace, :app पर तैयार है।',
    'tenant_welcome_user_set_password' => 'आप कभी भी उस ईमेल और पासवर्ड से साइन इन कर सकते हैं जिन्हें आपने साइन-अप के दौरान चुना था।',
    'tenant_welcome_admin_created'     => 'एक व्यवस्थापक ने आपके लिए यह कार्यक्षेत्र बनाया है। पहली बार पासवर्ड सेट करके लॉग इन करने के लिए नीचे दिए गए बटन का उपयोग करें।',
    'tenant_welcome_workspace_label'   => 'कार्यक्षेत्र:',
    'tenant_welcome_email_label'       => 'ईमेल:',
    'tenant_welcome_button_set_password' => 'अपना पासवर्ड सेट करें और लॉग इन करें',
    'tenant_welcome_button_login'        => 'अपने कार्यक्षेत्र में लॉग इन करें',
    'tenant_welcome_setup_expires'       => 'यह सेटअप लिंक 60 मिनट तक मान्य है। यदि यह समाप्त हो जाए, तो लॉगिन पृष्ठ पर',
    'tenant_welcome_forgot_password'     => '"पासवर्ड भूल गए"',
    'tenant_welcome_setup_expires_suffix' => 'लिंक का उपयोग करें।',
    'tenant_welcome_ignore'              => 'यदि आपने इस ईमेल की अपेक्षा नहीं की थी, तो आप इसे बिना किसी चिंता के अनदेखा कर सकते हैं।',

    // ─── Invitation (resources/views/emails/invitation.blade.php) ──
    'invitation_subject'          => 'आपको :app पर :workspace में आमंत्रित किया गया है',
    'invitation_default_inviter' => 'एक टीम सदस्य',
    'invitation_hello'           => 'नमस्ते,',
    'invitation_body'            => ':inviter ने आपको :app पर :workspace में :role के रूप में शामिल होने के लिए आमंत्रित किया है।',
    'invitation_button'          => 'आमंत्रण स्वीकार करें',
    'invitation_expiry'          => 'यह आमंत्रण 7 दिनों में समाप्त हो जाएगा।',
    'invitation_ignore'          => 'यदि आपने इस आमंत्रण की अपेक्षा नहीं की थी, तो आप इस ईमेल को बिना किसी चिंता के अनदेखा कर सकते हैं।',

    // ─── Password reset (resources/views/emails/password-reset.blade.php) ──
    'password_reset_subject'     => 'अपना :app पासवर्ड रीसेट करें',
    'password_reset_default_name' => 'आदरणीय',
    'password_reset_greeting'    => 'नमस्ते :name,',
    'password_reset_intro'       => 'हमें आपके :app खाते का पासवर्ड रीसेट करने का अनुरोध प्राप्त हुआ है। नया पासवर्ड चुनने के लिए नीचे दिए गए बटन पर क्लिक करें।',
    'password_reset_button'      => 'मेरा पासवर्ड रीसेट करें',
    'password_reset_expires'     => 'यह लिंक :minutes मिनट में समाप्त हो जाएगा। यदि आपने पासवर्ड रीसेट का अनुरोध नहीं किया है, तो आप इस ईमेल को बिना किसी चिंता के अनदेखा कर सकते हैं — आपका पासवर्ड वही रहेगा।',
    'password_reset_fallback'    => 'यदि उपरोक्त बटन काम नहीं करता, तो इस URL को अपने ब्राउज़र में पेस्ट करें:',

    // ─── Payment failed (resources/views/emails/payment-failed.blade.php) ──
    'payment_failed_subject'     => 'कार्रवाई आवश्यक: :workspace के लिए भुगतान विफल',
    'payment_failed_heading'     => 'भुगतान विफल',
    'payment_failed_attempt'     => 'प्रयास :attempt — कृपया अपनी भुगतान विधि अपडेट करें।',
    'payment_failed_greeting'    => 'नमस्ते,',
    'payment_failed_body'        => 'हमने आपके :workspace सब्सक्रिप्शन के लिए पंजीकृत कार्ड पर शुल्क लेने का प्रयास किया और भुगतान सफल नहीं हुआ।',
    'payment_failed_amount_label'      => 'देय राशि',
    'payment_failed_next_retry_label'  => 'अगला स्वचालित पुनः प्रयास',
    'payment_failed_cta_body'    => 'सेवा बाधित न हो, इसके लिए कृपया जल्द से जल्द अपनी भुगतान विधि अपडेट करें। कार्ड अपडेट करने के बाद हम स्वचालित रूप से पुनः शुल्क लेने का प्रयास करेंगे।',
    'payment_failed_button'      => 'भुगतान विधि अपडेट करें',
    'payment_failed_help'        => 'भुगतान विफल होने के सामान्य कारण: कार्ड की समय-सीमा समाप्त होना, अपर्याप्त धनराशि, या बैंक का धोखाधड़ी-निरोधक रोक। यदि आपको सहायता चाहिए, तो इस ईमेल का उत्तर दें।',

    // ─── Plan changed (resources/views/emails/plan-changed.blade.php) ──
    'plan_changed_subject_upgrade'   => 'आपको :plan में अपग्रेड किया गया है',
    'plan_changed_subject_downgrade' => 'आपकी योजना :plan में बदल दी गई है',
    'plan_changed_subject_default'   => 'आपकी योजना :plan में अपडेट कर दी गई है',
    'plan_changed_heading_upgrade'   => 'अब आप :plan पर हैं',
    'plan_changed_heading_downgrade' => 'योजना :plan में अपडेट की गई',
    'plan_changed_heading_default'   => 'योजना :plan में अपडेट की गई',
    'plan_changed_greeting'      => 'नमस्ते,',
    'plan_changed_body'          => ':app पर :workspace के लिए आपकी योजना अपडेट कर दी गई है।',
    'plan_changed_previous_label' => 'पिछली योजना',
    'plan_changed_new_label'     => 'नई योजना',
    'plan_changed_upgrade_note'  => 'नई सुविधाएँ और बढ़ी हुई सीमाएँ आपके कार्यक्षेत्र में पहले से ही अनलॉक हो चुकी हैं। उनका लाभ उठाने के लिए कभी भी लॉग इन करें।',
    'plan_changed_downgrade_note' => 'आपकी नई योजना तुरंत सक्रिय है। आपकी पिछली योजना की कुछ सुविधाएँ अब उपलब्ध नहीं हो सकती हैं — विवरण के लिए बिलिंग पृष्ठ देखें।',
    'plan_changed_button'        => 'बिलिंग डैशबोर्ड देखें',

    // ─── Plan slug labels (Pass 22) ────────────────────────────────────
    // Used by plan-changed.blade.php to translate the old/new plan slug
    // shown in the previous-plan / new-plan rows. Unknown future plans
    // fall back to ucfirst() in the view.
    'plan_value_free'            => 'निःशुल्क',
    'plan_value_starter'         => 'शुरुआती',
    'plan_value_pro'             => 'प्रो',
    'plan_value_business'        => 'व्यवसाय',
    'plan_value_enterprise'      => 'एंटरप्राइज़',
    'plan_value_trial'           => 'परीक्षण',

    // ─── Billing cycle labels (Pass 22) ────────────────────────────────
    // Used by subscription-activated.blade.php to translate the cycle
    // slug (monthly|yearly) interpolated into subscription_activated_billing_cycle.
    'billing_cycle_monthly'      => 'मासिक',
    'billing_cycle_yearly'       => 'वार्षिक',
    'billing_cycle_quarterly'    => 'त्रैमासिक',

    // ─── Subscription activated (resources/views/emails/subscription-activated.blade.php) ──
    'subscription_activated_subject' => ':plan में आपका स्वागत है — सब कुछ तैयार है',
    'subscription_activated_heading' => ':plan में आपका स्वागत है 🎉',
    'subscription_activated_greeting' => 'नमस्ते,',
    'subscription_activated_body' => ':app पर :workspace के लिए आपका सब्सक्रिप्शन अब सक्रिय है। आपने अपने परीक्षण के दौरान जो कुछ भी बनाया है वह सब साथ चलता है — लीड्स, पाइपलाइन्स, ऑटोमेशन्स, इंटीग्रेशन्स।',
    'subscription_activated_billing_cycle' => 'बिलिंग चक्र: :cycle.',
    'subscription_activated_button' => 'बिलिंग डैशबोर्ड देखें',
    'subscription_activated_footer' => 'यदि आपकी योजना के बारे में कोई प्रश्न हो, तो इस ईमेल का उत्तर दें और हम उसका ध्यान रखेंगे।',

    // ─── Subscription cancelled (resources/views/emails/subscription-cancelled.blade.php) ──
    'subscription_cancelled_subject' => 'आपका :workspace सब्सक्रिप्शन रद्द कर दिया गया है',
    'subscription_cancelled_heading' => 'आपका सब्सक्रिप्शन रद्द कर दिया गया है',
    'subscription_cancelled_greeting' => 'नमस्ते,',
    'subscription_cancelled_intro'   => 'हमने :app पर :workspace के लिए आपका सब्सक्रिप्शन रद्द कर दिया है।',
    'subscription_cancelled_ends_at' => 'आपको :date तक पूर्ण पहुँच मिलती रहेगी। उसके बाद, कार्यक्षेत्र रोक दिया जाएगा और इसे जारी रखने के लिए आपको पुनः सक्रिय करना होगा।',
    'subscription_cancelled_immediate' => 'पहुँच तत्काल प्रभाव से रोक दी गई है।',
    'subscription_cancelled_data_safe' => 'आपका डेटा — लीड्स, पाइपलाइन्स, ऑटोमेशन्स — हमारे सर्वर पर सुरक्षित है। यदि आप 90 दिनों के भीतर अपना मन बदलते हैं, तो आप एक क्लिक से पुनः सक्रिय कर सकते हैं और ठीक वहीं से शुरू कर सकते हैं जहाँ आपने छोड़ा था।',
    'subscription_cancelled_reason'   => 'दर्ज किया गया कारण: :reason',
    'subscription_cancelled_button'   => 'सब्सक्रिप्शन पुनः सक्रिय करें',
    'subscription_cancelled_footer'   => 'आपको जाते देखकर खेद है। यदि कुछ ऐसा था जो हम बेहतर कर सकते थे, तो इस ईमेल का उत्तर देकर हमें बताएँ।',

    // ─── Subscription expired (resources/views/emails/subscription-expired.blade.php) ──
    'subscription_expired_subject' => 'आपका :workspace सब्सक्रिप्शन समाप्त हो गया है',
    'subscription_expired_heading' => 'आपका सब्सक्रिप्शन समाप्त हो गया है',
    'subscription_expired_greeting' => 'नमस्ते,',
    'subscription_expired_body'    => ':app पर :workspace के लिए आपका सब्सक्रिप्शन समाप्त हो गया है। व्यवस्थापक पैनल तक पहुँच रोक दी गई है, लेकिन आपका डेटा अभी भी यहाँ है और प्रतीक्षा कर रहा है।',
    'subscription_expired_reactivate' => 'जब आप तैयार हों तब पुनः सक्रिय करें और वहीं से शुरू करें जहाँ आपने छोड़ा था।',
    'subscription_expired_button'  => 'सब्सक्रिप्शन पुनः सक्रिय करें',
    'subscription_expired_footer'  => 'बिलिंग के बारे में प्रश्न? बस इस ईमेल का उत्तर दें।',

    // ─── Trial ending soon (resources/views/emails/trial-ending-soon.blade.php) ──
    'trial_ending_soon_subject_tomorrow' => 'आपका :workspace परीक्षण कल समाप्त हो रहा है',
    'trial_ending_soon_subject_days'     => 'आपका :workspace परीक्षण :days दिनों में समाप्त हो रहा है',
    'trial_ending_soon_heading_one'  => 'आपका परीक्षण :days दिन में समाप्त हो रहा है',
    'trial_ending_soon_heading_other' => 'आपका परीक्षण :days दिनों में समाप्त हो रहा है',
    'trial_ending_soon_greeting'    => 'नमस्ते,',
    'trial_ending_soon_body'        => 'एक सौम्य अनुस्मारक — :app पर :workspace का आपका निःशुल्क परीक्षण :ends_at को समाप्त हो रहा है। अपने सभी लीड्स, पाइपलाइन्स और ऑटोमेशन्स को बिना किसी रुकावट के चलाते रहने के लिए अभी अपग्रेड करें।',
    'trial_ending_soon_after'       => 'आपके परीक्षण के समाप्त होने के बाद, जब तक आप कोई योजना नहीं चुनते, व्यवस्थापक पैनल तक पहुँच रोक दी जाएगी। आपका कोई भी डेटा हटाया नहीं जाएगा।',
    'trial_ending_soon_button'      => 'अपनी योजना चुनें',
    'trial_ending_soon_footer'      => 'प्रश्न? बस इस ईमेल का उत्तर दें और हम आपको सही योजना चुनने में मदद करेंगे।',

    // ─── Trial expired (resources/views/emails/trial-expired.blade.php) ──
    'trial_expired_subject' => 'आपका :workspace परीक्षण समाप्त हो गया है',
    'trial_expired_heading' => 'आपका परीक्षण समाप्त हो गया है',
    'trial_expired_greeting' => 'नमस्ते,',
    'trial_expired_body'   => ':app पर :workspace का आपका निःशुल्क परीक्षण अब समाप्त हो गया है। जब तक आप कोई योजना नहीं चुनते, व्यवस्थापक पैनल तक पहुँच रोक दी गई है — पर चिंता न करें, आपके सभी लीड्स, फ़ॉर्म और सेटिंग्स सुरक्षित हैं।',
    'trial_expired_pick_plan' => 'जब आप तैयार हों तब कोई योजना चुनें और कुछ सेकंडों में पूर्ण पहुँच पर वापस आ जाएँ।',
    'trial_expired_button' => 'अपना कार्यक्षेत्र पुनः सक्रिय करें',
    'trial_expired_footer' => 'चुनने में सहायता चाहिए? इस ईमेल का उत्तर दें — हमें मदद करके खुशी होगी।',

    // ─── Workspace suspended (resources/views/emails/workspace-suspended.blade.php) ──
    'workspace_suspended_subject' => 'आपका :workspace कार्यक्षेत्र निलंबित कर दिया गया है',
    'workspace_suspended_heading' => 'आपका कार्यक्षेत्र निलंबित कर दिया गया है',
    'workspace_suspended_greeting' => 'नमस्ते,',
    'workspace_suspended_body'    => 'आपके सब्सक्रिप्शन की समाप्ति के बाद लंबे समय तक निष्क्रियता के कारण :app पर आपका :workspace कार्यक्षेत्र निलंबित कर दिया गया है। सभी सदस्य व्यवस्थापक पैनल से साइन आउट हो गए हैं।',
    'workspace_suspended_data_safe' => 'आपका डेटा सुरक्षित है — लीड्स, फ़ॉर्म, ऑटोमेशन्स और सेटिंग्स सभी संरक्षित हैं। पुनः सक्रिय करना केवल एक क्लिक की दूरी पर है: कोई योजना चुनें और आपकी टीम कुछ सेकंडों में वापस आ जाएगी।',
    'workspace_suspended_button'  => 'अपना कार्यक्षेत्र पुनः सक्रिय करें',
    'workspace_suspended_footer'  => 'यदि यह एक त्रुटि लगती है या आपको वापस आने में सहायता चाहिए, तो बस इस ईमेल का उत्तर दें और हम इसे हल कर देंगे।',

    // ─── Tenant erasure requested (resources/views/emails/tenant-erasure-requested.blade.php) ──
    'tenant_erasure_requested_subject' => 'आपका :workspace कार्यक्षेत्र :days दिनों में हटा दिया जाएगा',
    'tenant_erasure_requested_heading' => 'कार्यक्षेत्र हटाने का कार्यक्रम निर्धारित',
    'tenant_erasure_requested_greeting' => 'नमस्ते :name,',
    'tenant_erasure_requested_intro'   => 'हमें :app पर :workspace कार्यक्षेत्र को हटाने का आपका अनुरोध प्राप्त हुआ है। आपका डेटा — प्रत्येक लीड, फ़ॉर्म, ऑटोमेशन, इंटीग्रेशन और सेटिंग — :days दिनों में स्थायी रूप से मिटा दिया जाएगा। प्रतीक्षा अवधि बंद होने के बाद यह कार्रवाई पूर्ववत नहीं की जा सकती।',
    'tenant_erasure_requested_window'  => ':days दिनों की प्रतीक्षा अवधि के दौरान आपका कार्यक्षेत्र निलंबित है — साइन-इन अवरुद्ध है, परंतु प्रत्येक रिकॉर्ड अक्षुण्ण रखा गया है यदि आप अपना मन बदलते हैं। आप अवधि बंद होने से पहले किसी भी समय गोपनीयता और डेटा पृष्ठ से हटाने को रद्द कर सकते हैं।',
    'tenant_erasure_requested_button'  => 'हटाना रद्द करें',
    'tenant_erasure_requested_footer'  => 'क्या आपने यह अनुरोध नहीं किया था? तुरंत ऊपर "हटाना रद्द करें" पर क्लिक करें और सहायता से संपर्क करें — हम कार्यक्षेत्र को बंद कर देंगे और जाँच करेंगे। यह संदेश GDPR अनुच्छेद 17 (मिटाने का अधिकार) के तहत हमारी सूचना दायित्वों को पूरा करता है।',

    // ─── Test email (resources/views/emails/test.blade.php) ──
    'test_subject'    => 'परीक्षण ईमेल — :app',
    'test_heading'    => 'ईमेल कॉन्फ़िगरेशन परीक्षण',
    'test_greeting'   => 'नमस्ते :name,',
    'test_body'       => 'यह :app से एक परीक्षण ईमेल है। यदि आपको यह प्राप्त हुआ है, तो आपकी ईमेल सेटिंग्स सही तरीके से कॉन्फ़िगर हैं।',
    'test_continued'  => 'अब आप अपने कार्यक्षेत्र से ब्रांडेड ईमेल भेज सकते हैं।',
    'test_button'     => 'डैशबोर्ड खोलें',

    // ─── Invoice send (app/Filament/Resources/InvoiceResource.php) ──
    'invoice_send_subject' => 'चालान :number',
    'invoice_send_body'    => "नमस्ते :name,\n\nचालान :number तैयार है: :url\n\nधन्यवाद।",

    // ─── Quote send (app/Filament/Resources/QuoteResource.php) ──
    'quote_send_subject'   => 'कोटेशन :number',
    'quote_send_body'      => "नमस्ते :name,\n\nआपका कोटेशन तैयार है: :url\n\nसादर,",

    // ─── Quote send for signature (app/Filament/Resources/QuoteResource/Pages/ViewQuote.php) ──
    'quote_send_review_subject' => 'कोटेशन :number — कृपया समीक्षा करें',
    'quote_send_review_body'    => "नमस्ते :name,\n\nआपका कोटेशन समीक्षा और हस्ताक्षर के लिए तैयार है:\n:url\n\nधन्यवाद।",

    // ─── Notification digest (app/Console/Commands/SendNotificationDigest.php) ──
    'digest_subject'                  => 'आपका :app अधिसूचना सारांश — :datetime',
    'digest_heading'                  => ':app अधिसूचना सारांश',
    'digest_intro_lede'               => 'नमस्ते :name, पिछले एक घंटे में आपने जो छूटा वह यहाँ है',
    'digest_col_type'                 => 'प्रकार',
    'digest_col_details'              => 'विवरण',
    'digest_col_when'                 => 'कब',
    'digest_view_button'              => ':app में देखें',
    'digest_footer_explainer'         => 'आपको यह इसलिए मिल रहा है क्योंकि आपने अधिसूचनाओं को प्रति घंटा सारांश पर सेट किया है।',
    'digest_manage_preferences_link'  => 'प्राथमिकताएँ प्रबंधित करें',
    'digest_fallback_message'         => 'अधिसूचना',

    // ─── Meeting ICS fallbacks (app/Mail/MeetingBookedMail.php, MeetingCancelledMail.php) ──
    'meeting_default_name'   => 'मीटिंग',
    'host_default_name'      => 'होस्ट',
    'meeting_description'    => ':host के साथ मीटिंग। पुनर्निर्धारित करें या रद्द करें: :url',
    // Filename of the .ics attachment buyer sees in their email client.  Use a
    // safe-slug form (no spaces or punctuation other than dash) so all email
    // clients accept the filename unmodified.  Pass-33 i18n fix — without this
    // the English literal "meeting-" prefix leaked into non-EN buyer inboxes.
    'meeting_ics_filename'   => 'meeting',

    // ─── Onboarding drip series (app/Mail/OnboardingDripMail.php) ──
    'drip_day_1_heading'  => 'आपका स्वागत है',
    'drip_day_1_body'     => "हमें खुशी है कि आप यहाँ हैं। यह जानने का सबसे तेज़ तरीका कि यह CRM आपके वर्कफ़्लो के लिए उपयुक्त है या नहीं — एक लीड जोड़ें और उसे इनबॉक्स से जीते हुए तक ले जाएँ।\n\nइसमें लगभग 90 सेकंड लगते हैं। नीचे क्लिक करें और शुरू हो जाएँ।",
    'drip_day_1_cta'      => 'मेरा पहला लीड जोड़ें',

    'drip_day_3_heading'  => 'आपको यह कैसा लग रहा है?',
    'drip_day_3_body'     => "दो दिन हो गए। अधिकांश टीमें इनमें से किसी एक पर अटक जाती हैं:\n\n• सही पाइपलाइन चरण सेट करना → सेटिंग्स → पाइपलाइन्स\n• अपना मौजूदा ईमेल जोड़ना → सेटिंग्स → ईमेल\n• स्प्रेडशीट से लीड्स आयात करना → लीड्स → आयात\n\nयदि आप इनमें से किसी (या किसी और चीज़) में फँसे हैं, तो इस ईमेल का उत्तर दें — हम हर उत्तर पढ़ते हैं।",
    'drip_day_3_cta'      => 'मेरा डैशबोर्ड खोलें',

    'drip_day_5_heading'  => '3 ऑटोमेशन्स जिन्हें हर टीम पहले सप्ताह में चालू करती है',
    'drip_day_5_body'     => "अधिकांश CRMs निष्क्रिय होते हैं — लीड्स वहीं पड़े रहते हैं जब तक कोई ध्यान न दे। ये तीन ऑटोमेशन्स आपके लिए ध्यान देते हैं:\n\n1. नए लीड्स को राउंड-रॉबिन से स्वचालित रूप से असाइन करें ताकि कुछ भी छूटे नहीं\n2. हॉट लीड्स पर Slack को सूचित करें ताकि प्रतिनिधियों को रिफ्रेश न करना पड़े\n3. 7 दिनों के बाद ठंडे लीड्स से एक नरम चेक-इन ईमेल के साथ फिर से जुड़ें\n\nतीनों को सेट करने में 5 मिनट से भी कम लगता है।",
    'drip_day_5_cta'      => 'ऑटोमेशन्स देखें',

    'drip_day_7_heading'  => 'एक सप्ताह बाद — त्वरित जाँच',
    'drip_day_7_body'     => "कैसा चल रहा है?\n\nयदि CRM पहले से ही अपनी कीमत निकाल रहा है (आपने लीड्स जोड़े हैं, आपकी टीम इसका उपयोग कर रही है, आप ऐसे सौदे बंद कर रहे हैं जो अन्यथा खो जाते): बढ़िया — आपका परीक्षण समाप्त होने पर स्वचालित रूप से एक भुगतान योजना में परिवर्तित हो जाता है, कोई कार्रवाई आवश्यक नहीं।\n\nयदि आप अभी भी असमंजस में हैं: इस ईमेल का उत्तर दें और बताएँ कि क्या कमी है। हमने पिछले 6 महीनों में रद्दीकरण फ़ीडबैक से 14 सुविधाएँ लॉन्च की हैं।",
    'drip_day_7_cta'      => 'योजनाएँ देखें',

    'drip_default_heading' => 'आपके CRM से एक संदेश',
    'drip_default_body'    => 'आशा है कि अब तक आपको सब कुछ उपयोगी लग रहा है।',
    'drip_default_cta'     => 'मेरा डैशबोर्ड खोलें',

];
