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
        'leads'         => 'clientes potenciales',
        'first_lead'    => 'primer cliente potencial',
        'onboarding'    => 'incorporación',
        'import'        => 'importación',
        'csv'           => 'csv',
        'migration'     => 'migración',
        'branding'      => 'marca',
        'white_label'   => 'marca blanca',
        'logo'          => 'logotipo',

        // Forms
        'forms'         => 'formularios',
        'embed'         => 'inserción',
        'website'       => 'sitio web',
        'utm'           => 'utm',
        'prefill'       => 'autorrelleno',

        // Pipelines
        'pipeline'      => 'embudo',
        'stages'        => 'etapas',
        'forecast'      => 'previsión',
        'teams'         => 'equipos',

        // Automations
        'automation'    => 'automatización',
        'workflow'      => 'flujo de trabajo',
        'manual'        => 'manual',
        'testing'       => 'pruebas',

        // Email
        'smtp'          => 'smtp',
        'email'         => 'correo electrónico',
        'configuration' => 'configuración',
        'deliverability' => 'entregabilidad',
        'spam'          => 'spam',

        // Billing
        'billing'       => 'facturación',
        'card'          => 'tarjeta',
        'payment'       => 'pago',
        'invoices'      => 'facturas',
        'receipts'      => 'recibos',
        'cancellation'  => 'cancelación',

        // Privacy
        'gdpr'          => 'gdpr',
        'export'        => 'exportación',
        'data'          => 'datos',
        'deletion'      => 'eliminación',

        // Team
        'team'          => 'equipo',
        'invite'        => 'invitación',
        'members'       => 'miembros',
        'roles'         => 'roles',
        'permissions'   => 'permisos',
    ],
];
