<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Navigation group labels (Filament sidebar)
|--------------------------------------------------------------------------
|
| Labels for the navigation group dividers shown in the Filament
| sidebar.  The English strings here also serve as the IDENTIFIERS
| that Filament Page/Resource classes match against via their static
| $navigationGroup property — that's a Filament quirk, so the
| identifiers stay English while the displayed labels come from this
| file via NavigationGroup::make('Account')->label(__('navigation.groups.account')).
|
| Buyers translate or adapt by editing this file, or by copying it
| to lang/<locale>/navigation.php and translating the values.
|
*/

return [

    'groups' => [

        // --- Tenant admin panel -----------------------------------------
        'leads'              => 'Leads',
        'pipeline'           => 'Pipeline',
        'inbox'              => 'Inbox',
        'forms'              => 'Forms',
        'automations'        => 'Automations',
        'integrations'       => 'Integrations',
        'reports'            => 'Reports',
        'brand_and_domain'   => 'Brand & Domain',
        'communications'     => 'Communications',
        'team_and_access'    => 'Team & Access',
        'advanced'           => 'Advanced',
        'account'            => 'Account',
        'settings'           => 'Settings',
        'sales'              => 'Sales',
        'templates'          => 'Templates',
        'tools'              => 'Tools',

        // --- Super-admin panel ------------------------------------------
        'tenants'            => 'Tenants',
        'users'              => 'Users',
        'system'             => 'System',
        'billing'            => 'Billing',
        'marketing'          => 'Marketing',

    ],

    /*
    |--------------------------------------------------------------------------
    | User-menu items (top-right avatar dropdown)
    |--------------------------------------------------------------------------
    |
    | Labels for the entries rendered in the Filament panel user menu
    | (AdminPanelProvider->userMenuItems(...)).  Keep keys snake_case so
    | translators can match them 1:1 with the MenuItem definitions.
    |
    */
    'user_menu' => [
        'my_profile'          => 'My Profile',
        'customize_dashboard' => 'Customize Dashboard',
    ],

];
