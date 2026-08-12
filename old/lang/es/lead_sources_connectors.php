<?php

declare(strict_types=1);

return [

    'fallback_label' => ':label',

    'calendly' => [
        'personal_access_token' => 'Token de acceso personal',
        'webhook_signing_key'   => 'Clave de firma del Webhook',
        'organization_uri'      => 'URI de la organización',
    ],

    'email_imap' => [
        'host'     => 'Host IMAP',
        'port'     => 'Puerto IMAP (993 para SSL)',
        'username' => 'Usuario de Email',
        'password' => 'Contraseña de Email',
        'ssl'      => 'Usar SSL',
    ],

    'google_ads' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'ID de cliente',
        'webhook_secret'  => 'Secreto del Webhook',
    ],

    'instagram' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'page_access_token'      => 'Token de acceso de la página',
        'qualification_keywords' => 'Palabras clave de calificación (separadas por comas)',
    ],

    'jotform' => [
        'api_key'       => 'API Key',
        'form_id'       => 'ID del formulario',
        'field_mapping' => 'Mapeo de campos (field_name => lead_field)',
    ],

    'linkedin' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'access_token'    => 'Access Token',
        'organization_id' => 'ID de la organización',
    ],

    'meta' => [
        'app_id'            => 'App ID',
        'app_secret'        => 'App Secret',
        'page_access_token' => 'Token de acceso de la página (se completa automáticamente vía OAuth)',
        'page_id'           => 'ID de la página',
    ],

    'microsoft_ads' => [
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'developer_token' => 'Developer Token',
        'account_id'      => 'ID de la cuenta',
        'webhook_secret'  => 'Secreto del Webhook',
    ],

    'pinterest' => [
        'app_id'        => 'App ID',
        'app_secret'    => 'App Secret',
        'access_token'  => 'Access Token',
        'ad_account_id' => 'ID de cuenta publicitaria',
    ],

    'snapchat' => [
        'client_id'     => 'Client ID',
        'client_secret' => 'Client Secret',
        'app_secret'    => 'App Secret (para verificación de firma)',
        'access_token'  => 'Access Token',
    ],

    'telegram' => [
        'bot_token'              => 'Token del bot',
        'webhook_secret'         => 'Secreto del Webhook',
        'qualification_keywords' => 'Palabras clave de calificación',
    ],

    'tiktok' => [
        'client_key'    => 'Client Key (App ID)',
        'client_secret' => 'Client Secret (App Secret)',
        'access_token'  => 'Access Token (se completa automáticamente vía OAuth)',
    ],

    'twitter' => [
        'consumer_key'    => 'Consumer Key (API Key)',
        'consumer_secret' => 'Consumer Secret',
        'access_token'    => 'Access Token',
        'access_secret'   => 'Access Token Secret',
    ],

    'typeform' => [
        'access_token'   => 'Access Token',
        'webhook_secret' => 'Secreto del Webhook',
        'field_mapping'  => 'Mapeo de campos (field_ref => lead_field)',
    ],

    'viber' => [
        'auth_token'             => 'Auth Token',
        'bot_name'               => 'Nombre del bot',
        'qualification_keywords' => 'Palabras clave de calificación',
    ],

    'whatsapp' => [
        'app_id'                 => 'App ID',
        'app_secret'             => 'App Secret',
        'phone_number_id'        => 'ID del número de teléfono',
        'access_token'           => 'Access Token',
        'qualification_keywords' => 'Palabras clave de calificación',
    ],

    'youtube' => [
        'developer_token' => 'Developer Token',
        'client_id'       => 'Client ID',
        'client_secret'   => 'Client Secret',
        'refresh_token'   => 'Refresh Token',
        'customer_id'     => 'ID de cliente',
        'channel_id'      => 'ID del canal de YouTube',
        'webhook_secret'  => 'Secreto del Webhook',
    ],

];
