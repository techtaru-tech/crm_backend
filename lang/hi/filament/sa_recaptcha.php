<?php

declare(strict_types=1);

return [

    'title'                       => 'reCAPTCHA सुरक्षा',
    'navigation_label'            => 'reCAPTCHA',

    'console_intro_prefix'        => 'अपनी कुंजी प्राप्त करें',
    'console_link_label'          => 'Google reCAPTCHA व्यवस्थापक कंसोल',
    'console_intro_suffix'        => 'साइट बनाते समय v3 चुनें। डोमेन सूची में :host जोड़ें।',
    'master_switch_label'         => 'मास्टर स्विच',
    'master_switch_helper'        => 'अपनी कुंजी खोए बिना reCAPTCHA को चालू या बंद करें। बंद होने पर नीचे प्रत्येक गार्ड अनदेखा किया जाता है।',
    'site_key_label'              => 'साइट कुंजी',
    'site_key_helper'             => 'सार्वजनिक कुंजी। ब्राउज़र को भेजी जाती है।',
    'secret_key_label'            => 'सीक्रेट कुंजी',
    'secret_key_helper'           => 'केवल सर्वर कुंजी। प्रस्तुत किए गए टोकनों को Google के विरुद्ध सत्यापित करने के लिए उपयोग की जाती है।',
    'min_score_label'             => 'न्यूनतम स्कोर',
    'min_score_helper'            => '0.0 (निश्चित बॉट) और 1.0 (निश्चित मानव) के बीच स्कोर। इससे नीचे सबमिशन अस्वीकार किए जाते हैं। Google 0.5 की अनुशंसा करता है।',

    'protected_surfaces_description' => 'प्रति-पृष्ठ टॉगल। यदि आप मिथ्या-सकारात्मक अस्वीकृति देखते हैं तो व्यक्तिगत रूप से अक्षम करें; ऊपर मास्टर स्विच इन्हें ओवरराइड करता है।',
    'guard_register_label'        => 'सार्वजनिक /register फ़ॉर्म',
    'guard_register_helper'       => 'स्व-सेवा कार्यक्षेत्र साइन-अप फ़ॉर्म की सुरक्षा करता है।',
    'guard_admin_login_label'     => 'टेनेंट /admin लॉगिन',
    'guard_admin_login_helper'    => 'टेनेंट-सामना करने वाले साइन-इन पृष्ठ की सुरक्षा करता है।',
    'guard_sa_login_label'        => 'सुपर-एडमिन लॉगिन',
    'guard_sa_login_helper'       => 'सुपर-एडमिन साइन-इन पृष्ठ की सुरक्षा करता है।',

    'settings_saved'              => 'reCAPTCHA सेटिंग्स सहेजी गईं',
    'action_save'                 => 'सहेजें',

];
