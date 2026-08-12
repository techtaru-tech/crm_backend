<?php

return [
    'categories' => [
        'automation'            => 'स्वचालन',
        'crm'                   => 'CRM',
        'email_marketing'       => 'ईमेल मार्केटिंग',
        'communication'         => 'संचार',
        'data_and_productivity' => 'डेटा एवं उत्पादकता',
        'other'                 => 'अन्य',
    ],

    'labels' => [
        'generic_webhook' => 'सामान्य Webhook',
        'rest_api_push'   => 'REST API पुश',
    ],

    'descriptions' => [
        'zapier'          => 'Zapier वेबहुक ट्रिगर के माध्यम से लीड को 5,000+ ऐप्स पर भेजें',
        'n8n'             => 'n8n वेबहुक नोड्स के साथ वर्कफ़्लो को स्वचालित करें',
        'make'            => 'Make (Integromat) वेबहुक मॉड्यूल से कनेक्ट करें',
        'pabbly'          => 'Pabbly Connect वर्कफ़्लो में लीड भेजें',
        'activepieces'    => 'नई लीड पर Activepieces फ़्लो ट्रिगर करें',
        'workato'         => 'Workato स्वचालन रेसिपी में लीड भेजें',
        'hubspot'         => 'HubSpot CRM में कॉन्टैक्ट और डील बनाएँ या अपडेट करें',
        'salesforce'      => 'लीड को Salesforce Leads या Contacts के रूप में पुश करें',
        'pipedrive'       => 'Pipedrive में Persons और Deals बनाएँ',
        'zoho_crm'        => 'Zoho CRM में लीड बनाएँ',
        'freshsales'      => 'Freshsales में कॉन्टैक्ट और डील बनाएँ',
        'monday'          => 'Monday.com बोर्ड में आइटम बनाएँ',
        'copper'          => 'Copper CRM में लीड या Persons बनाएँ',
        'close'           => 'Close CRM में लीड और कॉन्टैक्ट बनाएँ',
        'streak'          => 'Streak पाइपलाइन में बॉक्स बनाएँ',
        'insightly'       => 'Insightly में लीड या कॉन्टैक्ट बनाएँ',
        'bitrix24'        => 'Bitrix24 में CRM लीड बनाएँ',
        'sugarcrm'        => 'SugarCRM में लीड रिकॉर्ड बनाएँ',
        'vtiger'          => 'Vtiger में लीड रिकॉर्ड बनाएँ',
        'mailchimp'       => 'Mailchimp ऑडियंस में लीड को सब्सक्राइब करें',
        'activecampaign'  => 'ActiveCampaign सूचियों में कॉन्टैक्ट जोड़ें',
        'klaviyo'         => 'Klaviyo सूचियों में प्रोफ़ाइल जोड़ें',
        'brevo'           => 'Brevo (Sendinblue) कॉन्टैक्ट बनाएँ या अपडेट करें',
        'convertkit'      => 'ConvertKit अनुक्रमों में लीड को सब्सक्राइब करें',
        'drip'            => 'Drip में सब्सक्राइबर बनाएँ या अपडेट करें',
        'getresponse'     => 'GetResponse सूचियों में कॉन्टैक्ट जोड़ें',
        'moosend'         => 'Moosend मेलिंग सूचियों में लीड को सब्सक्राइब करें',
        'mailerlite'      => 'MailerLite समूहों में सब्सक्राइबर जोड़ें',
        'slack'           => 'Slack चैनल में लीड कार्ड पोस्ट करें',
        'microsoft_teams' => 'Microsoft Teams चैनल में लीड कार्ड पोस्ट करें',
        'twilio'          => 'Twilio के माध्यम से SMS सूचनाएँ भेजें',
        'vonage'          => 'Vonage/Nexmo के माध्यम से SMS सूचनाएँ भेजें',
        'intercom'        => 'Intercom में कॉन्टैक्ट बनाएँ या अपडेट करें',
        'zendesk'         => 'Zendesk में टिकट या कॉन्टैक्ट बनाएँ',
        'google_sheets'   => 'Google Sheets में लीड डेटा को पंक्तियों के रूप में जोड़ें',
        'notion'          => 'Notion डेटाबेस में पृष्ठ बनाएँ',
        'airtable'        => 'Airtable बेस में रिकॉर्ड बनाएँ',
        'generic_webhook' => 'किसी भी URL पर कस्टम JSON पेलोड के साथ लीड भेजें',
        'rest_api_push'   => 'किसी REST API एंडपॉइंट पर लीड डेटा POST करें',
    ],

    'fields' => [
        'generic_webhook' => [
            'webhook_url' => [
                'label' => 'Webhook URL',
            ],
            'method' => [
                'label' => 'HTTP विधि',
            ],
            'auth_type' => [
                'label'   => 'प्रमाणीकरण प्रकार',
                'options' => [
                    'none'    => 'कोई नहीं',
                    'bearer'  => 'Bearer Token',
                    'api_key' => 'API Key हेडर',
                    'basic'   => 'Basic प्रमाणीकरण',
                ],
            ],
            'auth_value' => [
                'label' => 'Auth Token / कुंजी मान',
            ],
            'auth_header' => [
                'label'       => 'API Key हेडर नाम',
                'placeholder' => 'X-API-Key (जब प्रमाणीकरण प्रकार = API Key हेडर हो तब उपयोग होता है)',
            ],
            'body_template' => [
                'label' => 'JSON बॉडी टेम्पलेट',
            ],
            'custom_headers' => [
                'label' => 'कस्टम हेडर ({key,value} ऑब्जेक्ट्स की JSON सरणी)',
            ],
        ],

        'microsoft_teams' => [
            'webhook_url' => [
                'label' => 'Webhook URL',
            ],
            'message_template' => [
                'label'       => 'संदेश टेम्पलेट',
                'placeholder' => 'वैकल्पिक: {{lead.email}}, {{lead.pipeline_stage}} आदि का उपयोग करते हुए सादा-पाठ टेम्पलेट।',
            ],
        ],

        'slack' => [
            'webhook_url' => [
                'label' => 'Webhook URL',
            ],
            'message_template' => [
                'label' => 'संदेश टेम्पलेट',
            ],
        ],

        'hubspot' => [
            '_oauth_info' => [
                'label' => 'OAuth कनेक्शन',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'access_token' => [
                'label' => 'Access Token (मैनुअल ओवरराइड)',
            ],
            'create_deal' => [
                'label'   => 'सिंक पर डील बनाएँ',
                'options' => [
                    'yes' => 'हाँ',
                    'no'  => 'नहीं',
                ],
            ],
            'deal_pipeline' => [
                'label' => 'डील पाइपलाइन ID',
            ],
            'deal_stage' => [
                'label' => 'डील चरण ID',
            ],
        ],

        'salesforce' => [
            '_oauth_info' => [
                'label' => 'OAuth कनेक्शन',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'instance_url' => [
                'label' => 'इंस्टेंस URL',
            ],
            'access_token' => [
                'label' => 'Access Token (मैनुअल ओवरराइड)',
            ],
            'sf_object' => [
                'label' => 'ऑब्जेक्ट प्रकार',
            ],
        ],

        'pipedrive' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'zoho_crm' => [
            '_oauth_info' => [
                'label' => 'OAuth कनेक्शन',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'access_token' => [
                'label' => 'Access Token (मैनुअल ओवरराइड)',
            ],
            'region' => [
                'label' => 'क्षेत्र (com, eu, in, com.au)',
            ],
        ],

        'freshsales' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'domain' => [
                'label' => 'सबडोमेन (जैसे yourcompany)',
            ],
            'create_deal' => [
                'label'   => 'कॉन्टैक्ट सिंक पर डील बनाएँ',
                'options' => [
                    'yes' => 'हाँ',
                    'no'  => 'नहीं',
                ],
            ],
            'deal_pipeline_id' => [
                'label' => 'डील पाइपलाइन ID (वैकल्पिक)',
            ],
            'deal_stage_id' => [
                'label' => 'डील चरण ID (वैकल्पिक)',
            ],
        ],

        'monday' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'board_id' => [
                'label' => 'बोर्ड ID',
            ],
        ],

        'copper' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'user_email' => [
                'label' => 'उपयोगकर्ता ईमेल',
            ],
        ],

        'close' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'insightly' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'streak' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'pipeline_key' => [
                'label' => 'पाइपलाइन कुंजी',
            ],
        ],

        'sugarcrm' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'इंस्टेंस URL',
            ],
        ],

        'vtiger' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'इंस्टेंस URL',
            ],
        ],

        'mailchimp' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'ऑडियंस ID',
            ],
            'data_center' => [
                'label' => 'डेटा सेंटर (जैसे us1)',
            ],
            'tags' => [
                'label' => 'टैग (अल्पविराम-पृथक)',
            ],
        ],

        'activecampaign' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_url' => [
                'label' => 'API URL',
            ],
            'list_id' => [
                'label' => 'सूची ID (वैकल्पिक)',
            ],
            'tags' => [
                'label' => 'टैग (अल्पविराम-पृथक)',
            ],
        ],

        'klaviyo' => [
            'api_key' => [
                'label' => 'निजी API Key',
            ],
            'list_id' => [
                'label' => 'सूची ID',
            ],
        ],

        'brevo' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'सूची ID',
            ],
        ],

        'convertkit' => [
            'api_key' => [
                'label' => 'API Key (सार्वजनिक)',
            ],
            'form_id' => [
                'label' => 'फ़ॉर्म / अनुक्रम ID',
            ],
        ],

        'drip' => [
            'api_token' => [
                'label' => 'API Token',
            ],
            'account_id' => [
                'label' => 'खाता ID',
            ],
        ],

        'getresponse' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'अभियान ID',
            ],
        ],

        'moosend' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'मेलिंग सूची ID',
            ],
        ],

        'mailerlite' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'group_id' => [
                'label' => 'समूह ID',
            ],
        ],

        'twilio' => [
            'account_sid' => [
                'label' => 'Account SID',
            ],
            'auth_token' => [
                'label' => 'Auth Token',
            ],
            'from_number' => [
                'label' => 'प्रेषक नंबर',
            ],
            'sms_template' => [
                'label'       => 'SMS टेम्पलेट',
                'placeholder' => 'नई लीड: {{lead.first_name}} ({{lead.email}})',
            ],
        ],

        'vonage' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_secret' => [
                'label' => 'API Secret',
            ],
            'from_number' => [
                'label' => 'प्रेषक नंबर',
            ],
            'sms_template' => [
                'label' => 'SMS टेम्पलेट',
            ],
        ],

        'intercom' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
        ],

        'zendesk' => [
            'subdomain' => [
                'label'       => 'सबडोमेन',
                'placeholder' => 'yourcompany',
            ],
            'email' => [
                'label' => 'एडमिन ईमेल',
            ],
            'api_token_zendesk' => [
                'label' => 'API Token',
            ],
            'create_ticket' => [
                'label'   => 'टिकट के रूप में बनाएँ (कॉन्टैक्ट के बजाय)',
                'options' => [
                    'contact' => 'कॉन्टैक्ट (अंतिम उपयोगकर्ता)',
                    'ticket'  => 'टिकट',
                ],
            ],
            'ticket_subject_template' => [
                'label'       => 'टिकट विषय टेम्पलेट',
                'placeholder' => 'नई लीड: {{lead.first_name}} {{lead.last_name}}',
            ],
        ],

        'google_sheets' => [
            '_oauth_info' => [
                'label' => 'OAuth कनेक्शन',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'OAuth प्रवाह के लिए आवश्यक',
            ],
            'access_token' => [
                'label' => 'OAuth Access Token (OAuth के बाद स्वतः भरा जाता है)',
            ],
            'spreadsheet_id' => [
                'label' => 'स्प्रेडशीट ID',
            ],
            'sheet_name' => [
                'label' => 'शीट का नाम',
            ],
        ],

        'notion' => [
            'api_key' => [
                'label' => 'एकीकरण टोकन',
            ],
            'database_id' => [
                'label' => 'डेटाबेस ID',
            ],
            'property_mapping' => [
                'label' => 'गुण मैपिंग (JSON सरणी — डिफ़ॉल्ट के लिए खाली छोड़ें)',
            ],
        ],

        'airtable' => [
            'api_key' => [
                'label' => 'Personal Access Token',
            ],
            'base_id' => [
                'label' => 'बेस ID',
            ],
            'table_id' => [
                'label' => 'तालिका का नाम',
            ],
        ],
    ],

    'exceptions' => [
        'unknown_type' => 'अज्ञात एकीकरण प्रकार: :type',
    ],
];
