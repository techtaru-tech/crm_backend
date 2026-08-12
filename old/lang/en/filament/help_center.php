<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| HelpCenterPage — Filament page strings
|------------------------------------------------------------
| Accessed via __('filament/help_center.<key>').
|
| The tags.* sub-array holds slug → display label pairs used by
| App\Filament\Pages\HelpCenterPage::articles(). Article titles,
| bodies, and category labels remain under
| lang/<locale>/help_center_articles.php (legacy file).
|
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/help_center.php.
*/

return [

    // ----- Article tags (snake_case slug => display label) -----
    'tags' => [
        // Getting started
        'leads'         => 'leads',
        'first_lead'    => 'first lead',
        'onboarding'    => 'onboarding',
        'import'        => 'import',
        'csv'           => 'csv',
        'migration'     => 'migration',
        'branding'      => 'branding',
        'white_label'   => 'white-label',
        'logo'          => 'logo',

        // Forms
        'forms'         => 'forms',
        'embed'         => 'embed',
        'website'       => 'website',
        'utm'           => 'utm',
        'prefill'       => 'prefill',

        // Pipelines
        'pipeline'      => 'pipeline',
        'stages'        => 'stages',
        'forecast'      => 'forecast',
        'teams'         => 'teams',

        // Automations
        'automation'    => 'automation',
        'workflow'      => 'workflow',
        'manual'        => 'manual',
        'testing'       => 'testing',

        // Email
        'smtp'          => 'smtp',
        'email'         => 'email',
        'configuration' => 'configuration',
        'deliverability' => 'deliverability',
        'spam'          => 'spam',

        // Billing
        'billing'       => 'billing',
        'card'          => 'card',
        'payment'       => 'payment',
        'invoices'      => 'invoices',
        'receipts'      => 'receipts',
        'cancellation'  => 'cancellation',

        // Privacy
        'gdpr'          => 'gdpr',
        'export'        => 'export',
        'data'          => 'data',
        'deletion'      => 'deletion',

        // Team
        'team'          => 'team',
        'invite'        => 'invite',
        'members'       => 'members',
        'roles'         => 'roles',
        'permissions'   => 'permissions',
    ],
];
