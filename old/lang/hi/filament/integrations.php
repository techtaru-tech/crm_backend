<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — IntegrationResource translation strings (hi)
|--------------------------------------------------------------------------
|
| Labels, placeholders, helper text, and action copy for the Integrations
| resource at /admin/integrations.
| Consumed via __('filament/integrations.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'एकीकरण',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'एकीकरण',
    'plural_model_label'                => 'एकीकरण',

    // ─── Table column labels ──────────────────────────────────────────
    'col_status'                        => 'स्थिति',

    // ─── Form: integration setup ───────────────────────────────────────
    'integration_type'                  => 'एकीकरण प्रकार',
    'display_name'                      => 'प्रदर्शन नाम',
    'enable_this_integration'           => 'इस एकीकरण को सक्षम करें',

    // ─── Form: connection configuration ────────────────────────────────
    'webhook_url'                       => 'Webhook URL',
    'webhook_url_placeholder'           => 'https://...',
    'api_key'                           => 'API कुंजी',
    'access_token_oauth'                => 'एक्सेस टोकन / OAuth टोकन',
    'domain_subdomain'                  => 'डोमेन / सबडोमेन',
    'domain_placeholder'                => 'yourcompany',
    'instance_url'                      => 'इंस्टेंस URL',
    'instance_url_placeholder'          => 'https://yourinstance.salesforce.com',
    'list_audience_id'                  => 'सूची / ऑडियंस ID',
    'board_id'                          => 'बोर्ड ID',
    'spreadsheet_id'                    => 'स्प्रेडशीट ID',
    'sheet_name'                        => 'शीट का नाम',
    'notion_database_id'                => 'Notion डेटाबेस ID',
    'airtable_base_id'                  => 'Airtable बेस ID',
    'airtable_table_name'               => 'Airtable तालिका का नाम',
    'account_sid'                       => 'खाता SID',
    'auth_token'                        => 'प्रमाणीकरण टोकन',
    'from_number'                       => 'प्रेषक नंबर',
    'api_secret'                        => 'API सीक्रेट',
    'user_email'                        => 'उपयोगकर्ता ईमेल',
    'streak_pipeline_key'               => 'Streak पाइपलाइन कुंजी',
    'activecampaign_api_url'            => 'ActiveCampaign API URL',
    'activecampaign_api_url_placeholder' => 'https://youracccount.api-us1.com',
    'convertkit_form_id'                => 'ConvertKit फॉर्म / सीक्वेंस ID',
    'drip_account_id'                   => 'Drip खाता ID',
    'api_token'                         => 'API टोकन',
    'mailerlite_group_id'               => 'MailerLite समूह ID',
    'mailchimp_data_center'             => 'Mailchimp डेटा सेंटर (जैसे us1)',
    'zendesk_subdomain'                 => 'Zendesk सबडोमेन',
    'zendesk_admin_email'               => 'Zendesk व्यवस्थापक ईमेल',
    'zendesk_api_token'                 => 'Zendesk API टोकन',
    'salesforce_object_type'            => 'Salesforce ऑब्जेक्ट प्रकार',
    'salesforce_object_lead'            => 'लीड',
    'salesforce_object_contact'         => 'संपर्क',
    'sms_template'                      => 'SMS टेम्पलेट',
    'sms_template_placeholder'          => 'नई लीड: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    'slack_message_template'            => 'Slack संदेश टेम्पलेट',
    'slack_message_template_placeholder' => '{{lead.first_name}}, {{lead.email}}, आदि का उपयोग करें।',
    'auth_type'                         => 'प्रमाणीकरण प्रकार',
    'auth_type_none'                    => 'कोई नहीं',
    'auth_type_bearer'                  => 'Bearer टोकन',
    'auth_type_api_key'                 => 'API कुंजी हेडर',
    'auth_type_basic'                   => 'मूल प्रमाणीकरण',
    'auth_value'                        => 'प्रमाणीकरण मान (टोकन/कुंजी/क्रेडेंशियल)',
    'json_body_template'                => 'JSON बॉडी टेम्पलेट',
    'json_body_template_placeholder'    => '{"name": "{{lead.first_name}} {{lead.last_name}}", "email": "{{lead.email}}"}',

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_label'               => 'LeadHub फ़ील्ड को लक्ष्य फ़ील्ड पर मैप करें',
    'leadhub_field'                     => 'LeadHub फ़ील्ड',
    'target_field_name'                 => 'लक्ष्य फ़ील्ड का नाम',
    'target_field_placeholder'          => 'जैसे email, FIRST_NAME, properties.email',
    'add_field_mapping'                 => 'फ़ील्ड मैपिंग जोड़ें',
    'lh_field_first_name'               => 'पहला नाम',
    'lh_field_last_name'                => 'अंतिम नाम',
    'lh_field_email'                    => 'ईमेल',
    'lh_field_phone'                    => 'फ़ोन',
    'lh_field_company'                  => 'कंपनी',
    'lh_field_source'                   => 'लीड स्रोत',
    'lh_field_status'                   => 'स्थिति',
    'lh_field_lead_score'               => 'लीड स्कोर',
    'lh_field_address'                  => 'पता',
    'lh_field_city'                     => 'शहर',
    'lh_field_country'                  => 'देश',
    'lh_field_notes'                    => 'नोट्स',

    // ─── Form: filter leads ────────────────────────────────────────────
    'filter_sources'                    => 'केवल इन स्रोतों से सिंक करें (सभी के लिए खाली छोड़ें)',
    'filter_tags'                       => 'केवल इन टैग वाली लीड सिंक करें',

    // ─── Table columns ─────────────────────────────────────────────────
    'name'                              => 'नाम',
    'category'                          => 'श्रेणी',
    'last_sync'                         => 'अंतिम सिंक',
    'last_sync_never'                   => 'कभी नहीं',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_status'               => 'स्थिति',
    'status_connected'                  => 'जुड़ा हुआ',
    'status_disconnected'               => 'अलग किया गया',
    'status_error'                      => 'त्रुटि / टूटा हुआ',
    'enabled'                           => 'सक्षम',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_test'                       => 'परीक्षण करें',
    'action_sync_logs'                  => 'सिंक लॉग',
    'connection_successful'             => 'कनेक्शन सफल!',
    'connection_failed'                 => 'कनेक्शन विफल। अपने क्रेडेंशियल जाँचें।',
    'error_prefix'                      => 'त्रुटि: ',

    // ─── Bulk actions ──────────────────────────────────────────────────
    'bulk_enable'                       => 'चयनित को सक्षम करें',
    'bulk_disable'                      => 'चयनित को अक्षम करें',

    // ─── ListIntegrations notifications ────────────────────────────────
    'notify_saved'                      => 'एकीकरण सहेजा गया।',
    'notify_enabled'                    => 'एकीकरण सक्षम किया गया।',
    'notify_disabled'                   => 'एकीकरण अक्षम किया गया।',
    'notify_removed'                    => 'एकीकरण हटाया गया।',

    // ─── Sync logs header actions ──────────────────────────────────────
    'retry_all_failed'                  => 'सभी विफल को पुनः प्रयास करें',
    'back_to_integrations'              => 'एकीकरण पर वापस जाएँ',
    'retrying_failed_syncs'             => ':count विफल सिंक का पुनः प्रयास किया जा रहा है।',

    // ─── List page: modal + cards ──────────────────────────────────────
    'connection_settings_heading'       => 'कनेक्शन सेटिंग्स',
    'modal_cancel'                      => 'रद्द करें',
    'modal_save_integration'            => 'एकीकरण सहेजें',
    'confirm_remove_integration'        => 'क्या इस एकीकरण को हटाना है?',

    // ─── Component: integration-config-notice ──────────────────────────
    'config_notice_note_label'          => 'नोट:',
    'config_notice_body'                => 'क्रेडेंशियल विश्राम पर एन्क्रिप्टेड हैं। OAuth एकीकरण (HubSpot, Salesforce, Zoho, Google Sheets) के लिए आपके OAuth प्रवाह से एक्सेस टोकन आवश्यक है। इसे ऊपर एक्सेस टोकन फ़ील्ड में पेस्ट करें।',

    // ─── List page: status pills ───────────────────────────────────────
    'status_connected_label'            => 'जुड़ा हुआ',
    'status_error_label'                => 'त्रुटि',
    'status_inactive_label'             => 'निष्क्रिय',
    'action_test_connection'            => 'कनेक्शन परीक्षण करें',
    'action_configure'                  => 'कॉन्फ़िगर करें',
    'action_sync_logs_title'            => 'सिंक लॉग',
    'action_remove'                     => 'हटाएँ',
    'action_disable'                    => 'अक्षम करें',
    'action_enable'                     => 'सक्षम करें',
    'last_sync_prefix'                  => 'अंतिम सिंक: :time',
    'btn_connect'                       => '+ कनेक्ट करें',
    'setup_title_prefix'                => 'सेटअप: :type',

    // ─── List page: OAuth config ───────────────────────────────────────
    'oauth_connected_pill'              => 'OAuth के माध्यम से जुड़ा हुआ',
    'oauth_reconnect'                   => 'OAuth के माध्यम से पुनः कनेक्ट करें',
    'oauth_connect'                     => 'OAuth के माध्यम से कनेक्ट करें',
    'oauth_hint'                        => 'पहले नीचे अपना Client ID और Secret दर्ज करें, फिर अधिकृत करने के लिए क्लिक करें।',

    // ─── List page: field mapping ──────────────────────────────────────
    'field_mapping_heading'             => 'फ़ील्ड मैपिंग',
    'field_optional_suffix'             => '(वैकल्पिक)',
    'field_mapping_desc'                => 'जुड़े हुए ऐप में :app फ़ील्ड को लक्ष्य फ़ील्ड नामों पर मैप करें।',
    'select_source_field'               => ':app फ़ील्ड',
    'select_target_field'               => 'लक्ष्य फ़ील्ड चुनें',
    'target_field_input_placeholder'    => 'लक्ष्य फ़ील्ड का नाम',
    'btn_add_mapping'                   => '+ मैपिंग जोड़ें',

    // ─── List page: source filter ──────────────────────────────────────
    'source_filter_heading'             => 'स्रोत फ़िल्टर',
    'source_filter_desc'                => 'केवल इन स्रोतों से लीड सिंक करें (सभी सिंक करने के लिए खाली छोड़ें)।',

    // ─── List page: tag filter ─────────────────────────────────────────
    'tag_filter_heading'                => 'टैग फ़िल्टर',
    'tag_filter_desc'                   => 'केवल वे लीड सिंक करें जिनमें इनमें से कम से कम एक टैग हो (सभी सिंक करने के लिए खाली छोड़ें)।',
    'no_tags_created'                   => 'अभी तक कोई टैग नहीं बनाया गया।',

    // ─── List page: pipeline filter ────────────────────────────────────
    'pipeline_filter_heading'           => 'पाइपलाइन फ़िल्टर',
    'pipeline_filter_desc'              => 'केवल वे लीड सिंक करें जो वर्तमान में इन पाइपलाइनों में हैं (सभी सिंक करने के लिए खाली छोड़ें)।',
    'no_pipelines_created'              => 'अभी तक कोई पाइपलाइन नहीं बनाई गई।',

    // ─── Sync logs page ────────────────────────────────────────────────
    'sync_logs_heading'                 => 'सिंक लॉग — :name',
    'sync_logs_total'                   => 'कुल :count',
    'no_sync_logs'                      => 'अभी तक कोई सिंक लॉग नहीं। एकीकरण ट्रिगर होने पर लीड यहाँ सिंक हो जाएँगी।',
    'col_lead'                          => 'लीड',
    'col_event'                         => 'घटना',
    'col_status'                        => 'स्थिति',
    'col_attempts'                      => 'प्रयास',
    'col_time'                          => 'समय',
    'col_details'                       => 'विवरण',
    'btn_show'                          => 'दिखाएँ',
    'btn_hide'                          => 'छिपाएँ',
    'detail_payload'                    => 'पेलोड',
    'detail_response'                   => 'प्रतिक्रिया',
];
