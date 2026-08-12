<?php

declare(strict_types=1);

return [

    'fallback_label' => ':label',

    'calendly' => [
        'personal_access_token' => 'व्यक्तिगत Access Token',
        'webhook_signing_key'   => 'वेबहुक हस्ताक्षर कुंजी',
        'organization_uri'      => 'संगठन URI',
    ],

    'email_imap' => [
        'host'     => 'IMAP होस्ट',
        'port'     => 'IMAP पोर्ट (SSL के लिए 993)',
        'username' => 'ईमेल उपयोगकर्ता नाम',
        'password' => 'ईमेल पासवर्ड',
        'ssl'      => 'SSL का उपयोग करें',
    ],

    'google_ads' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'ग्राहक ID',
        'webhook_secret'  => 'वेबहुक रहस्य',
    ],

    'instagram' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'page_access_token'      => 'पेज Access Token',
        'qualification_keywords' => 'योग्यता कीवर्ड (अल्पविराम-पृथक)',
    ],

    'jotform' => [
        'api_key'       => 'API Key',
        'form_id'       => 'फ़ॉर्म ID',
        'field_mapping' => 'फ़ील्ड मैपिंग (field_name => lead_field)',
    ],

    'linkedin' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'access_token'    => 'Access Token',
        'organization_id' => 'संगठन ID',
    ],

    'meta' => [
        'app_id'            => 'App ID',
        'app_secret'        => 'App Secret',
        'page_access_token' => 'पेज Access Token (OAuth के माध्यम से स्वतः भरा जाता है)',
        'page_id'           => 'पेज ID',
    ],

    'microsoft_ads' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'developer_token' => 'Developer Token',
        'account_id'      => 'खाता ID',
        'webhook_secret'  => 'वेबहुक रहस्य',
    ],

    'pinterest' => [
        'app_id'        => 'App ID',
        'app_secret'    => 'App Secret',
        'access_token'  => 'Access Token',
        'ad_account_id' => 'विज्ञापन खाता ID',
    ],

    'snapchat' => [
        'client_id'     => 'Client ID',
        'client_secret' => 'Client Secret',
        'app_secret'    => 'App Secret (हस्ताक्षर सत्यापन के लिए)',
        'access_token'  => 'Access Token',
    ],

    'telegram' => [
        'bot_token'              => 'बॉट टोकन',
        'webhook_secret'         => 'वेबहुक रहस्य',
        'qualification_keywords' => 'योग्यता कीवर्ड',
    ],

    'tiktok' => [
        'client_key'    => 'Client Key (App ID)',
        'client_secret' => 'Client Secret (App Secret)',
        'access_token'  => 'Access Token (OAuth के माध्यम से स्वतः भरा जाता है)',
    ],

    'twitter' => [
        'consumer_key'    => 'Consumer Key (API Key)',
        'consumer_secret' => 'Consumer Secret',
        'access_token'    => 'Access Token',
        'access_secret'   => 'Access Token Secret',
    ],

    'typeform' => [
        'access_token'   => 'Access Token',
        'webhook_secret' => 'वेबहुक रहस्य',
        'field_mapping'  => 'फ़ील्ड मैपिंग (field_ref => lead_field)',
    ],

    'viber' => [
        'auth_token'             => 'Auth Token',
        'bot_name'               => 'बॉट नाम',
        'qualification_keywords' => 'योग्यता कीवर्ड',
    ],

    'whatsapp' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'phone_number_id'        => 'फ़ोन नंबर ID',
        'access_token'           => 'Access Token',
        'qualification_keywords' => 'योग्यता कीवर्ड',
    ],

    'youtube' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'ग्राहक ID',
        'channel_id'      => 'YouTube चैनल ID',
        'webhook_secret'  => 'वेबहुक रहस्य',
    ],

];
