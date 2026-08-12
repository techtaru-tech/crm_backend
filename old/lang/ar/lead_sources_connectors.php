<?php

declare(strict_types=1);

return [

    'fallback_label' => ':label',

    'calendly' => [
        'personal_access_token' => 'رمز الوصول الشخصي',
        'webhook_signing_key'   => 'مفتاح توقيع Webhook',
        'organization_uri'      => 'URI المؤسسة',
    ],

    'email_imap' => [
        'host'     => 'مضيف IMAP',
        'port'     => 'منفذ IMAP (993 لـ SSL)',
        'username' => 'اسم مستخدم البريد الإلكتروني',
        'password' => 'كلمة مرور البريد الإلكتروني',
        'ssl'      => 'استخدم SSL',
    ],

    'google_ads' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'معرّف العميل',
        'webhook_secret'  => 'سر Webhook',
    ],

    'instagram' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'page_access_token'      => 'رمز الوصول للصفحة',
        'qualification_keywords' => 'كلمات التأهيل الرئيسية (مفصولة بفواصل)',
    ],

    'jotform' => [
        'api_key'       => 'API Key',
        'form_id'       => 'معرّف النموذج',
        'field_mapping' => 'تعيين الحقول (field_name => lead_field)',
    ],

    'linkedin' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'access_token'    => 'Access Token',
        'organization_id' => 'معرّف المؤسسة',
    ],

    'meta' => [
        'app_id'            => 'App ID',
        'app_secret'        => 'App Secret',
        'page_access_token' => 'رمز الوصول للصفحة (يُملأ تلقائياً عبر OAuth)',
        'page_id'           => 'معرّف الصفحة',
    ],

    'microsoft_ads' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'developer_token' => 'Developer Token',
        'account_id'      => 'معرّف الحساب',
        'webhook_secret'  => 'سر Webhook',
    ],

    'pinterest' => [
        'app_id'        => 'App ID',
        'app_secret'    => 'App Secret',
        'access_token'  => 'Access Token',
        'ad_account_id' => 'معرّف الحساب الإعلاني',
    ],

    'snapchat' => [
        'client_id'     => 'Client ID',
        'client_secret' => 'Client Secret',
        'app_secret'    => 'App Secret (للتحقق من التوقيع)',
        'access_token'  => 'Access Token',
    ],

    'telegram' => [
        'bot_token'              => 'رمز البوت',
        'webhook_secret'         => 'سر Webhook',
        'qualification_keywords' => 'كلمات التأهيل الرئيسية',
    ],

    'tiktok' => [
        'client_key'    => 'Client Key (App ID)',
        'client_secret' => 'Client Secret (App Secret)',
        'access_token'  => 'Access Token (يُملأ تلقائياً عبر OAuth)',
    ],

    'twitter' => [
        'consumer_key'    => 'Consumer Key (API Key)',
        'consumer_secret' => 'Consumer Secret',
        'access_token'    => 'Access Token',
        'access_secret'   => 'Access Token Secret',
    ],

    'typeform' => [
        'access_token'   => 'Access Token',
        'webhook_secret' => 'سر Webhook',
        'field_mapping'  => 'تعيين الحقول (field_ref => lead_field)',
    ],

    'viber' => [
        'auth_token'             => 'Auth Token',
        'bot_name'               => 'اسم البوت',
        'qualification_keywords' => 'كلمات التأهيل الرئيسية',
    ],

    'whatsapp' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'phone_number_id'        => 'معرّف رقم الهاتف',
        'access_token'           => 'Access Token',
        'qualification_keywords' => 'كلمات التأهيل الرئيسية',
    ],

    'youtube' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'معرّف العميل',
        'channel_id'      => 'معرّف قناة YouTube',
        'webhook_secret'  => 'سر Webhook',
    ],

];
