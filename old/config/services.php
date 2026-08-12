<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth (Calendar 2-way sync)
    |--------------------------------------------------------------------------
    | Read by GoogleCalendarSyncService for the per-user OAuth flow.
    | Operator creates an OAuth client at https://console.cloud.google.com/
    | with redirect URI https://your-domain/admin/calendar/oauth/callback
    | and scopes calendar.events + userinfo.email.
    */
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Microsoft (Outlook Calendar 2-way sync via Microsoft Graph)
    |--------------------------------------------------------------------------
    | Read by OutlookCalendarSyncService.  Operator registers an app at
    | https://entra.microsoft.com/ with redirect URI
    | https://your-domain/admin/calendar/oauth/callback and API
    | permissions: Calendars.ReadWrite, User.Read, offline_access.
    */
    'microsoft' => [
        'client_id'     => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing — Operator-side Tax Configuration
    |--------------------------------------------------------------------------
    | Read by TaxCalculator + StripeGateway::resolveTaxRate.
    | operator_country = ISO-3166 alpha-2 of where the operator is
    | tax-registered (e.g. GR for Greece).  Drives EU MOSS / reverse-
    | charge logic for SaaS-side invoices.
    */
    'billing' => [
        'operator_country' => env('OPERATOR_COUNTRY'),
    ],

];
