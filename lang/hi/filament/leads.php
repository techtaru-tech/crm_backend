<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | LeadResource अनुवाद स्ट्रिंग्स (hi)
 |--------------------------------------------------------------------------
 |
 | LeadResource और इसके Pages + Relation Managers द्वारा उपयोग की जाने वाली
 | सभी उपयोगकर्ता-दृश्य स्ट्रिंग्स। कुंजियाँ snake_case अंग्रेज़ी में हैं और
 | lang/en/filament/leads.php के समान रखी गई हैं; केवल मान अनुवादित होते हैं।
 | __('filament/leads.<key>') के माध्यम से उपयोग किया जाता है।
 |
 */

return [
    // ─── नेविगेशन ───
    'nav_label'                     => 'सभी लीड',

    // ─── मॉडल लेबल ───
    'model_label'                   => 'लीड',
    'plural_model_label'            => 'लीड',

    // ─── वैश्विक खोज ───
    'search_result_fallback'        => 'लीड #:id',
    'search_result_email'           => 'ईमेल',
    'search_result_source'          => 'स्रोत',
    'search_result_status'          => 'स्थिति',
    'search_result_score'           => 'स्कोर',
    'search_result_score_value'     => ':score अंक',

    // ─── फॉर्म: संपर्क जानकारी ───
    'first_name'                    => 'पहला नाम',
    'last_name'                     => 'अंतिम नाम',
    'email'                         => 'ईमेल',
    'phone'                         => 'फ़ोन',
    'company'                       => 'कंपनी',
    'company_name'                  => 'कंपनी का नाम',
    'domain'                        => 'डोमेन',
    'industry'                      => 'उद्योग',

    // ─── फॉर्म: लीड विवरण ───
    'source'                        => 'स्रोत',
    'status'                        => 'स्थिति',
    'assigned_to'                   => 'सौंपा गया',
    'pipeline'                      => 'पाइपलाइन',
    'stage'                         => 'चरण',
    'score'                         => 'स्कोर',
    'starred'                       => 'तारांकित',
    'lead_notes'                    => 'टिप्पणियाँ',

    // ─── फॉर्म: सौदा ───
    'deal_value'                    => 'सौदे का मूल्य',
    'currency'                      => 'मुद्रा',
    'expected_close_date'           => 'अपेक्षित समापन तिथि',
    'lost_reason'                   => 'हानि का कारण',

    // ─── फॉर्म: अतिरिक्त जानकारी ───
    'source_reference_id'           => 'स्रोत संदर्भ ID',
    'last_contacted'                => 'अंतिम संपर्क',

    // ─── फॉर्म: श्रेय (एट्रिब्यूशन) ───
    'attribution_description'       => 'इस लीड को बनाने वाले फॉर्म या विजेट से प्राप्त किया गया।',
    'utm_source'                    => 'UTM Source',
    'utm_medium'                    => 'UTM Medium',
    'utm_campaign'                  => 'UTM Campaign',
    'utm_content'                   => 'UTM Content',
    'utm_term'                      => 'UTM Term',
    'landing_page'                  => 'लैंडिंग पृष्ठ',
    'referrer'                      => 'रेफ़रर',
    'custom_fields_description'     => 'किरायेदार-निर्धारित फ़ील्ड। सेटिंग्स → कस्टम फ़ील्ड में कॉन्फ़िगर करें।',

    // ─── तालिका कॉलम ───
    'name'                          => 'नाम',
    'expected_close'                => 'अपेक्षित समापन',
    'tags'                          => 'टैग',
    'assigned'                      => 'सौंपा गया',
    'dup'                           => 'डुप्लीकेट',
    'waiting_on'                    => 'प्रतीक्षा में',
    'created_at'                    => 'बनाया गया',

    // ─── फ़िल्टर ───
    'filter_label_source'           => 'स्रोत',
    'filter_label_status'           => 'स्थिति',
    'tag'                           => 'टैग',
    'starred_only'                  => 'केवल तारांकित',
    'not_starred'                   => 'गैर-तारांकित',
    'duplicates_only'               => 'केवल डुप्लीकेट',
    'waiting_us'                    => 'हम (उन्होंने उत्तर दिया)',
    'waiting_them'                  => 'वे (हमने संपर्क किया)',
    'waiting_new'                   => 'नया (कोई संपर्क नहीं)',
    'created_from'                  => 'से बनाया गया',
    'created_until'                 => 'तक बनाया गया',
    'min_score'                     => 'न्यूनतम स्कोर',
    'min_deal_value'                => 'न्यूनतम सौदा मूल्य',
    'max_deal_value'                => 'अधिकतम सौदा मूल्य',

    // ─── पंक्ति क्रियाएँ ───
    'tooltip_unstar'                => 'तारांकन हटाएँ',
    'tooltip_star_this_lead'        => 'इस लीड को तारांकित करें',
    'tooltip_view_lead'             => 'लीड देखें',
    'tooltip_edit'                  => 'संपादित करें',
    'tooltip_delete'                => 'हटाएँ',
    'view_detail_action_label'      => 'विवरण देखें',

    // ─── बल्क क्रियाएँ ───
    'bulk_assign_agent'             => 'एजेंट सौंपें',
    'bulk_assign_to'                => 'सौंपें',
    'bulk_leads_assigned'           => 'लीड सौंपी गईं।',
    'bulk_change_status'            => 'स्थिति बदलें',
    'bulk_status_updated'           => 'स्थिति अद्यतन की गई।',
    'bulk_add_tag'                  => 'टैग जोड़ें',
    'bulk_tag_added'                => 'चयनित लीड में टैग जोड़ा गया।',
    'bulk_remove_tag'               => 'टैग हटाएँ',
    'bulk_tag_removed'              => 'चयनित लीड से टैग हटाया गया।',
    'bulk_move_to_stage'            => 'चरण में स्थानांतरित करें',
    'bulk_leads_moved'              => 'लीड को चरण में स्थानांतरित किया गया।',
    'bulk_export_csv'               => 'चयनित निर्यात करें (CSV)',
    'bulk_export_queued'            => 'निर्यात कतार में जोड़ा गया — डाउनलोड लिंक शीघ्र ही आएगा।',
    'bulk_run_automation'           => 'स्वचालन चलाएँ',
    'bulk_select_automation'        => 'स्वचालन चुनें',
    'bulk_enroll_in_sequence'       => 'अनुक्रम में नामांकित करें',
    'bulk_sequence'                 => 'अनुक्रम',
    'bulk_automation_queued'        => ':count लीड के लिए स्वचालन कतार में जोड़ा गया।',
    'bulk_enrolled_skipped'         => ':added लीड नामांकित। :skipped पहले से नामांकित को छोड़ा गया।',

    // ─── रिक्त स्थिति ───
    'empty_heading'                 => 'अभी तक कोई लीड नहीं',
    'empty_description'             => 'CSV आयात करके, एक एम्बेड करने योग्य फॉर्म बनाकर, या मैन्युअल रूप से जोड़कर अपनी पहली लीड कैप्चर करें।',
    'empty_add_lead'                => 'लीड जोड़ें',
    'empty_import_csv'              => 'CSV से आयात करें',
    'empty_build_form'              => 'कैप्चर फॉर्म बनाएँ',

    // ─── व्यू पृष्ठ: हेडर क्रियाएँ ───
    'add_line_item'                 => 'पंक्ति आइटम जोड़ें',
    'product'                       => 'उत्पाद',
    'item_name'                     => 'आइटम का नाम',
    'unit_price'                    => 'इकाई मूल्य',
    'discount_percent'              => 'छूट %',
    'line_item_added'               => 'पंक्ति आइटम जोड़ा गया।',

    'create_task'                   => 'कार्य बनाएँ',
    'task_title'                    => 'कार्य का शीर्षक',
    'description'                   => 'विवरण',
    'due_at'                        => 'देय तिथि',
    'priority'                      => 'प्राथमिकता',
    'reminder_at'                   => 'अनुस्मारक समय',
    'reminder_help'                 => 'डिफ़ॉल्ट रूप से देय समय से एक घंटे पहले।',
    'task_created'                  => 'कार्य बनाया गया।',

    'send_email'                    => 'ईमेल भेजें',
    'load_template'                 => 'टेम्पलेट से लोड करें',
    'load_template_help'            => 'विषय और मुख्य भाग भरने के लिए एक सहेजा गया ईमेल टेम्पलेट चुनें। भेजने से पहले भी आप उन्हें संपादित कर सकते हैं।',
    'attachments'                   => 'अनुलग्नक',
    'no_email_address'              => 'लीड के पास कोई ईमेल पता नहीं है।',
    'email_log_mode_title'          => 'ईमेल कतार में है, लेकिन आउटबाउंड मेल लॉग मोड में है।',
    'email_log_mode_body'           => 'SMTP कॉन्फ़िगर नहीं किया गया — संदेश केवल storage/logs/laravel.log में लिखा जाएगा। SMTP कॉन्फ़िगर करने और वितरण शुरू करने के लिए सेटिंग्स → ईमेल पर जाएँ।',
    'email_queued'                  => 'ईमेल वितरण के लिए कतार में जोड़ा गया।',

    'call_lead'                     => 'कॉल करें',
    'call_modal_heading'            => 'कॉल प्रारंभ करें',
    'call_modal_description'        => 'क्या :phone पर कॉल प्रारंभ करनी है? पहले आपका फ़ोन बजेगा, फिर लीड से जुड़ेगा।',
    'call_now'                      => 'अभी कॉल करें',
    'call_no_phone'                 => 'आपके उपयोगकर्ता प्रोफ़ाइल में कोई फ़ोन नंबर नहीं है — कॉल जोड़ी नहीं जा सकती।',
    'call_failed_to_start'          => 'कॉल प्रारंभ नहीं हुई — मैसेजिंग → वॉइस सेटिंग्स देखें।',
    'call_initiated'                => 'कॉल प्रारंभ की गई — अपना फ़ोन उठाएँ।',
    'call_failed_prefix'            => 'कॉल विफल: ',

    'send_message'                  => 'संदेश भेजें',
    'conversation_count'            => 'बातचीत (:count)',
    'channel'                       => 'चैनल',
    'message'                       => 'संदेश',
    'no_phone_number'               => 'लीड के पास कोई फ़ोन नंबर नहीं है।',
    'message_queued'                => 'संदेश वितरण के लिए कतार में जोड़ा गया।',

    'log_call'                      => 'कॉल लॉग करें',
    'inbound'                       => 'इनबाउंड',
    'outbound'                      => 'आउटबाउंड',
    'outcome_connected'             => 'कनेक्ट किया गया',
    'outcome_voicemail'             => 'वॉइसमेल छोड़ा',
    'outcome_no_answer'             => 'कोई उत्तर नहीं',
    'outcome_not_interested'        => 'रुचि नहीं',
    'outcome_callback'              => 'कॉलबैक का अनुरोध',
    'call_logged'                   => 'कॉल लॉग की गई।',
    'duration'                      => 'अवधि',
    'duration_minutes_suffix'       => 'मिनट',
    'outcome'                       => 'परिणाम',

    'add_note'                      => 'टिप्पणी जोड़ें',
    'mention_label'                 => 'टीम सदस्यों का उल्लेख करें (खोजने के लिए @ टाइप करें)',
    'mention_placeholder'           => 'जैसे @jane',
    'mention_help'                  => 'एकाधिक उल्लेख अल्पविराम से अलग करें।',
    'note'                          => 'टिप्पणी',
    'note_body_help'                => 'टीम सदस्यों का उल्लेख करने के लिए @name का उपयोग करें।',
    'note_added'                    => 'टिप्पणी जोड़ी गई।',

    'move_stage'                    => 'चरण बदलें',
    'lead_moved_to_stage'           => 'लीड को नए चरण में स्थानांतरित किया गया।',

    'assign'                        => 'सौंपें',
    'lead_assigned'                 => 'लीड सौंपी गई।',

    'enroll_in_sequence'            => 'अनुक्रम में नामांकित करें',
    'sequence'                      => 'अनुक्रम',
    'already_enrolled'              => 'लीड पहले से ही इस अनुक्रम में नामांकित है।',
    'lead_enrolled'                 => 'लीड अनुक्रम में नामांकित की गई।',

    'apply_tags'                    => 'टैग',
    'tags_updated'                  => 'टैग अद्यतन किए गए।',

    'star'                          => 'तारांकित करें',
    'unstar'                        => 'तारांकन हटाएँ',

    'more'                          => 'अधिक',

    'create_quote'                  => 'कोटेशन बनाएँ',
    'create_invoice'                => 'चालान बनाएँ',

    'enrich_with_ai'                => 'AI से समृद्ध करें',
    're_enrich_with_ai'             => 'AI से फिर से समृद्ध करें',
    're_enrich_modal_heading'       => 'लीड को फिर से समृद्ध करें',
    're_enrich_modal_description'   => 'यह लीड पहले से ही समृद्ध की जा चुकी है। समृद्धि फिर से चलाने से कंपनी, उद्योग और स्थान डेटा अधिलिखित होगा।',
    'enrich_no_email'               => 'लीड के पास कोई ईमेल नहीं है — ईमेल पते के बिना समृद्ध नहीं किया जा सकता।',
    'enrich_queued'                 => 'समृद्धि कतार में जोड़ी गई। डेटा शीघ्र ही दिखाई देगा।',

    'ai_draft_email'                => 'AI ड्राफ़्ट ईमेल',
    'email_intent'                  => 'ईमेल का उद्देश्य',
    'intent_introduction'           => 'प्रारंभिक परिचय',
    'intent_follow_up'              => 'अनुवर्ती',
    'intent_proposal'               => 'प्रस्ताव / अगले चरण',
    'intent_re_engage'              => 'ठंडी लीड को पुनः सक्रिय करें',
    'intent_closing'                => 'समापन / पुष्टिकरण',
    'additional_context'            => 'अतिरिक्त संदर्भ (वैकल्पिक)',
    'additional_context_placeholder'=> 'जैसे उन्होंने कल हमारा मूल्य निर्धारण पृष्ठ देखा...',
    'ai_draft_failed'               => 'ड्राफ़्ट उत्पन्न नहीं किया जा सका। सुनिश्चित करें कि OpenAI API कुंजी कॉन्फ़िगर है।',
    'ai_draft_generated'            => 'ड्राफ़्ट उत्पन्न किया गया',
    'subject_label'                 => 'विषय',

    'merge_lead'                    => 'लीड मर्ज करें',
    'merge_into_label'              => 'इस लीड को इसमें मर्ज करें (प्राथमिक के रूप में रखें)',
    'merge_into_help'               => 'प्राथमिक लीड रखी जाएगी। यह लीड संग्रहीत की जाएगी।',
    'merge_primary_not_found'       => 'प्राथमिक लीड नहीं मिली।',
    'merge_success'                 => 'लीड मर्ज की गईं। प्राथमिक लीड पर पुनर्निर्देशित किया जा रहा है…',
    'merge_option_format'           => ':name — :email (:field पर मिलान)',
    'no_email'                      => 'कोई ईमेल नहीं',

    'export_data'                   => 'डेटा निर्यात करें',

    'send_portal_link'              => 'पोर्टल लिंक भेजें',
    'portal_link_heading'           => 'पोर्टल लॉगिन लिंक भेजें',
    'portal_link_description'       => 'लीड के ईमेल पर 30-मिनट का जादुई लिंक भेजता है ताकि वे ग्राहक पोर्टल के माध्यम से अपने सौदे की स्थिति देख सकें और दस्तावेज़ अपलोड कर सकें।',
    'portal_link_sent_prefix'       => 'पोर्टल लिंक भेजा गया: ',
    'portal_link_failed_prefix'     => 'भेजने में विफल: ',

    'gdpr_anonymize'                => 'GDPR गुमनाम करें',
    'gdpr_anonymize_heading'        => 'लीड को गुमनाम करें',
    'gdpr_anonymize_description'    => 'सभी व्यक्तिगत पहचान योग्य जानकारी (PII) को प्लेसहोल्डर से बदलता है, लेकिन लीड रिकॉर्ड और समग्र आँकड़े (सौदा मूल्य, स्थिति, पाइपलाइन, स्रोत, टैग) बनाए रखता है। अनुलग्नक, ईमेल, संदेश, टिप्पणियाँ और पृष्ठ दृश्य हटा दिए जाते हैं। गतिविधि पंक्तियाँ मेटाडेटा हटाकर रखी जाती हैं।',
    'gdpr_anonymize_confirm'        => 'हाँ, गुमनाम करें',
    'gdpr_anonymized_success'       => 'लीड गुमनाम की गई। समग्र आँकड़े संरक्षित।',

    'gdpr_erase'                    => 'GDPR मिटाएँ',
    'gdpr_erase_heading'            => 'GDPR के तहत लीड मिटाएँ',
    'gdpr_erase_description'        => 'इस लीड के हर निशान को स्थायी रूप से हटाता है — गतिविधियाँ, टिप्पणियाँ, कार्य, अनुलग्नक, ईमेल, संदेश, वेब-पृष्ठ दृश्य, सौदा पंक्ति आइटम और ईमेल-अनुक्रम नामांकन। इसे पूर्ववत नहीं किया जा सकता।',
    'gdpr_erase_confirm'            => 'हाँ, स्थायी रूप से मिटाएँ',
    'gdpr_erase_success'            => 'लीड डेटा स्थायी रूप से मिटाया गया।',

    'attachment_uploaded'           => 'अनुलग्नक अपलोड किया गया।',
    'attachment_deleted'            => 'अनुलग्नक हटाया गया।',
    'line_item_removed'             => 'पंक्ति आइटम हटाया गया।',

    // ─── संपादन पृष्ठ ───
    'full_detail_view'              => 'पूर्ण विवरण दृश्य',

    // ─── सूची पृष्ठ ───
    'kanban_board'                  => 'कानबन बोर्ड',
    'save_filters'                  => 'फ़िल्टर सहेजें',
    'view_name'                     => 'दृश्य का नाम',
    'view_name_placeholder'         => 'जैसे इस सप्ताह की मेरी हॉट लीड',
    'email_alerts'                  => 'जब नई लीड मेल खाएँ तो मुझे ईमेल भेजें',
    'email_alerts_help'             => 'प्रति घंटा जाँच — जब नई लीड इस फ़िल्टर से मेल खाएँ तो आपको एक डाइजेस्ट मिलेगा।',
    'share_with_team'               => 'टीम के साथ साझा करें',
    'share_with_team_help'          => 'आपके कार्यक्षेत्र के सभी सदस्य इस दृश्य को लोड कर सकते हैं।',
    'filter_view_saved'             => 'फ़िल्टर दृश्य सहेजा गया',
    'filter_view_saved_as'          => '«:name» के रूप में सहेजा गया।',
    'filter_view_loaded'            => '«:name» लोड किया गया',

    'saved_views'                   => 'सहेजे गए दृश्य',
    'no_saved_views_yet'            => 'अभी तक कोई सहेजा गया दृश्य नहीं। फ़िल्टर लागू करें और एक बनाने के लिए «फ़िल्टर सहेजें» पर क्लिक करें।',
    'select_saved_view'             => 'एक सहेजा गया दृश्य चुनें',
    'saved_view_not_found'          => 'सहेजा गया दृश्य नहीं मिला।',
    'placeholder_empty_label'       => '',

    'delete_view'                   => 'दृश्य हटाएँ',
    'no_saved_views_to_delete'      => 'हटाने के लिए कोई सहेजा गया दृश्य नहीं।',
    'select_view_to_delete'         => 'हटाने के लिए दृश्य चुनें',
    'saved_view_deleted'            => 'सहेजा गया दृश्य हटाया गया।',

    'export_current_filters'        => 'निर्यात (वर्तमान फ़िल्टर)',
    'export_queued_with_link'       => 'निर्यात कतार में जोड़ा गया — आपको शीघ्र ही एक डाउनलोड लिंक मिलेगा।',

    'import_from_crm'               => 'CRM से आयात करें',
    'import_modal_heading'          => 'किसी अन्य CRM से लीड आयात करें',
    'import_modal_description'      => 'HubSpot, Pipedrive, Salesforce, या किसी भी सामान्य CRM से निर्यात किया गया CSV अपलोड करें। «स्वतः-पहचान» पर सेट होने पर विक्रेता का स्वतः पता लगाता है।',
    'source_crm'                    => 'स्रोत CRM',
    'auto_detect_option'            => 'CSV हेडर से स्वतः-पहचानें',
    'source_crm_help'               => 'स्वतः-पहचान हेडर पंक्ति की जाँच करती है। यदि स्वतः-पहचान आपके प्रारूप को नहीं पहचानती तो एक विशिष्ट विक्रेता चुनें।',
    'csv_file'                      => 'CSV फ़ाइल',
    'csv_file_help'                 => '20 MB तक। पहली पंक्ति में कॉलम हेडर होने चाहिए।',
    'no_workspace_context'          => 'कोई कार्यक्षेत्र संदर्भ नहीं — कृपया पुनः लोड करें।',
    'no_file_uploaded'              => 'कोई फ़ाइल अपलोड नहीं की गई।',
    'csv_import_complete'           => 'CSV आयात पूर्ण',

    // ─── CSV आयात सूचना मुख्य भाग पंक्तियाँ ───
    'import_body_imported_count'    => ':vendor से :count लीड आयात की गईं।',
    'import_body_skipped_count'     => ':count पंक्तियाँ छोड़ी गईं (कोई ईमेल या फ़ोन नहीं)।',
    'import_body_batch_errors'      => ':count बैच त्रुटि(याँ) — लॉग देखें।',

    // ─── रिलेशन: ईमेल ───
    'emails_title'                  => 'ईमेल',
    'from'                          => 'से',
    'body_text'                     => 'मुख्य भाग (पाठ)',
    'subject'                       => 'विषय',
    'sent'                          => 'भेजा गया',
    'opened'                        => 'खोला गया',
    'clicked'                       => 'क्लिक किया गया',
    'received'                      => 'प्राप्त हुआ',
    'direction'                     => 'दिशा',
    'email_modal_default'           => 'ईमेल',
    'body'                          => 'मुख्य भाग',

    // ─── रिलेशन: संदेश ───
    'messages_title'                => 'संदेश',
    'channel_whatsapp'              => 'WhatsApp',
    'channel_sms'                   => 'SMS',
    'channel_telegram'              => 'Telegram',
    'channel_viber'                 => 'Viber',
    'status_sent'                   => 'भेजा गया',
    'status_delivered'              => 'पहुँचाया गया',
    'status_read'                   => 'पढ़ा गया',
    'status_failed'                 => 'विफल',
    'media_url'                     => 'मीडिया URL',
    'message_modal'                 => 'संदेश',
    'message_status'                => 'स्थिति',
    'sent_at'                       => 'भेजा गया समय',

    // ─── रिलेशन: कार्य ───
    'tasks_title'                   => 'कार्य',
    'due'                           => 'देय',
    'done'                          => 'पूर्ण',
    'mark_complete'                 => 'पूर्ण के रूप में चिह्नित करें',
    'mark_incomplete'               => 'अधूरा के रूप में चिह्नित करें',
    'reminder_help_short'           => 'डिफ़ॉल्ट रूप से देय समय से एक घंटे पहले।',

    // ─── रिलेशन: सौदा पंक्ति आइटम ───
    'line_items_title'              => 'पंक्ति आइटम',
    'discount'                      => 'छूट',
    'quantity'                      => 'मात्रा',
    'total'                         => 'कुल',

    // ─── रिलेशन: अनुक्रम नामांकन ───
    'email_sequences_title'         => 'ईमेल अनुक्रम',
    'step'                          => 'चरण',
    'next_send'                     => 'अगला प्रेषण',
    'unenroll'                      => 'नामांकन रद्द करें',
    'lead_unenrolled'               => 'लीड का नामांकन रद्द किया गया।',
    'sequence_status'               => 'स्थिति',
    'enrolled_at'                   => 'नामांकित किया गया',
    'unenroll_reason_manual'        => 'मैन्युअल रूप से नामांकन रद्द',
    // Wave BB: LeadObserver (won/converted),
    // ProcessEmailSequences (अनुपलब्ध डेटा / कोई ईमेल नहीं) और LeadEmail
    // (आवक उत्तर) द्वारा लिखे गए स्थायी कारण। लिखने के समय अनुवादित किए
    // जाते हैं ताकि कॉलम सम्मिलन के क्षण की सक्रिय लोकेल से मेल खाए।
    'unenroll_reason_converted'     => 'लीड को रूपांतरित के रूप में चिह्नित किया गया',
    'unenroll_reason_won'           => 'लीड को जीता हुआ के रूप में चिह्नित किया गया',
    'unenroll_reason_missing_data'  => 'अनुक्रम या लीड अनुपलब्ध',
    'unenroll_reason_no_email'      => 'लीड के पास कोई ईमेल पता नहीं है',
    'unenroll_reason_replied'       => 'लीड ने उत्तर दिया',
    // Wave BB: LeadActivity.metadata.i18n_params में LeadObserver द्वारा
    // लिखे गए प्लेसहोल्डर जब पुराना/नया पाइपलाइन चरण अनुपलब्ध हो
    // (stage_none_placeholder) या कोई असाइनी सेट न हो
    // (unassigned_placeholder)।
    'stage_none_placeholder'        => 'कोई नहीं',
    'unassigned_placeholder'        => 'असौंपा',

    // ─── रिलेशन: पृष्ठ दृश्य ───
    'web_activity_title'            => 'वेब गतिविधि',
    'viewed'                        => 'देखा गया',
    'page_path'                     => 'पथ',
    'page_title'                    => 'शीर्षक',
    'page_utm_source'               => 'UTM Source',

    // ─── व्यू पृष्ठ: उपशीर्षक और wire:confirm स्ट्रिंग्स ───
    'view_quotes_heading'           => 'कोटेशन',
    'view_invoices_heading'         => 'चालान',
    'confirm_delete_attachment'     => 'क्या आप वाकई इस अनुलग्नक को हटाना चाहते हैं?',
    'confirm_remove_line_item'      => 'क्या यह पंक्ति आइटम हटाना है?',

    // ─── व्यू पृष्ठ: Blade दृश्य लेबल और अनुभाग शीर्षक ───
    'section_contact_info'          => 'संपर्क जानकारी',
    'section_attachments'           => 'अनुलग्नक',
    'section_line_items'            => 'पंक्ति आइटम',
    'section_quotes_invoices'       => 'कोटेशन और चालान',
    'section_internal_notes'        => 'आंतरिक टिप्पणियाँ',
    'section_call_history'          => 'कॉल इतिहास',
    'section_web_activity'          => 'वेब गतिविधि',
    'section_activity_timeline'     => 'गतिविधि समयरेखा',
    'section_custom_fields'         => 'कस्टम फ़ील्ड',
    'section_email_sequences'       => 'ईमेल अनुक्रम',
    'section_ai_coaching'           => 'AI मार्गदर्शन',
    'section_conversations'         => 'बातचीत',
    'view_name_label'               => 'नाम',
    'view_email_label'              => 'ईमेल',
    'view_phone_label'              => 'फ़ोन',
    'view_source_label'             => 'स्रोत',
    'view_status_label'             => 'स्थिति',
    'view_lead_score_label'         => 'लीड स्कोर',
    'view_pts_unit'                 => '/ 100 अंक',
    'view_no_name'                  => '(कोई नाम नहीं)',
    'view_dash'                     => '—',
    'view_why_this_score'           => 'यह स्कोर क्यों?',
    'view_default_rule_name'        => 'नियम',
    'view_pts_suffix'               => 'अंक',
    'view_assigned_to_label'        => 'सौंपा गया',
    'view_pipeline_stage_label'     => 'पाइपलाइन चरण',
    'view_tags_label'               => 'टैग',
    'view_company_label'            => 'कंपनी',
    'view_job_title_label'          => 'पद',
    'view_industry_size_label'      => 'उद्योग / आकार',
    'view_employees_suffix'         => 'कर्मचारी',
    'view_country_label'            => 'देश',
    'view_linkedin_label'           => 'LinkedIn',
    'view_linkedin_view_profile'    => 'प्रोफ़ाइल देखें →',
    'view_ai_enriched_label'        => 'AI से समृद्ध',
    'view_created_label'            => 'बनाया गया',
    'view_inbox_in'                 => 'आवक',
    'view_inbox_out'                => 'जावक',
    'view_inbox_status_prefix'      => 'स्थिति:',

    // ─── लीड दृश्य: एकीकृत इनबॉक्स चैनल लेबल (Pass 22) ───
    // resources/views/filament/resources/leads/view.blade.php द्वारा मौजूदा
    // channel_* कुंजियों (whatsapp, sms, telegram, viber) के साथ उपयोग किया जाता है।
    // ये उन ईमेल और वेबचैट मानों को कवर करते हैं जो इनबॉक्स द्वारा
    // LeadEmail/LeadMessage पंक्तियों को मर्ज करते समय उत्पन्न होते हैं।
    'channel_email'                 => 'ईमेल',
    'channel_webchat'               => 'वेब चैट',

    // ─── लीड दृश्य: एकीकृत इनबॉक्स स्थिति पिल (Pass 22) ───
    // leads/view.blade.php में इनलाइन गणना की गई इनबॉक्स-स्तरीय स्थिति झंडों का
    // मानचित्र (opened/clicked/bounced/sent आदि) ताकि पिल पाठ सक्रिय लोकेल का
    // पालन करे, न कि कच्चे ucfirst() रूप का।
    'inbox_status_opened'           => 'खोला गया',
    'inbox_status_clicked'          => 'क्लिक किया गया',
    'inbox_status_bounced'          => 'बाउंस हुआ',
    'inbox_status_sent'             => 'भेजा गया',
    'inbox_status_delivered'        => 'पहुँचाया गया',
    'inbox_status_read'             => 'पढ़ा गया',
    'inbox_status_failed'           => 'विफल',
    'inbox_status_pending'          => 'लंबित',
    'view_open_conversation_view'   => 'पूर्ण बातचीत दृश्य खोलें',
    'view_step_label'               => 'चरण',
    'view_next_label'               => '· अगला :time',
    'view_completed_label'          => '· पूर्ण :time',
    'view_no_attachments_yet'       => 'अभी तक कोई अनुलग्नक नहीं।',
    'view_attachment_download_title' => 'डाउनलोड',
    'view_attachment_delete_title'  => 'हटाएँ',
    'view_uploading'                => 'अपलोड हो रहा है...',
    'view_save_attachments'         => 'अनुलग्नक सहेजें',
    'view_table_item'               => 'आइटम',
    'view_table_qty'                => 'मात्रा',
    'view_table_unit_price'         => 'इकाई मूल्य',
    'view_table_discount'           => 'छूट',
    'view_table_total'              => 'कुल',
    'view_table_sku_prefix'         => 'SKU:',
    'view_table_total_label'        => 'कुल:',
    'view_remove_item_title'        => 'हटाएँ',
    'view_no_line_items'            => 'अभी तक कोई पंक्ति आइटम नहीं। इस सौदे में उत्पाद या सेवाएँ जोड़ने के लिए ऊपर «पंक्ति आइटम जोड़ें» पर क्लिक करें।',
    'view_no_quotes_invoices'       => 'अभी तक कोई कोटेशन या चालान नहीं। हेडर में «अधिक → कोटेशन बनाएँ / चालान बनाएँ» क्रियाओं का उपयोग करें।',
    'view_invoice_due_label'        => 'देय :date',
    // लीड दृश्य साइडबार पर एम्बेडेड कोटेशन / चालान / कॉल सूचियों के लिए
    // स्थिति बैज। अज्ञात स्थितियों के लिए ucfirst() फ़ॉलबैक के साथ अनुवादक-
    // पहले लुकअप कस्टम एनम विस्तार को सुगम रखता है।
    'view_quote_status_draft'       => 'ड्राफ़्ट',
    'view_quote_status_sent'        => 'भेजा गया',
    'view_quote_status_accepted'    => 'स्वीकृत',
    'view_quote_status_declined'    => 'अस्वीकृत',
    'view_quote_status_expired'     => 'समाप्त',
    'view_quote_status_converted'   => 'रूपांतरित',
    'view_invoice_status_draft'     => 'ड्राफ़्ट',
    'view_invoice_status_sent'      => 'भेजा गया',
    'view_invoice_status_partial'   => 'आंशिक',
    'view_invoice_status_overdue'   => 'विलंबित',
    'view_invoice_status_paid'      => 'भुगतान किया गया',
    'view_invoice_status_cancelled' => 'रद्द किया गया',
    'view_invoice_status_refunded'  => 'धनवापसी की गई',
    'view_call_status_completed'    => 'पूर्ण',
    'view_call_status_failed'       => 'विफल',
    'view_call_status_canceled'     => 'रद्द',
    'view_call_status_no_answer'    => 'कोई उत्तर नहीं',
    'view_call_status_busy'         => 'व्यस्त',
    'view_note_system'              => 'सिस्टम',
    'view_note_mentioned_label'     => 'उल्लिखित:',
    'view_no_internal_notes'        => 'अभी तक कोई आंतरिक टिप्पणी नहीं।',
    'view_call_agent_default'       => 'एजेंट',
    'view_call_ai_summary'          => 'AI सारांश',
    'view_utm_prefix'               => 'utm:',
    'view_activity_by'              => ':name द्वारा',
    'view_no_activity_yet'          => 'अभी तक कोई गतिविधि दर्ज नहीं की गई।',
    'view_custom_yes'               => 'हाँ',
    'view_custom_no'                => 'नहीं',
    'view_media_attachment'         => '[मीडिया अनुलग्नक]',

    // ─── बातचीत «अंतिम उत्तर द्वारा» कॉलम (लीड तालिका) ──
    'conversation_last_by_us'       => 'हम',
    'conversation_last_by_them'     => 'वे',
    'conversation_last_by_new'      => 'नया',

    // ─── बैज फ़ॉलबैक लेबल (Wave A — Filament badge formatStateUsing)
    // ये कुंजियाँ LeadResource और इसके RelationManagers में TextColumn बैजों पर
    // formatStateUsing() कॉलबैक का समर्थन करती हैं। जहाँ कॉलम एक टाइप किए गए
    // एनम (जैसे App\Enums\LeadStatus) से मैप करता है, वहाँ एनम की label() विधि
    // प्राथमिकता लेती है और ये कुंजियाँ डेटाबेस के लीगेसी मानों के लिए कच्चे-
    // स्ट्रिंग फ़ॉलबैक के रूप में काम करती हैं जो कास्ट को बायपास करते हैं।

    // लीड स्थिति — enums/lead_status.php को मिरर करती है साथ ही H7 एनम
    // docblock द्वारा रखे गए लीगेसी 'converted' उपनाम को भी।
    'status_new'                    => 'नया',
    'status_contacted'              => 'संपर्क किया गया',
    'status_qualified'              => 'योग्य',
    'status_won'                    => 'जीता',
    'status_converted'              => 'रूपांतरित',
    'status_lost'                   => 'खोया',

    // दिशा बैज (Messages relation manager)। ब्रांड-तटस्थ।
    'direction_inbound'             => 'आवक',
    'direction_outbound'            => 'जावक',

    // ईमेल दिशा बैज — संदेश दिशा से अलग रखे गए ताकि अनुवादक चैनल के
    // अनुसार वाक्यांश को बिना युग्मन के अनुकूलित कर सकें।
    'email_direction_inbound'       => 'आवक',
    'email_direction_outbound'      => 'जावक',

    // संदेश स्थिति बैज — सामान्य 'status_*' से अलग कुंजी उपसर्ग ताकि
    // MessagesRelationManager के अनुवाद लीड स्थिति कुंजियों से स्वतंत्र रहें।
    'message_status_sent'           => 'भेजा गया',
    'message_status_delivered'      => 'पहुँचाया गया',
    'message_status_read'           => 'पढ़ा गया',
    'message_status_failed'         => 'विफल',

    // कार्य प्राथमिकता बैज (TasksRelationManager)।
    'task_priority_urgent'          => 'अत्यावश्यक',
    'task_priority_high'            => 'उच्च',
    'task_priority_normal'          => 'सामान्य',
    'task_priority_low'             => 'निम्न',

    // ईमेल-अनुक्रम नामांकन स्थिति बैज।
    'enrollment_status_active'      => 'सक्रिय',
    'enrollment_status_completed'   => 'पूर्ण',
    'enrollment_status_replied'     => 'उत्तर दिया',
    'enrollment_status_unenrolled'  => 'नामांकन रद्द',

    // मिटाने के दौरान डेटाबेस पंक्तियों में लिखे गए GDPR गुमनामीकरण लेबल
    // ताकि प्लेसहोल्डर गुमनामीकरण के क्षण में ऑपरेटर की सक्रिय लोकेल से
    // मेल खाएँ।
    'gdpr_anonymous'                => 'अनाम',
    'gdpr_task_label'               => 'कार्य #:id',
];
