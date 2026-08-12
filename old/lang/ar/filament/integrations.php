<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — IntegrationResource translation strings (ar)
|--------------------------------------------------------------------------
|
| Labels, placeholders, helper text, and action copy for the Integrations
| resource at /admin/integrations.
| Consumed via __('filament/integrations.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'التكاملات',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'تكامل',
    'plural_model_label'                => 'التكاملات',

    // ─── Table column labels ──────────────────────────────────────────
    'col_status'                        => 'الحالة',

    // ─── Form: integration setup ───────────────────────────────────────
    'integration_type'                  => 'نوع التكامل',
    'display_name'                      => 'الاسم الظاهر',
    'enable_this_integration'           => 'تفعيل هذا التكامل',

    // ─── Form: connection configuration ────────────────────────────────
    'webhook_url'                       => 'رابط Webhook',
    'webhook_url_placeholder'           => 'https://...',
    'api_key'                           => 'مفتاح API',
    'access_token_oauth'                => 'رمز الوصول / رمز OAuth',
    'domain_subdomain'                  => 'النطاق / النطاق الفرعي',
    'domain_placeholder'                => 'yourcompany',
    'instance_url'                      => 'رابط المثيل',
    'instance_url_placeholder'          => 'https://yourinstance.salesforce.com',
    'list_audience_id'                  => 'معرّف القائمة / الجمهور',
    'board_id'                          => 'معرّف اللوحة',
    'spreadsheet_id'                    => 'معرّف جدول البيانات',
    'sheet_name'                        => 'اسم الورقة',
    'notion_database_id'                => 'معرّف قاعدة بيانات Notion',
    'airtable_base_id'                  => 'معرّف قاعدة Airtable',
    'airtable_table_name'               => 'اسم جدول Airtable',
    'account_sid'                       => 'SID الحساب',
    'auth_token'                        => 'رمز المصادقة',
    'from_number'                       => 'الرقم المُرسِل',
    'api_secret'                        => 'سر API',
    'user_email'                        => 'البريد الإلكتروني للمستخدم',
    'streak_pipeline_key'               => 'مفتاح خط أنابيب Streak',
    'activecampaign_api_url'            => 'رابط API الخاص بـ ActiveCampaign',
    'activecampaign_api_url_placeholder' => 'https://youracccount.api-us1.com',
    'convertkit_form_id'                => 'معرّف نموذج / تسلسل ConvertKit',
    'drip_account_id'                   => 'معرّف حساب Drip',
    'api_token'                         => 'رمز API',
    'mailerlite_group_id'               => 'معرّف مجموعة MailerLite',
    'mailchimp_data_center'             => 'مركز بيانات Mailchimp (مثل us1)',
    'zendesk_subdomain'                 => 'النطاق الفرعي لـ Zendesk',
    'zendesk_admin_email'               => 'البريد الإلكتروني لمسؤول Zendesk',
    'zendesk_api_token'                 => 'رمز API الخاص بـ Zendesk',
    'salesforce_object_type'            => 'نوع كائن Salesforce',
    'salesforce_object_lead'            => 'عميل محتمل',
    'salesforce_object_contact'         => 'جهة اتصال',
    'sms_template'                      => 'قالب SMS',
    'sms_template_placeholder'          => 'عميل محتمل جديد: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    'slack_message_template'            => 'قالب رسالة Slack',
    'slack_message_template_placeholder' => 'استخدم {{lead.first_name}}, {{lead.email}}, إلخ.',
    'auth_type'                         => 'نوع المصادقة',
    'auth_type_none'                    => 'بدون',
    'auth_type_bearer'                  => 'رمز Bearer',
    'auth_type_api_key'                 => 'رأس مفتاح API',
    'auth_type_basic'                   => 'مصادقة أساسية',
    'auth_value'                        => 'قيمة المصادقة (الرمز/المفتاح/بيانات الاعتماد)',
    'json_body_template'                => 'قالب نص JSON',
    'json_body_template_placeholder'    => '{"name": "{{lead.first_name}} {{lead.last_name}}", "email": "{{lead.email}}"}',

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_label'               => 'تعيين حقول LeadHub إلى الحقول المستهدفة',
    'leadhub_field'                     => 'حقل LeadHub',
    'target_field_name'                 => 'اسم الحقل المستهدف',
    'target_field_placeholder'          => 'مثل email، FIRST_NAME، properties.email',
    'add_field_mapping'                 => 'إضافة تعيين حقل',
    'lh_field_first_name'               => 'الاسم الأول',
    'lh_field_last_name'                => 'اسم العائلة',
    'lh_field_email'                    => 'البريد الإلكتروني',
    'lh_field_phone'                    => 'الهاتف',
    'lh_field_company'                  => 'الشركة',
    'lh_field_source'                   => 'مصدر العميل المحتمل',
    'lh_field_status'                   => 'الحالة',
    'lh_field_lead_score'               => 'درجة العميل المحتمل',
    'lh_field_address'                  => 'العنوان',
    'lh_field_city'                     => 'المدينة',
    'lh_field_country'                  => 'الدولة',
    'lh_field_notes'                    => 'الملاحظات',

    // ─── Form: filter leads ────────────────────────────────────────────
    'filter_sources'                    => 'مزامنة العملاء المحتملين من هذه المصادر فقط (اترك فارغاً للجميع)',
    'filter_tags'                       => 'مزامنة العملاء المحتملين الذين يحملون هذه الوسوم فقط',

    // ─── Table columns ─────────────────────────────────────────────────
    'name'                              => 'الاسم',
    'category'                          => 'الفئة',
    'last_sync'                         => 'آخر مزامنة',
    'last_sync_never'                   => 'أبداً',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_status'               => 'الحالة',
    'status_connected'                  => 'متصل',
    'status_disconnected'               => 'غير متصل',
    'status_error'                      => 'خطأ / معطّل',
    'enabled'                           => 'مفعّل',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_test'                       => 'اختبار',
    'action_sync_logs'                  => 'سجلات المزامنة',
    'connection_successful'             => 'تم الاتصال بنجاح!',
    'connection_failed'                 => 'فشل الاتصال. تحقق من بيانات الاعتماد.',
    'error_prefix'                      => 'خطأ: ',

    // ─── Bulk actions ──────────────────────────────────────────────────
    'bulk_enable'                       => 'تفعيل المحدد',
    'bulk_disable'                      => 'تعطيل المحدد',

    // ─── ListIntegrations notifications ────────────────────────────────
    'notify_saved'                      => 'تم حفظ التكامل.',
    'notify_enabled'                    => 'تم تفعيل التكامل.',
    'notify_disabled'                   => 'تم تعطيل التكامل.',
    'notify_removed'                    => 'تمت إزالة التكامل.',

    // ─── Sync logs header actions ──────────────────────────────────────
    'retry_all_failed'                  => 'إعادة محاولة كل ما فشل',
    'back_to_integrations'              => 'العودة إلى التكاملات',
    'retrying_failed_syncs'             => 'تتم إعادة محاولة :count من عمليات المزامنة الفاشلة.',

    // ─── List page: modal + cards ──────────────────────────────────────
    'connection_settings_heading'       => 'إعدادات الاتصال',
    'modal_cancel'                      => 'إلغاء',
    'modal_save_integration'            => 'حفظ التكامل',
    'confirm_remove_integration'        => 'هل تريد إزالة هذا التكامل؟',

    // ─── Component: integration-config-notice ──────────────────────────
    'config_notice_note_label'          => 'ملاحظة:',
    'config_notice_body'                => 'بيانات الاعتماد مشفّرة عند التخزين. تكاملات OAuth (HubSpot، Salesforce، Zoho، Google Sheets) تتطلب رمز وصول من تدفّق OAuth الخاص بك. ألصقه في حقل رمز الوصول أعلاه.',

    // ─── List page: status pills ───────────────────────────────────────
    'status_connected_label'            => 'متصل',
    'status_error_label'                => 'خطأ',
    'status_inactive_label'             => 'غير نشط',
    'action_test_connection'            => 'اختبار الاتصال',
    'action_configure'                  => 'تهيئة',
    'action_sync_logs_title'            => 'سجلات المزامنة',
    'action_remove'                     => 'إزالة',
    'action_disable'                    => 'تعطيل',
    'action_enable'                     => 'تفعيل',
    'last_sync_prefix'                  => 'آخر مزامنة: :time',
    'btn_connect'                       => '+ اتصال',
    'setup_title_prefix'                => 'الإعداد: :type',

    // ─── List page: OAuth config ───────────────────────────────────────
    'oauth_connected_pill'              => 'متصل عبر OAuth',
    'oauth_reconnect'                   => 'إعادة الاتصال عبر OAuth',
    'oauth_connect'                     => 'الاتصال عبر OAuth',
    'oauth_hint'                        => 'أدخل Client ID و Secret أدناه أولاً، ثم انقر للتفويض.',

    // ─── List page: field mapping ──────────────────────────────────────
    'field_mapping_heading'             => 'تعيين الحقول',
    'field_optional_suffix'             => '(اختياري)',
    'field_mapping_desc'                => 'عيّن حقول :app إلى أسماء الحقول المستهدفة في التطبيق المتصل.',
    'select_source_field'               => 'حقل :app',
    'select_target_field'               => 'اختر الحقل المستهدف',
    'target_field_input_placeholder'    => 'اسم الحقل المستهدف',
    'btn_add_mapping'                   => '+ إضافة تعيين',

    // ─── List page: source filter ──────────────────────────────────────
    'source_filter_heading'             => 'مرشّح المصدر',
    'source_filter_desc'                => 'مزامنة العملاء المحتملين من هذه المصادر فقط (اترك فارغاً لمزامنة الكل).',

    // ─── List page: tag filter ─────────────────────────────────────────
    'tag_filter_heading'                => 'مرشّح الوسوم',
    'tag_filter_desc'                   => 'مزامنة العملاء المحتملين الذين يحملون وسماً واحداً على الأقل من هذه الوسوم (اترك فارغاً لمزامنة الكل).',
    'no_tags_created'                   => 'لم يتم إنشاء أي وسوم بعد.',

    // ─── List page: pipeline filter ────────────────────────────────────
    'pipeline_filter_heading'           => 'مرشّح خط الأنابيب',
    'pipeline_filter_desc'              => 'مزامنة العملاء المحتملين الموجودين حالياً في خطوط الأنابيب هذه فقط (اترك فارغاً لمزامنة الكل).',
    'no_pipelines_created'              => 'لم يتم إنشاء أي خطوط أنابيب بعد.',

    // ─── Sync logs page ────────────────────────────────────────────────
    'sync_logs_heading'                 => 'سجلات المزامنة — :name',
    'sync_logs_total'                   => ':count إجمالاً',
    'no_sync_logs'                      => 'لا توجد سجلات مزامنة بعد. ستتم مزامنة العملاء المحتملين هنا بمجرد تشغيل التكامل.',
    'col_lead'                          => 'العميل المحتمل',
    'col_event'                         => 'الحدث',
    'col_status'                        => 'الحالة',
    'col_attempts'                      => 'المحاولات',
    'col_time'                          => 'الوقت',
    'col_details'                       => 'التفاصيل',
    'btn_show'                          => 'إظهار',
    'btn_hide'                          => 'إخفاء',
    'detail_payload'                    => 'الحمولة',
    'detail_response'                   => 'الاستجابة',
];
