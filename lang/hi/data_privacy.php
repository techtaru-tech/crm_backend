<?php

declare(strict_types=1);

return [

    'error_no_tenant'             => 'हम आपके कार्यक्षेत्र को हल नहीं कर सके। कृपया लॉग आउट करें और फिर से लॉग इन करें।',

    'export_title'                => 'मेरा डेटा निर्यात करें',
    'export_description'          => 'हम इस कार्यक्षेत्र के लिए हमारे पास मौजूद हर रिकॉर्ड का एक ZIP बनाएंगे — लीड्स, फ़ॉर्म, स्वचालन, सेटिंग्स, सब कुछ — JSON प्रारूप में। डाउनलोड लिंक 48 घंटों के लिए वैध है।',

    'export_building_title'       => 'आपका निर्यात तैयार किया जा रहा है…',
    'export_building_body'        => 'कुछ मिनटों में पृष्ठ रिफ़्रेश करें। बड़े कार्यक्षेत्रों में कुछ समय लग सकता है।',

    'export_failed_title'         => 'पिछला निर्यात विफल रहा।',
    'export_failed_body'          => 'नया अनुरोध करने का प्रयास करें — यदि यह फिर से विफल होता है, तो समर्थन से संपर्क करें।',
    'export_failed_btn'           => 'पुनः प्रयास करें',

    'export_ready_title'          => '✅ आपका संग्रह तैयार है',
    'export_ready_size'           => 'आकार: :size',
    'export_ready_expires'        => 'समाप्ति: :when',
    'export_download_btn'         => 'ZIP डाउनलोड करें',
    'export_rebuild_btn'          => 'नया निर्यात तैयार करें',
    'export_request_btn'          => 'मेरा डेटा निर्यात करें',

    'deletion_title'              => 'मेरा कार्यक्षेत्र हटाएँ',
    'deletion_description'        => 'इस कार्यक्षेत्र और इसके लिए हमारे पास मौजूद हर रिकॉर्ड को स्थायी रूप से हटा देता है। 30 दिन पहले निर्धारित किया जाता है ताकि आप अपना मन बदल सकें — तब तक, कार्यक्षेत्र सक्रिय रहता है।',

    'deletion_scheduled_title'    => '⏰ विलोपन निर्धारित',
    'deletion_scheduled_on'       => ':date को',
    'deletion_days_left'          => '(:count दिन शेष)|(:count दिन शेष)',
    'deletion_cancel_btn'         => 'विलोपन रद्द करें',

    'deletion_request_confirm'    => 'क्या आप पूरी तरह से निश्चित हैं? यह इस कार्यक्षेत्र में हर रिकॉर्ड के स्थायी विलोपन को निर्धारित करता है। आपके पास रद्द करने के लिए 30 दिन होंगे।',
    'deletion_request_btn'        => 'विलोपन निर्धारित करें',

    'deletion_not_owner'          => 'केवल कार्यक्षेत्र स्वामी ही विलोपन निर्धारित कर सकता है। यदि इस कार्यक्षेत्र को स्थायी रूप से हटाने की आवश्यकता है तो कृपया स्वामी से संपर्क करें।',

    'gdpr_footer'                 => 'ये नियंत्रण GDPR अनुच्छेद 15 (पहुँच का अधिकार) और 17 (विलोपन का अधिकार) को लागू करते हैं। DPA / उप-प्रोसेसर जानकारी के लिए, समर्थन से संपर्क करें।',

    'readme_title'                => ':app — व्यक्तिगत डेटा निर्यात',
    'readme_divider'              => '============================================================',
    'readme_workspace'            => 'कार्यक्षेत्र: :workspace',
    'readme_generated'            => 'उत्पन्न: :timestamp',
    'readme_intro'                => "इस संग्रह में आपके कार्यक्षेत्र के लिए हमारे पास मौजूद हर रिकॉर्ड है,\nपोर्टेबिलिटी के लिए JSON में निर्यात किया गया (कहीं भी आयात करने योग्य — CRM,\nस्प्रेडशीट्स, कस्टम स्क्रिप्ट)।",
    'readme_file_map_header'      => 'फ़ाइल मानचित्र',
    'readme_subdivider'           => '------------------------------------------------------------',
    'readme_row_readme'           => 'README.txt                         यह फ़ाइल',
    'readme_row_tenant'           => 'tenant.json                        कार्यक्षेत्र प्रोफ़ाइल, सेटिंग्स, ब्रांडिंग',
    'readme_row_members'          => 'members.json                       टीम सदस्य + भूमिकाएँ',
    'readme_row_leads'            => 'leads.json                         हर लीड',
    'readme_row_companies'        => 'companies.json                     लिंक की गई कंपनी रिकॉर्ड्स',
    'readme_row_activities'       => 'lead_activities.json               टाइमलाइन ईवेंट्स',
    'readme_row_notes'            => 'lead_notes.json                    लीड्स से जुड़े नोट्स',
    'readme_row_tasks'            => 'lead_tasks.json                    कार्य',
    'readme_row_messages'         => 'lead_messages.json                 आवक / जावक संदेश',
    'readme_row_emails'           => 'lead_emails.json                   ईमेल',
    'readme_row_calls'            => 'lead_calls.json                    कॉल लॉग',
    'readme_row_pipelines'        => 'pipelines.json                     पाइपलाइन',
    'readme_row_pipeline_stages'  => 'pipeline_stages.json               चरण',
    'readme_row_tags'             => 'tags.json                          टैग',
    'readme_row_custom_fields'    => 'custom_field_definitions.json      कस्टम फ़ील्ड स्कीमा',
    'readme_row_forms'            => 'forms.json                         फ़ॉर्म',
    'readme_row_form_submissions' => 'form_submissions.json              सबमिशन',
    'readme_row_landing_pages'    => 'landing_pages.json                 लैंडिंग पृष्ठ',
    'readme_row_automations'      => 'automations.json                   स्वचालन',
    'readme_row_email_sequences'  => 'email_sequences.json               ड्रिप अभियान',
    'readme_row_email_templates'  => 'email_templates.json               टेम्पलेट',
    'readme_row_products'         => 'products.json                      कैटलॉग',
    'readme_row_quotes'           => 'quotes.json                        कोटेशन',
    'readme_row_invoices'         => 'invoices.json                      इनवॉइस',
    'readme_row_meeting_types'    => 'meeting_types.json                 बुक करने योग्य मीटिंग प्रकार',
    'readme_row_meeting_bookings' => 'meeting_bookings.json              बुक की गई मीटिंग्स',
    'readme_row_integrations'     => 'integrations.json                  कनेक्टेड एकीकरण',
    'readme_row_api_keys'         => 'api_keys.json                      API कुंजी (रहस्य संशोधित)',
    'readme_notes_header'         => 'नोट्स',
    'readme_note_iso8601'         => '- सभी डेटटाइम फ़ील्ड ISO-8601 हैं।',
    'readme_note_redaction'       => "- API कुंजी रहस्य और कोई भी कॉलम जिसे secret / token /\n  api_key / key के रूप में चिह्नित किया गया है, संशोधित हैं।",
    'readme_note_attachments'     => "- फ़ाइल अनुलग्नक केवल पथ द्वारा संदर्भित हैं — वास्तविक फ़ाइलें\n  डाउनलोड करने के लिए, समर्थन से संपर्क करें।",
    'readme_note_snapshot'        => '- यह निर्यात :timestamp पर एक स्नैपशॉट है।',

];
