<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Plan catalog — display labels
|--------------------------------------------------------------------------
| Mirrors the display fields of config/plans.php (and the fallback catalog
| inside database/seeders/PlansSeeder.php) so the same keys can be
| translated per-locale without changing the structural config.
|
| Consumer: PlanService legacy-array build path translates `name` and
| `description` via __('plans.<key>.name') and __('plans.<key>.description')
| with translator-first-with-fallback to the original config / DB value.
|
| Keys must match the plan `key` column (trial, starter, pro,
| professional, business, enterprise) so PlanResource-created custom plans
| naturally bypass translation (the translator returns the raw key,
| triggering the fallback to the buyer's typed value).
*/

return [

    'trial' => [
        'name'        => 'Trial',
        'description' => 'Try LeadHub free for 14 days. No credit card required.',
    ],

    'starter' => [
        'name'        => 'Starter',
        'description' => 'Everything a small team needs to capture and manage leads.',
    ],

    // Config catalog uses the `pro` key.
    'pro' => [
        'name'        => 'Pro',
        'description' => 'For growing teams that need automation, integrations, and branding.',
    ],

    // Seeder fallback catalog uses the `professional` key.
    'professional' => [
        'name'        => 'Professional',
        'description' => 'For growing sales organizations that need advanced automations, AI-powered insights, and full white-label control — without enterprise-grade overhead.',
    ],

    'business' => [
        'name'        => 'Business',
        'description' => 'Scale confidently with priority support, unlimited automations, SSO/SAML sign-in, and custom audit-log retention for regulated industries.',
    ],

    'enterprise' => [
        'name'        => 'Enterprise',
        'description' => 'Unlimited everything. For large teams and agencies.',
    ],

    // Extra long-form copy that the seeder's fallback catalog uses for
    // the same keys when config/plans.php is empty on a fresh install.
    // PlanService prefers the per-key entry above; these are reserved for
    // future buyer-facing copy variants.
    'seeder' => [
        'trial' => [
            'name'        => 'Free Trial',
            'description' => '14 days free. No card required. Full Professional feature set so teams can evaluate the product before committing.',
        ],
        'starter' => [
            'name'        => 'Starter',
            'description' => 'Built for solo founders and small teams closing their first 100 deals. Everything you need to capture, qualify, and nurture leads end to end.',
        ],
        'enterprise' => [
            'name'        => 'Enterprise',
            'description' => 'Tailored for Fortune-grade teams. Unlimited seats, dedicated infrastructure, custom SLA, on-prem or private-cloud deployment, and a named CSM.',
        ],
    ],
];
