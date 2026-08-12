<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Cmd-K command palette (resources/views/filament/cmd-k.blade.php)
|------------------------------------------------------------
| Accessed via __('filament/cmd_k.<key>').
|
| Strings are emitted via a JSON island in the Blade view and
| consumed by public/js/views/filament/cmd-k.js. Keep keys in
| sync with both the view and the JS factory.
*/

return [

    // ─── Search bar ───────────────────────────────────────────────
    'placeholder'      => 'Type to search — leads, pipelines, settings…',
    'no_matches_for'   => 'No matches for ":query".',

    // ─── Footer hint labels ───────────────────────────────────────
    'kbd_navigate'     => 'navigate',
    'kbd_open'         => 'open',
    'kbd_toggle'       => 'toggle',
    'kbd_escape_key'   => 'esc',

    // ─── Section labels ───────────────────────────────────────────
    'section_pages'    => 'Pages',
    'section_account'  => 'Account',
    'section_settings' => 'Settings',
    'section_actions'  => 'Actions',

    // ─── Command titles ───────────────────────────────────────────
    'cmd_leads'              => 'Leads',
    'cmd_kanban_board'       => 'Kanban Board',
    'cmd_pipelines'          => 'Pipelines',
    'cmd_forms'              => 'Forms',
    'cmd_landing_pages'      => 'Landing Pages',
    'cmd_automations'        => 'Automations',
    'cmd_email_templates'    => 'Email Templates',
    'cmd_companies'          => 'Companies',
    'cmd_quotes'             => 'Quotes',
    'cmd_invoices'           => 'Invoices',
    'cmd_calendar'           => 'Calendar',
    'cmd_conversations'      => 'Conversations',
    'cmd_marketplace'        => 'Marketplace',
    'cmd_industry_packs'     => 'Industry Packs',
    'cmd_reports'            => 'Reports',
    'cmd_help_faq'           => 'Help & FAQ',
    'cmd_billing'            => 'Billing',
    'cmd_privacy_data'       => 'Privacy & Data',
    'cmd_two_factor_auth'    => 'Two-Factor Auth',
    'cmd_profile'            => 'Profile',
    'cmd_settings_branding'  => 'Settings — Branding',
    'cmd_settings_email'     => 'Settings — Email',
    'cmd_settings_team'      => 'Settings — Team',
    'cmd_new_lead'           => 'New Lead',
    'cmd_new_form'           => 'New Form',
    'cmd_new_automation'     => 'New Automation',

    // ─── Keyword strings (space-separated for substring match) ────
    'kw_leads'              => 'lead contacts customers',
    'kw_kanban'             => 'pipeline board kanban',
    'kw_pipelines'          => 'pipeline stages',
    'kw_forms'              => 'forms capture submission',
    'kw_landing_pages'      => 'landing marketing',
    'kw_automations'        => 'automation workflow',
    'kw_email_templates'    => 'email template',
    'kw_companies'          => 'company organization',
    'kw_quotes'             => 'quote proposal',
    'kw_invoices'           => 'invoice payment',
    'kw_calendar'           => 'calendar meetings booking',
    'kw_conversations'      => 'inbox conversations chat',
    'kw_marketplace'        => 'marketplace templates',
    'kw_industry_packs'     => 'industry pack vertical',
    'kw_reports'            => 'reports analytics',
    'kw_help_faq'           => 'help faq support',
    'kw_billing'            => 'billing plan upgrade',
    'kw_privacy_data'       => 'privacy gdpr export delete',
    'kw_two_factor_auth'    => 'two factor 2fa security',
    'kw_profile'            => 'profile account user',
    'kw_settings_branding'  => 'branding logo color',
    'kw_settings_email'     => 'email smtp settings',
    'kw_settings_team'      => 'team seats invite',
    'kw_new_lead'           => 'create new lead',
    'kw_new_form'           => 'create new form',
    'kw_new_automation'     => 'create new automation',
];
