<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — AutomationResource अनुवाद स्ट्रिंग्स
|--------------------------------------------------------------------------
|
| /admin/automations पर Automations संसाधन के लिए लेबल, सहायक टेक्स्ट,
| प्लेसहोल्डर, विवरण और बल्क-एक्शन कॉपी।
| __('filament/automations.<key>') के माध्यम से उपयोग किया जाता है।
|
*/

return [

    // ----- नेविगेशन -----
    'nav_label'           => 'स्वचालन',
    'nav_badge_tooltip'   => 'पिछले 24 घंटों में स्वचालन विफलताएँ',

    // ----- मॉडल लेबल (ब्रेडक्रंब / पेज शीर्षक) -----
    'model_label'         => 'स्वचालन',
    'plural_model_label'  => 'स्वचालन',

    // ----- रिपीटर itemLabel टेम्पलेट (इमोजी आइकन शाब्दिक रूप से सुरक्षित) -----
    'item_label_condition' => '🔍 शर्त: ',
    'item_label_action'    => '⚡ क्रिया: ',
    'item_label_delay_wait' => '⏱ प्रतीक्षा करें ',
    'item_label_delay_default_unit' => 'मिनट',

    // ─── बुनियादी विवरण ──────────────────────────────────────────────
    'automation_name'                   => 'स्वचालन का नाम',
    'description'                       => 'विवरण',
    'active'                            => 'सक्रिय',
    'respect_business_hours'            => 'व्यावसायिक घंटों का सम्मान करें',
    'respect_business_hours_help'       => 'Tenant के व्यावसायिक घंटों की विंडो के बाहर ट्रिगर्स को छोड़ें।',

    // ─── ट्रिगर अनुभाग ──────────────────────────────────────────────
    'trigger_description'               => 'परिभाषित करें कि यह स्वचालन कब सक्रिय होगा।',
    'trigger_event'                     => 'ट्रिगर इवेंट',

    // ─── चरण अनुभाग ─────────────────────────────────────────────────
    'steps_description'                 => 'शर्तें (फ़िल्टर), क्रियाएँ और विलंब जोड़ें। चरण ऊपर से नीचे तक निष्पादित होते हैं। पुनः क्रमित करने के लिए खींचें।',
    'add_step'                          => 'चरण जोड़ें',
    'step_type'                         => 'चरण का प्रकार',

    // ─── ट्रिगर कॉन्फ़िगरेशन — प्रत्येक प्रकार के लिए ────────────────
    'filter_by_sources'                 => 'स्रोत(ओं) के अनुसार फ़िल्टर करें',
    'filter_by_sources_help'            => 'सभी स्रोतों पर ट्रिगर करने के लिए खाली छोड़ें।',
    'from_stage'                        => 'इस चरण से',
    'to_stage'                          => 'इस चरण तक',
    'tag_name'                          => 'टैग का नाम',
    'score_threshold'                   => 'स्कोर सीमा',
    'crosses'                           => 'पार करता है',
    'no_activity_for'                   => 'इतने समय तक कोई गतिविधि नहीं',
    'unit'                              => 'इकाई',
    'form_blank_for_any'                => 'फॉर्म (किसी के लिए खाली छोड़ें)',

    // ─── शर्त कॉन्फ़िगरेशन ──────────────────────────────────────────
    'condition_type'                    => 'शर्त का प्रकार',
    'source'                            => 'स्रोत',
    'field_name'                        => 'फ़ील्ड का नाम',
    'field_name_placeholder'            => 'जैसे email, status',
    'value'                             => 'मान',
    'score'                             => 'स्कोर',
    'user'                              => 'उपयोगकर्ता',
    'time_range'                        => 'समय सीमा (HH:MM-HH:MM)',
    'time_range_placeholder'            => '09:00-17:00',
    'days'                              => 'दिन',
    'days_placeholder'                  => 'सोमवार,मंगलवार',

    // ─── क्रिया कॉन्फ़िगरेशन ────────────────────────────────────────
    'action'                            => 'क्रिया',
    'email_template'                    => 'ईमेल टेम्पलेट',
    'notify_users'                      => 'उपयोगकर्ताओं को सूचित करें',
    'notify_assigned_agent'             => 'सौंपे गए एजेंट को भी सूचित करें',
    'custom_message'                    => 'कस्टम संदेश',
    'assignment_mode'                   => 'असाइनमेंट मोड',
    'users_round_robin_pool'            => 'उपयोगकर्ता (राउंड रॉबिन पूल)',
    'target_stage'                      => 'लक्ष्य चरण',
    'new_status'                        => 'नई स्थिति',
    'webhook_url'                       => 'Webhook URL',
    'hmac_secret'                       => 'HMAC सीक्रेट (वैकल्पिक)',
    'task_title'                        => 'कार्य का शीर्षक',
    'task_title_help'                   => '{first_name}, {last_name}, {full_name}, {email}, {lead_score} का समर्थन करता है।',
    'due_in_hours'                      => 'घंटों में देय (अभी से)',
    'due_in_hours_help'                 => 'स्वचालन सक्रिय होने के इतने घंटे बाद कार्य देय होगा।',
    'priority'                          => 'प्राथमिकता',
    'assign_task_to'                    => 'कार्य किसे सौंपें',
    'assign_task_to_help'               => 'लीड के सौंपे गए उपयोगकर्ता पर वापस लौटने के लिए खाली छोड़ें।',
    'slack_webhook_url'                 => 'Slack Webhook URL',
    'slack_message'                     => 'संदेश ({{lead.first_name}} आदि का समर्थन करता है)',
    'sms_message'                       => 'SMS संदेश',
    'sms_message_help'                  => '{{first_name}}, {{last_name}}, {{full_name}}, {{email}}, {{company}} का समर्थन करता है',

    // ─── विलंब कॉन्फ़िगरेशन ─────────────────────────────────────────
    'wait'                              => 'प्रतीक्षा करें',

    // ─── तालिका कॉलम ────────────────────────────────────────────────
    'name'                              => 'नाम',
    'trigger'                           => 'ट्रिगर',
    'steps'                             => 'चरण',
    'runs'                              => 'चलाएँ',
    'created'                           => 'निर्मित',

    // ─── पंक्ति क्रियाएँ ─────────────────────────────────────────────
    'history'                           => 'इतिहास',

    // ─── बल्क क्रियाएँ ──────────────────────────────────────────────
    'enable_selected'                   => 'चयनित को सक्षम करें',
    'disable_selected'                  => 'चयनित को अक्षम करें',

    // ─── हेडर क्रियाएँ ──────────────────────────────────────────────
    'browse_templates'                  => 'टेम्पलेट ब्राउज़ करें',
    'run_history'                       => 'चलाने का इतिहास',
    'back_to_automation'                => 'स्वचालन पर वापस जाएँ',

    // ─── चलाने का इतिहास पृष्ठ ──────────────────────────────────────
    'run_history_heading'               => 'चलाने का इतिहास — :name',
    'runs_count'                        => ':count बार चला',
    'no_runs_yet'                       => 'अभी तक कोई रन नहीं। यह स्वचालन सक्रिय नहीं हुआ है।',
    'col_lead'                          => 'लीड',
    'col_started'                       => 'शुरू हुआ',
    'col_duration'                      => 'अवधि',
    'col_status'                        => 'स्थिति',
    'col_steps'                         => 'चरण',
    'btn_hide'                          => 'छिपाएँ',
    'btn_show'                          => 'दिखाएँ',
    'btn_show_steps'                    => ':count चरण',
    'no_log'                            => 'कोई लॉग नहीं',

    // ─── चयन विकल्प ─────────────────────────────────────────────────
    'option_above_threshold'            => 'सीमा से ऊपर',
    'option_below_threshold'            => 'सीमा से नीचे',
    'option_minutes'                    => 'मिनट',
    'option_hours'                      => 'घंटे',
    'option_days'                       => 'दिन',
    'option_specific_user'              => 'विशिष्ट उपयोगकर्ता',
    'option_round_robin'                => 'राउंड रॉबिन',
    'option_lead_status_new'            => 'नया',
    'option_lead_status_contacted'      => 'संपर्क किया गया',
    'option_lead_status_qualified'      => 'योग्य',
    'option_lead_status_lost'           => 'खोया',
    'option_lead_status_won'            => 'जीता',
];
