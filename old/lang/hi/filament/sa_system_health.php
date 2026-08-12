<?php

declare(strict_types=1);

return [
    'title'                            => 'सिस्टम स्वास्थ्य',

    'label_leadhub_version'            => 'LeadHub संस्करण',
    'label_laravel'                    => 'Laravel',
    'label_php'                        => 'PHP',
    'label_environment'                => 'पर्यावरण',
    'label_debug_mode'                 => 'डिबग मोड',
    'label_queue_driver'               => 'कतार ड्राइवर',
    'label_cache_driver'               => 'कैश ड्राइवर',
    'label_session_driver'             => 'सत्र ड्राइवर',
    'label_mail_driver'                => 'मेल ड्राइवर',
    'label_database'                   => 'डेटाबेस',
    'label_timezone'                   => 'समय क्षेत्र',
    'label_billing'                    => 'बिलिंग',

    'value_on'                         => 'चालू',
    'value_off'                        => 'बंद',
    'value_enabled'                    => 'सक्षम',
    'value_disabled'                   => 'अक्षम',
    'value_not_available'              => 'N/A',

    'card_system_info'                 => 'सिस्टम जानकारी',
    'card_disk_usage'                  => 'डिस्क उपयोग',

    'stat_label_total'                 => 'कुल',
    'stat_label_used'                  => 'उपयोग',
    'stat_label_free'                  => 'मुक्त',

    'disk_used_of_total'               => ':total में से :used उपयोग',

    // ─── Maintenance actions (shared-hosting friendly) ──
    'card_maintenance'                     => 'रखरखाव',

    'action_finalize_update'                 => 'अपडेट को अंतिम रूप दें',
    'action_finalize_update_confirm'         => 'एक ही क्लिक में पोस्ट-अपडेट का पूरा क्रम चलाता है: पुराने bootstrap कैश हटाएँ → लंबित डेटाबेस माइग्रेशन लागू करें → सभी Laravel कैश साफ़ करें (config, route, view, cache) → प्रोडक्शन कैश पुनः बनाएँ (config, route, view, event, Filament कंपोनेंट, blade आइकन)। अपनी इंस्टॉल फ़ाइलें बदलने के तुरंत बाद इसका उपयोग करें (जैसे आपने cPanel फ़ाइल मैनेजर से LeadHub का zip फिर से अपलोड किया है या अपडेट पेज से रिलीज़ zip अपलोड किया है)। कभी भी क्लिक करना सुरक्षित है — पहले से लागू किए गए चरण स्वचालित रूप से छोड़ दिए जाते हैं।',
    'notif_finalize_update_success_title'    => 'अपडेट अंतिम रूप में आ गया',
    'notif_finalize_update_success_body'     => 'पोस्ट-अपडेट के सभी चरण पूरे हो गए। आपकी इंस्टॉल अब नए कोड के साथ पूरी तरह सिंक है।',
    'notif_finalize_update_partial_title'    => 'अपडेट चेतावनियों के साथ अंतिम रूप में आया',
    'notif_finalize_update_failures_label'   => 'जो चरण पूरे नहीं हुए:',
    'notif_finalize_update_failed_title'     => 'अपडेट को अंतिम रूप नहीं दे सके',

    'action_clear_caches'                  => 'सभी कैश साफ़ करें',
    'action_clear_caches_confirm'          => 'config, route, view, event और compiled कैश को एक साथ साफ़ करने के लिए `php artisan optimize:clear` चलाता है। कभी भी चलाना सुरक्षित है — आमतौर पर नया कोड डिप्लॉय करने या साझा होस्टिंग पर सेटिंग्स अपडेट करने के बाद आवश्यक है जहाँ SSH उपलब्ध नहीं है।',
    'notif_clear_caches_success_title'     => 'कैश साफ़ किया गया',
    'notif_clear_caches_success_body'      => 'सभी Laravel कैश साफ़ कर दिए गए।',
    'notif_clear_caches_failed_title'      => 'कैश साफ़ नहीं हो सका',

    'action_run_migrations'                  => 'लंबित डेटाबेस माइग्रेशन लागू करें',
    'action_run_migrations_confirm'          => 'सभी लंबित डेटाबेस माइग्रेशन लागू करने के लिए `php artisan migrate --force` चलाता है। अपनी इंस्टॉल फ़ाइलें बदलने के तुरंत बाद (जैसे आपने cPanel फ़ाइल मैनेजर से LeadHub का zip फिर से अपलोड किया है) इसका उपयोग करें ताकि नए डेटाबेस कॉलम और सेटिंग्स पंक्तियाँ नए कोड से मेल खाएँ। कभी भी क्लिक करना सुरक्षित है — पहले से लागू किए गए माइग्रेशन स्वचालित रूप से छोड़ दिए जाते हैं।',
    'notif_run_migrations_success_title'     => 'माइग्रेशन लागू किए गए',
    'notif_run_migrations_success_body'      => 'सभी लंबित डेटाबेस माइग्रेशन लागू कर दिए गए। अब आप पहले विफल हुई कार्रवाई को फिर से आज़मा सकते हैं।',
    'notif_run_migrations_failed_title'      => 'माइग्रेशन लागू नहीं कर सके',

    'action_rebuild_caches'                => 'कैश पुनः बनाएँ',
    'action_rebuild_caches_confirm'        => 'क्रम में config, route, और view कैश को पहले साफ़ करता है, फिर पुनः बनाता है — धीमी साझा होस्ट पर बाद के अनुरोधों को तेज़ करता है। यदि आप अभी भी बदलाव कर रहे हैं तो इसे छोड़ दें; केवल साफ़ करना पर्याप्त है।',
    'notif_rebuild_caches_success_title'   => 'कैश पुनः बनाए गए',
    'notif_rebuild_caches_success_body'    => 'config, route, और view कैश को साफ़ करके पुनः बनाया गया।',
    'notif_rebuild_caches_failed_title'    => 'कैश पुनः नहीं बना सके',

    'action_storage_link'                  => 'स्टोरेज सिमलिंक बनाएँ',
    'action_storage_link_confirm'          => 'public/storage सिमलिंक बनाता है जिसका उपयोग Laravel अपलोड की गई फ़ाइलें प्रदर्शित करने के लिए करता है। नई इंस्टॉल पर आवश्यक है जहाँ सेटअप के दौरान सिमलिंक नहीं बना था (उदा. open_basedir ने इंस्टॉलर को रोक दिया हो)।',
    'notif_storage_link_success_title'     => 'स्टोरेज सिमलिंक बनाया गया',
    'notif_storage_link_already_title'     => 'स्टोरेज सिमलिंक पहले से मौजूद है',
    'notif_storage_link_failed_title'      => 'स्टोरेज सिमलिंक नहीं बना सके',

    'action_restart_queue'                 => 'क्यू वर्कर पुनः शुरू करें',
    'action_restart_queue_confirm'         => 'चल रहे क्यू वर्करों को नया कोड लेने के लिए शालीनता से पुनः शुरू होने का संकेत देता है। `sync` ड्राइवर पर कोई प्रभाव नहीं — केवल तभी प्रासंगिक है जब आप Horizon या `queue:work` चलाते हैं।',
    'notif_restart_queue_success_title'    => 'क्यू वर्करों को पुनः शुरू करने का संकेत दिया गया',
    'notif_restart_queue_skipped_title'    => 'क्यू ड्राइवर `sync` है',
    'notif_restart_queue_skipped_body'     => '`sync` ड्राइवर पर कोई बैकग्राउंड वर्कर नहीं चलता — पुनः शुरू करने के लिए कुछ नहीं है।',
    'notif_restart_queue_failed_title'     => 'क्यू वर्कर पुनः शुरू नहीं हो सके',
];
