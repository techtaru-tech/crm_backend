<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Backups page — Filament एडमिन स्ट्रिंग्स (hi)
|------------------------------------------------------------
| __('filament/sa_backups.<key>') के माध्यम से उपयोग किया जाता है।
*/

return [
    'title'                            => 'बैकअप और पुनर्स्थापना',
    'navigation_label'                 => 'बैकअप',

    // सूचनाएँ
    'backup_created_title'             => 'बैकअप बनाया गया।',
    'backup_failed_title'              => 'बैकअप विफल।',
    'backup_deleted_title'             => 'बैकअप हटाया गया।',
    'backup_delete_failed_title'       => 'बैकअप हटाने में असमर्थ।',
    'restore_complete_title'           => 'पुनर्स्थापना पूर्ण।',
    'restore_complete_body'            => ':backup से :files फ़ाइलें और :rows SQL कथन पुनर्स्थापित किए गए।',
    'restore_failed_title'             => 'पुनर्स्थापना विफल।',
    'backup_not_found_title'           => 'बैकअप नहीं मिला।',
    'backup_healthy_title'             => 'बैकअप ठीक दिखता है।',
    'backup_healthy_body'              => ':name — :count SQL कथन पाए गए।',
    'backup_verify_failed_title'       => 'बैकअप सत्यापन विफल।',

    // हेडर क्रियाएँ
    'action_create'                    => 'अभी बैकअप बनाएँ',

    // पुनर्स्थापना मोडल
    'restore_modal_heading'            => 'क्या यह बैकअप पुनर्स्थापित करें?',
    'restore_modal_description'        => 'पुनर्स्थापना चयनित संग्रह की सामग्री के साथ हर डेटाबेस तालिका और अपलोड की गई फ़ाइल को अधिलेखित कर देती है। वर्तमान स्थिति को एक अलग बैकअप के बिना पुनर्प्राप्त नहीं किया जा सकता — यदि आप रोलबैक विकल्प चाहते हैं तो पहले एक नया बैकअप बनाएँ।',
    'restore_modal_submit'             => 'हाँ, पुनर्स्थापित करें',

    // हटाने का मोडल
    'delete_modal_heading'             => 'क्या यह बैकअप हटाएँ?',
    'delete_modal_description'         => 'संग्रह को डिस्क से स्थायी रूप से हटा दिया जाएगा। आपके अन्य बैकअप प्रभावित नहीं होंगे, लेकिन हटाने के बाद इस विशिष्ट स्नैपशॉट को पुनर्प्राप्त नहीं किया जा सकता।',
    'delete_modal_submit'              => 'हटाएँ',

    // Hero / पेज सामग्री
    'hero_eyebrow'                     => 'सिस्टम',
    'hero_title'                       => 'बैकअप और पुनर्स्थापना',
    'hero_sub_html'                    => 'प्रत्येक बैकअप आपके डेटाबेस और अपलोड की गई फ़ाइलों को <code>storage/app/backups/</code> के अंतर्गत एक टाइमस्टैम्प वाली zip में बंडल करता है। जोखिम भरे ऑपरेशन से पहले हेडर में «अभी बैकअप बनाएँ» बटन का उपयोग करें, और जब आपको वापस जाना हो तो एक क्लिक से पुनर्स्थापित करें।',
    'empty_no_backups'                 => 'अभी तक कोई बैकअप नहीं। पहला बनाने के लिए «अभी बैकअप बनाएँ» पर क्लिक करें।',

    // तालिका कॉलम
    'col_archive'                      => 'संग्रह',
    'col_created'                      => 'बनाया गया',
    'col_size'                         => 'आकार',
    'col_actions'                      => 'क्रियाएँ',

    // पंक्ति क्रिया बटन
    'btn_download'                     => 'डाउनलोड',
    'btn_verify'                       => 'सत्यापित करें',
    'btn_restore'                      => 'पुनर्स्थापित करें',
    'btn_delete'                       => 'हटाएँ',

    // रात्रिकालीन टॉगल बैनर
    'nightly_status_strong'            => 'रात्रिकालीन बैकअप: :state।',
    'nightly_state_enabled'            => 'सक्षम',
    'nightly_state_disabled'           => 'अक्षम',
    'nightly_enabled_description'      => 'अनुसूचित कार्य रनर के माध्यम से हर रात 02:00 UTC पर एक नया बैकअप स्वचालित रूप से बनाया जाता है।',
    'nightly_disabled_link_text'       => 'सेटिंग्स → स्क्रिप्ट सेटिंग्स',
    'nightly_disabled_prefix'          => 'स्वचालित सुरक्षा के लिए रात्रिकालीन बैकअप सक्षम करें ',
    'nightly_disabled_suffix'          => ' में।',
    'nightly_footer_note'              => 'ऊपर दिए गए बटन तदर्थ और प्री-अपग्रेड बैकअप के लिए हैं।',
];
