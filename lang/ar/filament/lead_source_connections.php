<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadSourceConnectionResource translation strings
|--------------------------------------------------------------------------
|
| Labels, descriptions, helper text, placeholders and action copy for the
| Lead Sources resource at /admin/lead-source-connections.
| Consumed via __('filament/lead_source_connections.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'مصادر العملاء المحتملين',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'مصدر عملاء محتملين',
    'plural_model_label'                => 'مصادر العملاء المحتملين',

    // ─── Form fields & column labels ───────────────────────────────────
    'source'                            => 'المصدر',
    'active'                            => 'نشط',
    'col_name'                          => 'الاسم',
    'col_source'                        => 'المصدر',
    'col_status'                        => 'الحالة',

    // ─── Form: connection details ──────────────────────────────────────
    'connection_name'                   => 'اسم الاتصال',
    'connection_name_helper'            => 'مثل: «Facebook – الصفحة الرئيسية»',

    // ─── Form: webhook URL section ─────────────────────────────────────
    'your_webhook_url'                  => 'رابط Webhook الخاص بك',
    'webhook_url_placeholder_default'   => 'احفظ الاتصال أولًا للحصول على الرابط',
    'webhook_url_helper'                => 'انسخ هذا الرابط والصقه في إعدادات Webhook الخاصة بمنصة المصدر.',

    // ─── Form: OAuth authorization section ─────────────────────────────
    'oauth_description'                 => 'احفظ الاتصال أولًا، ثم اضغط «الاتصال عبر OAuth» من القائمة لتفويض الوصول. ستُخزَّن بيانات الاعتماد (رمز الوصول، رمز التحديث) تلقائيًا.',
    'oauth_instruction_text'            => 'بعد حفظ هذا الاتصال، استخدم زر «الاتصال عبر OAuth» في قائمة الاتصالات للتفويض. App ID / Client ID وApp Secret / Client Secret مطلوبان قبل إعادة التوجيه.',

    // ─── Form: API credentials section ─────────────────────────────────
    'credentials_description'           => 'املأ بيانات الاعتماد المطلوبة لهذا المصدر. تُخزَّن مشفّرة.',
    'credentials_select_source'         => 'اختر مصدرًا أعلاه لرؤية بيانات الاعتماد المطلوبة.',
    'credentials_none_required'         => 'لا تتطلب هذه المصدر بيانات اعتماد.',

    // ─── Form: message source settings ─────────────────────────────────
    'message_source_description'        => 'هيّئ كيفية التقاط الرسائل كعملاء محتملين.',
    'qualification_keywords'            => 'الكلمات المفتاحية للتأهيل',
    'qualification_keywords_helper'     => 'يُنشَأ عميل محتمل عندما يرسل شخص ما رسالة تحتوي على إحدى هذه الكلمات. اتركه فارغًا لالتقاط جميع الرسائل.',
    'qualification_keywords_placeholder' => 'أضف كلمة مفتاحية…',

    // ─── Form: Meta page selection ─────────────────────────────────────
    'meta_page_description'             => 'بعد الاتصال عبر OAuth، اختر صفحة Facebook/Instagram لاستخدامها لجلب العملاء المحتملين.',
    'active_page'                       => 'الصفحة النشطة',
    'active_page_helper'                => 'سيُستخدم رمز الوصول الخاص بهذه الصفحة لاسترجاع العملاء المحتملين من Meta Lead Ads API.',

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_description'         => 'عيّن حقول نموذج المصدر إلى حقول عملاء LeadHub المحتملين (first_name، last_name، email، phone).',
    'field_mapping'                     => 'تعيين الحقول',
    'source_field_name'                 => 'اسم حقل المصدر',
    'leadhub_field_value'               => 'حقل LeadHub (first_name / last_name / email / phone)',

    // ─── Table columns ─────────────────────────────────────────────────
    'leads'                             => 'العملاء المحتملون',
    'last_lead'                         => 'آخر عميل محتمل',
    'webhook_url'                       => 'رابط Webhook',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_source'               => 'المصدر',
    'filter_label_status'               => 'الحالة',
    'status_connected'                  => 'متصل',
    'status_disconnected'               => 'غير متصل',
    'status_error'                      => 'خطأ',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_connect_oauth'              => 'الاتصال عبر OAuth',
    'action_connect_oauth_tooltip'      => 'تفويض الوصول عبر OAuth لتعبئة بيانات الاعتماد تلقائيًا',
    'action_test'                       => 'اختبار',
    'test_failed_message'               => 'فشل اختبار الاتصال',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'لا توجد مصادر عملاء محتملين متصلة',
    'empty_description'                 => 'اتصل بمصدر عملاء محتملين لبدء التقاطهم.',
];
