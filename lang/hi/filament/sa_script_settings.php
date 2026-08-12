<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin ScriptSettings — Filament admin स्ट्रिंग्स
|------------------------------------------------------------
| __('filament/sa_script_settings.<key>') के माध्यम से एक्सेस किया जाता है।
| खरीदार इस फ़ाइल को संपादित करके या lang/<locale>/filament/sa_script_settings.php
| पर कॉपी करके अनुवाद या अनुकूलित कर सकते हैं।
*/

return [

    // ----- पेज शीर्षक / नेविगेशन -----
    'title'                            => 'स्क्रिप्ट सेटिंग्स',
    'navigation_label'                 => 'स्क्रिप्ट सेटिंग्स',

    // ----- रिपोर्टिंग और स्थानीयकरण अनुभाग -----
    'reporting_section_description'    => 'बिलिंग डैशबोर्ड, मूल्य निर्धारण पेज, और कहीं भी जहाँ Tenant ने अपना मान नहीं चुना है, उसके लिए उपयोग किए जाने वाले डिफ़ॉल्ट। Spatie Laravel Settings डेटाबेस पेलोड में संग्रहीत — .env पुनः लेखन से बचता है और सहेजने पर तुरंत प्रभावी होता है (कैश क्लियर की आवश्यकता नहीं)।',
    'reporting_currency_label'         => 'रिपोर्टिंग मुद्रा',
    'reporting_currency_helper'        => 'बिलिंग डैशबोर्ड पर MRR / ARR / ARPU आंकड़ों के लिए उपयोग किया जाता है।',
    'default_timezone_label'           => 'डिफ़ॉल्ट टाइमज़ोन',
    'date_format_label'                => 'तिथि प्रारूप',
    'default_language_label'           => 'डिफ़ॉल्ट भाषा',
    'default_language_helper'          => 'नए विज़िटर्स के लिए डिफ़ॉल्ट UI भाषा सेट करता है। शामिल: अंग्रेज़ी, अरबी (RTL), स्पेनिश, हिंदी।',

    // जब config('locales.supported') खाली हो तो अंतिम-सहारा लेबल —
    // हमें अभी भी कम से कम एक Select विकल्प की आवश्यकता है, इसलिए हम
    // 'en' => __('locale_fallback_english_native') पर वापस आते हैं।
    // en लोकेल में स्वयं "English" ही रहता है, जानबूझकर आत्म-संदर्भात्मक।
    'locale_fallback_english_native'   => 'English',

    // ----- बैकअप अनुभाग -----
    'backups_section_description'      => 'डेटाबेस और अपलोड की गई फ़ाइलों का स्वचालित रात्रिकालीन बैकअप। बैकअप storage/app/backups/ के तहत संग्रहीत किए जाते हैं।',
    'auto_nightly_backup_label'        => 'रात्रिकालीन स्वचालित बैकअप सक्षम करें',
    'auto_nightly_backup_helper'       => 'सक्षम होने पर, अनुसूचित कार्य रनर (cron.php) के माध्यम से हर रात 02:00 UTC पर एक नया बैकअप बनाया जाता है।',

    // ----- सार्वजनिक मार्केटिंग लैंडिंग अनुभाग -----
    'landing_section_description'      => 'विज़िटर्स रूट URL (/) पर कौन सा लैंडिंग पेज देखते हैं। परिवर्तन सहेजने के बाद प्रभावी होता है — कैश क्लियर की आवश्यकता नहीं।',
    'landing_template_label'           => 'लैंडिंग टेम्पलेट',
    'landing_template_helper'          => 'सुपर-एडमिन के रूप में साइन इन रहते हुए /?preview=1 के साथ किसी भी वैरिएंट का पूर्वावलोकन करें। परिवर्तन सहेजने के तुरंत बाद प्रभावी होता है — कैश क्लियर की आवश्यकता नहीं।',
    'landing_variant_light'            => 'लाइट — पॉलिश सफ़ेद मार्केटिंग हीरो (डिफ़ॉल्ट)',
    'landing_variant_warm'             => 'वार्म — क्रीम पृष्ठभूमि, सेरिफ़ डिस्प्ले, संपादकीय पत्रिका जैसा अनुभव',
    'landing_variant_modern'           => 'मॉडर्न — डार्क थीम, बैंगनी/गुलाबी ग्रेडिएंट',
    'landing_variant_editorial'        => 'एडिटोरियल — डार्क मोनोक्रोम, post-Linear bento',
    'landing_variant_classic'          => 'क्लासिक — कंटेंट-भारी मूल टेम्पलेट',

    // ----- आउटबाउंड ईमेल / SMTP अनुभाग -----
    'smtp_section_description'         => 'आपकी .env फ़ाइल में लिखे गए स्क्रिप्ट-व्यापी मेल डिफ़ॉल्ट (सहेजने पर config:clear ट्रिगर होता है)। भेजने के समय प्राथमिकता क्रम: (1) ईमेल सेटिंग्स से Tenant का अपना SMTP → (2) ये स्क्रिप्ट-स्तरीय .env मान → (3) Laravel का हार्ड-कोडेड "log" ड्राइवर फ़ॉलबैक। जो Tenants अपना पेज खाली छोड़ते हैं वे स्वचालित रूप से इन डिफ़ॉल्ट पर वापस आ जाते हैं।',
    'mailer_label'                     => 'Mailer',
    'mailer_helper'                    => 'जब तक आप परीक्षण कर रहे हों, इसे "Log" पर रखें। नीचे आपके क्रेडेंशियल काम करने के बाद SMTP पर स्विच करें।',
    'mailer_option_smtp'               => 'SMTP',
    'mailer_option_sendmail'           => 'sendmail',
    'mailer_option_log'                => 'Log (केवल dev — कोई वास्तविक ईमेल नहीं भेजा गया)',
    'mailer_option_array'              => 'Array (परीक्षण — मेल को त्याग देता है)',
    'smtp_host_label'                  => 'SMTP होस्ट',
    'smtp_host_placeholder'            => 'smtp.example.com',
    'smtp_port_label'                  => 'SMTP पोर्ट',
    'smtp_port_placeholder'            => '587 (STARTTLS), 465 (SSL), 25 (बिना एन्क्रिप्शन)',
    'encryption_label'                 => 'एन्क्रिप्शन',
    'encryption_option_tls'            => 'TLS (STARTTLS — अनुशंसित)',
    'encryption_option_ssl'            => 'SSL',
    'encryption_option_none'           => 'कोई नहीं',
    'smtp_username_label'              => 'SMTP उपयोगकर्ता नाम',
    'smtp_password_label'              => 'SMTP पासवर्ड',
    'smtp_password_placeholder'        => 'वर्तमान रखने के लिए अपरिवर्तित छोड़ें',
    'from_address_label'               => 'भेजने वाले का पता',
    'from_address_placeholder'         => 'noreply@yourdomain.com',
    'from_name_label'                  => 'भेजने वाले का नाम',
    'from_name_placeholder'            => 'LeadHub',

    // ----- सूचनाएँ -----
    'settings_saved_title'             => 'स्क्रिप्ट सेटिंग्स सहेजी गईं।',
    'settings_saved_body'              => 'रिपोर्टिंग + मेल डिफ़ॉल्ट अपडेट किए गए। यदि आपने Mailer बदला है, तो यह पुष्टि करने के लिए परीक्षण ईमेल भेजें कि यह काम करता है।',
    'no_user_title'                    => 'कोई प्रमाणित उपयोगकर्ता नहीं।',
    'mailer_dry_warning_title'         => 'वर्तमान Mailer ":mailer" है — कोई वास्तविक ईमेल नहीं भेजा जाएगा।',
    'mailer_dry_warning_body'          => 'परीक्षण से पहले SMTP पर स्विच करें और सहेजें।',
    'test_email_sent_title'            => ':recipient को परीक्षण ईमेल भेजा गया',
    'test_email_sent_body'             => 'अगले मिनट में इनबॉक्स (और स्पैम) देखें।',
    'test_send_failed_title'           => 'भेजना विफल: :error',
    'test_send_failed_body'            => 'होस्ट, पोर्ट, एन्क्रिप्शन और क्रेडेंशियल की दोबारा जाँच करें।',

    // ----- परीक्षण ईमेल बॉडी -----
    'test_email_body_intro'            => 'यह आपके :app स्क्रिप्ट सेटिंग्स पेज से एक परीक्षण ईमेल है।',
    'test_email_body_confirmation'     => 'यदि आपको यह प्राप्त हुआ है, तो आपके SMTP क्रेडेंशियल काम कर रहे हैं।',
    'test_email_body_sent_at'          => 'भेजा गया: :timestamp UTC',
    'test_email_subject'               => '[:app] SMTP परीक्षण ईमेल',

    // ----- हेडर क्रियाएँ -----
    'action_send_test'                 => 'परीक्षण ईमेल भेजें',
    'action_send_test_tooltip'         => 'फ़ॉर्म पर वर्तमान में मौजूद मानों का उपयोग करता है — पहले परीक्षण करें, यदि काम करे तो सहेजें।',
    'action_send_test_recipient_label' => 'परीक्षण ईमेल यहाँ भेजें',
    'action_send_test_recipient_helper' => 'कोई भी पता — क्रेडेंशियल को कमिट करने से पहले अपने प्रोडक्शन मेलबॉक्स को वितरण सत्यापित करने के लिए ओवरराइड करें।',
    'action_send_test_modal_submit'    => 'परीक्षण भेजें',
    'action_save_settings'             => 'सेटिंग्स सहेजें',

    // ----- हीरो -----
    'page_hero_title'                  => 'आपके LeadHub डिप्लॉयमेंट के लिए वैश्विक डिफ़ॉल्ट',
    'hero_eyebrow'                     => 'स्क्रिप्ट स्वामी सेटिंग्स',
    'hero_subtitle'                    => 'अपने बिलिंग डैशबोर्ड के लिए रिपोर्टिंग मुद्रा, अपना डिफ़ॉल्ट टाइमज़ोन, और वह भाषा चुनें जिसके साथ हर नया वर्कस्पेस शुरू होता है।',

    // ----- शेड्यूलर / cron व्याख्याकर्ता -----
    'cron_details_summary'             => 'शेड्यूलर क्या चलाता है?',
    'cron_desc'                        => 'अपने रिमोट शेड्यूलर को हर मिनट इस URL पर GET करने के लिए कॉन्फ़िगर करें:',

    // ─── Cron अनुभाग शीर्षक + परिचय ───
    'cron_section_title'               => 'पृष्ठभूमि कार्य शेड्यूलर (cron)',
    'cron_section_desc_html'           => 'LeadHub हर मिनट कई पृष्ठभूमि कार्य चलाता है — ईमेल भेजना, स्वचालन, अनुसूचित रिपोर्ट, लीड स्कोरिंग, कार्य अनुस्मारक, IMAP इनबॉक्स पोलिंग, सदस्यता जीवनचक्र जाँच, और रात्रिकालीन बैकअप। ये सभी एक cron ट्रिगर पर निर्भर करते हैं जो प्रति मिनट एक बार <code class="ss-chip">cron.php</code> को हिट करता है या <code class="ss-chip">artisan schedule:run</code> चलाता है। वह विकल्प चुनें जिसका आपका सर्वर समर्थन करता है।',

    // ─── शेड्यूलर विवरण सूची (अंतराल → विवरण) ───
    'cron_list_every_5_min_label'      => 'हर 5 मिनट',
    'cron_list_every_5_min_desc'       => 'IMAP इनबॉक्स पोलिंग, कार्य अनुस्मारक',
    'cron_list_every_15_min_label'     => 'हर 15 मिनट',
    'cron_list_every_15_min_desc'      => 'ईमेल अनुक्रम चरण डिस्पैचर',
    'cron_list_every_hour_label'       => 'हर घंटे',
    'cron_list_every_hour_desc'        => 'गतिविधि-रहित स्वचालन, अनुसूचित रिपोर्ट वितरण, सहेजे गए-फ़िल्टर अलर्ट',
    'cron_list_every_6_hours_label'    => 'हर 6 घंटे',
    'cron_list_every_6_hours_desc'     => 'सदस्यता जीवनचक्र (ट्रायल समाप्ति, अनुग्रह-अवधि अनुस्मारक)',
    'cron_list_daily_02_label'         => 'दैनिक 02:00 पर',
    'cron_list_daily_02_desc'          => 'Tenant डेटाबेस बैकअप (जब सक्षम हो)',
    'cron_list_daily_09_label'         => 'दैनिक 09:00 पर',
    'cron_list_daily_09_desc'          => 'LeadHub अद्यतन-जाँच पिंग',
    'cron_list_daily_user_label'       => 'उपयोगकर्ता द्वारा कॉन्फ़िगर किए गए घंटे पर दैनिक',
    'cron_list_daily_user_desc'        => 'सूचनाएँ डाइजेस्ट',

    // ─── Cron विकल्प A: शेयर्ड होस्टिंग ───
    'cron_option_a_label'              => 'शेयर्ड होस्टिंग (cPanel / Plesk / DirectAdmin)',
    'cron_option_a_tag'                => 'सबसे आसान',
    'cron_option_a_desc_html'          => 'अपने होस्टिंग पैनल में, <em>Cron Jobs</em> खोलें और हर मिनट चलने वाला एक कार्य जोड़ें:',
    'cron_option_a_hint_html'          => 'कुछ शेयर्ड होस्ट <code class="ss-chip-light">exec()</code> को ब्लॉक करते हैं — यदि उपरोक्त चुपचाप विफल हो जाए तो इसके बजाय विकल्प B का उपयोग करें।',

    // ─── Cron विकल्प B: URL-आधारित cron ───
    'cron_option_b_label'              => 'URL-आधारित cron (cron-job.org, EasyCron, UptimeRobot)',
    'cron_option_b_secret_hint_html'   => '<code class="ss-chip-light">secret=</code> पैरामीटर को <code class="ss-chip-light">.env</code> में आपके <code class="ss-chip-light">CRON_SECRET</code> से मेल खाना चाहिए — यह किसी और को आपके शेड्यूलर को ट्रिगर करने से रोकता है।',
    'cron_option_b_warn_html'          => '<strong>ध्यान दें:</strong> <code class="ss-chip-warn">CRON_SECRET</code> सेट नहीं है — आपका URL ट्रिगर इंटरनेट पर खुला है। <code class="ss-chip-warn">.env</code> में 32-वर्ण का यादृच्छिक सीक्रेट जोड़ें और ऊपर दिए गए URL में <code class="ss-chip-warn">?secret=…</code> जोड़ें।',

    // ─── Cron विकल्प C: VPS / डेडिकेटेड (मूल Laravel शेड्यूलर) ───
    'cron_option_c_label'              => 'VPS / डेडिकेटेड (मूल Laravel शेड्यूलर)',
    'cron_option_c_tag'                => 'VPS के लिए अनुशंसित',
    'cron_option_c_desc_html'          => 'अपने वेब उपयोगकर्ता के रूप में SSH करें और <code class="ss-chip-light">crontab -e</code> चलाएँ, फिर जोड़ें:',
    'cron_option_c_hint_html'          => 'यह <code class="ss-chip-light">cron.php</code> को बायपास करता है और Laravel के मूल शेड्यूलर का उपयोग करता है — सबसे कम ओवरहेड, Redis क्यू वाले VPS या डेडिकेटेड सर्वर के लिए सर्वोत्तम।',

    // ─── Cron कॉपी बटन + सत्यापन ───
    'cron_copy'                        => 'कॉपी',
    'cron_copied'                      => 'कॉपी किया गया',
    'cron_verify_text_html'            => '<strong>सत्यापित करें कि यह काम कर रहा है:</strong> अपना cron सहेजने के बाद 2 मिनट प्रतीक्षा करें, फिर "क्यू और वर्कर्स" पेज (किसी भी Tenant वर्कस्पेस के अंदर) देखें — यदि कार्य प्रवाहित हो रहे हैं, तो कनेक्शन एक हालिया टाइमस्टैम्प दिखाएगा। आप प्रत्येक पंजीकृत कार्य देखने के लिए SSH से <code class="ss-chip-success">php artisan schedule:list</code> भी चला सकते हैं।',
];
