<?php

return [
    'categories' => [
        'automation'            => 'الأتمتة',
        'crm'                   => 'CRM',
        'email_marketing'       => 'التسويق عبر البريد الإلكتروني',
        'communication'         => 'الاتصالات',
        'data_and_productivity' => 'البيانات والإنتاجية',
        'other'                 => 'أخرى',
    ],

    'labels' => [
        'generic_webhook' => 'Webhook عام',
        'rest_api_push'   => 'إرسال إلى REST API',
    ],

    'descriptions' => [
        'zapier'          => 'أرسل العملاء المحتملين إلى أكثر من 5,000 تطبيق عبر مشغّلات Zapier Webhook',
        'n8n'             => 'أتمتة سير العمل باستخدام عُقد Webhook في n8n',
        'make'            => 'الاتصال بوحدات Webhook في Make (Integromat)',
        'pabbly'          => 'أرسل العملاء المحتملين إلى سير عمل Pabbly Connect',
        'activepieces'    => 'تشغيل تدفقات Activepieces عند ورود عملاء محتملين جدد',
        'workato'         => 'أرسل العملاء المحتملين إلى وصفات أتمتة Workato',
        'hubspot'         => 'إنشاء جهات الاتصال والصفقات أو تحديثها في HubSpot CRM',
        'salesforce'      => 'دفع العملاء المحتملين كـ Leads أو Contacts في Salesforce',
        'pipedrive'       => 'إنشاء أشخاص وصفقات في Pipedrive',
        'zoho_crm'        => 'إنشاء عملاء محتملين في Zoho CRM',
        'freshsales'      => 'إنشاء جهات اتصال وصفقات في Freshsales',
        'monday'          => 'إنشاء عناصر في لوحات Monday.com',
        'copper'          => 'إنشاء عملاء محتملين أو أشخاص في Copper CRM',
        'close'           => 'إنشاء عملاء محتملين وجهات اتصال في Close CRM',
        'streak'          => 'إنشاء صناديق في خطوط Streak',
        'insightly'       => 'إنشاء عملاء محتملين أو جهات اتصال في Insightly',
        'bitrix24'        => 'إنشاء عملاء محتملين في CRM داخل Bitrix24',
        'sugarcrm'        => 'إنشاء سجلات عملاء محتملين في SugarCRM',
        'vtiger'          => 'إنشاء سجلات عملاء محتملين في Vtiger',
        'mailchimp'       => 'اشتراك العملاء المحتملين في جماهير Mailchimp',
        'activecampaign'  => 'إضافة جهات اتصال إلى قوائم ActiveCampaign',
        'klaviyo'         => 'إضافة ملفات تعريف إلى قوائم Klaviyo',
        'brevo'           => 'إنشاء أو تحديث جهات اتصال Brevo (Sendinblue)',
        'convertkit'      => 'اشتراك العملاء المحتملين في تسلسلات ConvertKit',
        'drip'            => 'إنشاء أو تحديث المشتركين في Drip',
        'getresponse'     => 'إضافة جهات اتصال إلى قوائم GetResponse',
        'moosend'         => 'اشتراك العملاء المحتملين في قوائم بريد Moosend',
        'mailerlite'      => 'إضافة مشتركين إلى مجموعات MailerLite',
        'slack'           => 'نشر بطاقات العملاء المحتملين في قنوات Slack',
        'microsoft_teams' => 'نشر بطاقات العملاء المحتملين في قنوات Microsoft Teams',
        'twilio'          => 'إرسال إشعارات SMS عبر Twilio',
        'vonage'          => 'إرسال إشعارات SMS عبر Vonage/Nexmo',
        'intercom'        => 'إنشاء أو تحديث جهات الاتصال في Intercom',
        'zendesk'         => 'إنشاء تذاكر أو جهات اتصال في Zendesk',
        'google_sheets'   => 'إلحاق بيانات العميل المحتمل كصفوف في Google Sheets',
        'notion'          => 'إنشاء صفحات في قواعد بيانات Notion',
        'airtable'        => 'إنشاء سجلات في قواعد Airtable',
        'generic_webhook' => 'أرسل العملاء المحتملين إلى أي URL ببيانات JSON مخصصة',
        'rest_api_push'   => 'إرسال بيانات العميل المحتمل (POST) إلى نقطة نهاية REST API',
    ],

    'fields' => [
        'generic_webhook' => [
            'webhook_url' => [
                'label' => 'رابط Webhook',
            ],
            'method' => [
                'label' => 'طريقة HTTP',
            ],
            'auth_type' => [
                'label'   => 'نوع المصادقة',
                'options' => [
                    'none'    => 'بدون',
                    'bearer'  => 'Bearer Token',
                    'api_key' => 'رأس API Key',
                    'basic'   => 'مصادقة أساسية',
                ],
            ],
            'auth_value' => [
                'label' => 'Auth Token / قيمة المفتاح',
            ],
            'auth_header' => [
                'label'       => 'اسم رأس API Key',
                'placeholder' => 'X-API-Key (يُستخدم عندما يكون نوع المصادقة = رأس API Key)',
            ],
            'body_template' => [
                'label' => 'قالب نص JSON',
            ],
            'custom_headers' => [
                'label' => 'رؤوس مخصصة (مصفوفة JSON من كائنات {key,value})',
            ],
        ],

        'microsoft_teams' => [
            'webhook_url' => [
                'label' => 'رابط Webhook',
            ],
            'message_template' => [
                'label'       => 'قالب الرسالة',
                'placeholder' => 'اختياري: قالب نص عادي باستخدام {{lead.email}}، {{lead.pipeline_stage}}، إلخ.',
            ],
        ],

        'slack' => [
            'webhook_url' => [
                'label' => 'رابط Webhook',
            ],
            'message_template' => [
                'label' => 'قالب الرسالة',
            ],
        ],

        'hubspot' => [
            '_oauth_info' => [
                'label' => 'اتصال OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'access_token' => [
                'label' => 'Access Token (تجاوز يدوي)',
            ],
            'create_deal' => [
                'label'   => 'إنشاء صفقة عند المزامنة',
                'options' => [
                    'yes' => 'نعم',
                    'no'  => 'لا',
                ],
            ],
            'deal_pipeline' => [
                'label' => 'معرّف خط الصفقات',
            ],
            'deal_stage' => [
                'label' => 'معرّف مرحلة الصفقة',
            ],
        ],

        'salesforce' => [
            '_oauth_info' => [
                'label' => 'اتصال OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'instance_url' => [
                'label' => 'رابط المثيل',
            ],
            'access_token' => [
                'label' => 'Access Token (تجاوز يدوي)',
            ],
            'sf_object' => [
                'label' => 'نوع الكائن',
            ],
        ],

        'pipedrive' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'zoho_crm' => [
            '_oauth_info' => [
                'label' => 'اتصال OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'access_token' => [
                'label' => 'Access Token (تجاوز يدوي)',
            ],
            'region' => [
                'label' => 'المنطقة (com، eu، in، com.au)',
            ],
        ],

        'freshsales' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'domain' => [
                'label' => 'النطاق الفرعي (مثلاً yourcompany)',
            ],
            'create_deal' => [
                'label'   => 'إنشاء صفقة عند مزامنة جهة الاتصال',
                'options' => [
                    'yes' => 'نعم',
                    'no'  => 'لا',
                ],
            ],
            'deal_pipeline_id' => [
                'label' => 'معرّف خط الصفقات (اختياري)',
            ],
            'deal_stage_id' => [
                'label' => 'معرّف مرحلة الصفقة (اختياري)',
            ],
        ],

        'monday' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'board_id' => [
                'label' => 'معرّف اللوحة',
            ],
        ],

        'copper' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'user_email' => [
                'label' => 'البريد الإلكتروني للمستخدم',
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
                'label' => 'مفتاح الخط',
            ],
        ],

        'sugarcrm' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'رابط المثيل',
            ],
        ],

        'vtiger' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'رابط المثيل',
            ],
        ],

        'mailchimp' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'معرّف الجمهور',
            ],
            'data_center' => [
                'label' => 'مركز البيانات (مثلاً us1)',
            ],
            'tags' => [
                'label' => 'الوسوم (مفصولة بفواصل)',
            ],
        ],

        'activecampaign' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_url' => [
                'label' => 'رابط API',
            ],
            'list_id' => [
                'label' => 'معرّف القائمة (اختياري)',
            ],
            'tags' => [
                'label' => 'الوسوم (مفصولة بفواصل)',
            ],
        ],

        'klaviyo' => [
            'api_key' => [
                'label' => 'API Key خاص',
            ],
            'list_id' => [
                'label' => 'معرّف القائمة',
            ],
        ],

        'brevo' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'معرّف القائمة',
            ],
        ],

        'convertkit' => [
            'api_key' => [
                'label' => 'API Key (عام)',
            ],
            'form_id' => [
                'label' => 'معرّف النموذج / التسلسل',
            ],
        ],

        'drip' => [
            'api_token' => [
                'label' => 'API Token',
            ],
            'account_id' => [
                'label' => 'معرّف الحساب',
            ],
        ],

        'getresponse' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'معرّف الحملة',
            ],
        ],

        'moosend' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'معرّف القائمة البريدية',
            ],
        ],

        'mailerlite' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'group_id' => [
                'label' => 'معرّف المجموعة',
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
                'label' => 'رقم المرسِل',
            ],
            'sms_template' => [
                'label'       => 'قالب SMS',
                'placeholder' => 'عميل محتمل جديد: {{lead.first_name}} ({{lead.email}})',
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
                'label' => 'رقم المرسِل',
            ],
            'sms_template' => [
                'label' => 'قالب SMS',
            ],
        ],

        'intercom' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
        ],

        'zendesk' => [
            'subdomain' => [
                'label'       => 'النطاق الفرعي',
                'placeholder' => 'yourcompany',
            ],
            'email' => [
                'label' => 'البريد الإلكتروني للمشرف',
            ],
            'api_token_zendesk' => [
                'label' => 'API Token',
            ],
            'create_ticket' => [
                'label'   => 'إنشاء كتذكرة (بدلاً من جهة اتصال)',
                'options' => [
                    'contact' => 'جهة اتصال (مستخدم نهائي)',
                    'ticket'  => 'تذكرة',
                ],
            ],
            'ticket_subject_template' => [
                'label'       => 'قالب موضوع التذكرة',
                'placeholder' => 'عميل محتمل جديد: {{lead.first_name}} {{lead.last_name}}',
            ],
        ],

        'google_sheets' => [
            '_oauth_info' => [
                'label' => 'اتصال OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'مطلوب لتدفق OAuth',
            ],
            'access_token' => [
                'label' => 'OAuth Access Token (يُملأ تلقائياً بعد OAuth)',
            ],
            'spreadsheet_id' => [
                'label' => 'معرّف جدول البيانات',
            ],
            'sheet_name' => [
                'label' => 'اسم الورقة',
            ],
        ],

        'notion' => [
            'api_key' => [
                'label' => 'رمز التكامل',
            ],
            'database_id' => [
                'label' => 'معرّف قاعدة البيانات',
            ],
            'property_mapping' => [
                'label' => 'تعيين الخصائص (مصفوفة JSON — اتركه فارغاً لاستخدام الإعدادات الافتراضية)',
            ],
        ],

        'airtable' => [
            'api_key' => [
                'label' => 'Personal Access Token',
            ],
            'base_id' => [
                'label' => 'معرّف القاعدة',
            ],
            'table_id' => [
                'label' => 'اسم الجدول',
            ],
        ],
    ],

    'exceptions' => [
        'unknown_type' => 'نوع تكامل غير معروف: :type',
    ],
];
