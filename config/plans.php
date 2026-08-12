<?php

/*
|--------------------------------------------------------------------------
| LeadHub Plan Matrix
|--------------------------------------------------------------------------
| Defines the features and limits for each subscription plan. Super Admins
| can override per-tenant via the Tenant edit page, but these are the
| defaults applied when a tenant is first created.
|
| Limit conventions:
|   -1 = unlimited
|    0 = feature disabled
|   >0 = hard limit
|--------------------------------------------------------------------------
*/

return [

    'default' => env('LEADHUB_DEFAULT_PLAN', 'trial'),

    'trial_days' => env('LEADHUB_TRIAL_DAYS', 14),

    'plans' => [

        'trial' => [
            'name'         => 'Trial',
            'description'  => 'Try LeadHub free for 14 days. No credit card required.',
            'price'        => 0,
            'interval'     => 'month',
            'is_public'    => false,
            'sort'         => 0,
            'limits' => [
                'max_seats'                      => 3,
                'max_leads'                      => 250,
                'max_forms'                      => 3,
                'max_pipelines'                  => 2,
                'max_automations'                => 2,
                'max_integrations'               => 1,
                'max_api_keys'                   => 1,
                'max_custom_domains'             => 0,
                'marketplace_installs_per_month' => 3,
            ],
            'features' => [
                'integrations'        => false,
                'automations'         => true,
                'api_access'          => false,
                'custom_domain'       => false,
                'white_label'         => false,
                'webhooks_outbound'   => false,
                'reports_advanced'    => false,
                'priority_support'    => false,
                'marketplace_install' => true,
            ],
        ],

        'starter' => [
            'name'         => 'Starter',
            'description'  => 'Everything a small team needs to capture and manage leads.',
            'price'        => 29,
            'interval'     => 'month',
            'is_public'    => true,
            'sort'         => 1,
            'limits' => [
                'max_seats'                      => 5,
                'max_leads'                      => 2_000,
                'max_forms'                      => 10,
                'max_pipelines'                  => 5,
                'max_automations'                => 5,
                'max_integrations'               => 3,
                'max_api_keys'                   => 2,
                'max_custom_domains'             => 0,
                'marketplace_installs_per_month' => 10,
            ],
            'features' => [
                'integrations'        => true,
                'automations'         => true,
                'api_access'          => true,
                'custom_domain'       => false,
                'white_label'         => false,
                'webhooks_outbound'   => false,
                'reports_advanced'    => false,
                'priority_support'    => false,
                'marketplace_install' => true,
            ],
        ],

        'pro' => [
            'name'         => 'Pro',
            'description'  => 'For growing teams that need automation, integrations, and branding.',
            'price'        => 79,
            'interval'     => 'month',
            'is_public'    => true,
            'sort'         => 2,
            'highlight'    => true,
            'limits' => [
                'max_seats'                      => 15,
                'max_leads'                      => 20_000,
                'max_forms'                      => 50,
                'max_pipelines'                  => 20,
                'max_automations'                => 25,
                'max_integrations'               => 15,
                'max_api_keys'                   => 10,
                'max_custom_domains'             => 1,
                'marketplace_installs_per_month' => 50,
            ],
            'features' => [
                'integrations'        => true,
                'automations'         => true,
                'api_access'          => true,
                'custom_domain'       => true,
                'white_label'         => true,
                'webhooks_outbound'   => true,
                'reports_advanced'    => true,
                'priority_support'    => false,
                'marketplace_install' => true,
            ],
        ],

        'enterprise' => [
            'name'         => 'Enterprise',
            'description'  => 'Unlimited everything. For large teams and agencies.',
            'price'        => 199,
            'interval'     => 'month',
            'is_public'    => true,
            'sort'         => 3,
            'limits' => [
                'max_seats'                      => -1,
                'max_leads'                      => -1,
                'max_forms'                      => -1,
                'max_pipelines'                  => -1,
                'max_automations'                => -1,
                'max_integrations'               => -1,
                'max_api_keys'                   => -1,
                'max_custom_domains'             => -1,
                'marketplace_installs_per_month' => -1,
            ],
            'features' => [
                'integrations'        => true,
                'automations'         => true,
                'api_access'          => true,
                'custom_domain'       => true,
                'white_label'         => true,
                'webhooks_outbound'   => true,
                'reports_advanced'    => true,
                'priority_support'    => true,
                'marketplace_install' => true,
            ],
        ],

    ],

];
