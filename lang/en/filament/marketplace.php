<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — MarketplacePage translation strings
|--------------------------------------------------------------------------
|
| Notification copy for the in-app template marketplace at /admin/marketplace.
| Consumed via __('filament/marketplace.<key>').
|
*/

return [

    // ─── Navigation / page header ─────────────────────────────────────
    'nav_label'                 => 'Marketplace',
    'title'                     => 'Templates Marketplace',
    'heading'                   => 'Templates Marketplace',
    'subheading'                => 'Install pre-built automations, funnels, email sequences, and forms shared by other workspaces. Featured items vetted by the operator.',

    // ─── Install flow notifications ───────────────────────────────────
    'notif_not_found_title'     => 'Template not found or unpublished.',
    'notif_no_workspace_title'  => 'No workspace context.',
    'notif_paid_title'          => 'Paid templates not yet supported',
    'notif_paid_body'           => 'This template costs :price :currency. Paid checkout flow lands in a follow-up.',
    'notif_installed_title'     => 'Template installed',
    'notif_installed_body'      => 'Created :count record(s) of type :type. Find it under the matching section — review the content before activating.',
    'notif_install_failed_title' => 'Install failed',
    'notif_install_unknown_error' => 'Unknown error',

    // wire:confirm
    'confirm_install_template'  => "Install ':name' into your workspace? You'll find it under the matching section, set to draft so you can review before activating.",

    // ─── Page body (resources/views/filament/pages/marketplace.blade.php) ──
    'search_placeholder'        => 'Search templates by name or description…',
    'all_types'                 => 'All types',
    'all_categories'            => 'All categories',
    'featured_only'             => 'Featured only',
    'empty_no_matches_title'    => 'No matches',
    'empty_no_templates_title'  => 'Marketplace is empty',
    'empty_no_matches_body'     => 'Try removing filters or different search terms.',
    'empty_no_templates_body'   => 'No templates have been published yet. Be the first — build a template and share it from your automation / form / email sequence editor.',
    'templates_count'           => '{1} :count template available|[2,*] :count templates available',
    'featured_tag'              => '⭐ Featured',
    'by_owner_prefix'           => 'by',
    'installs_count'            => '{1} :count install|[2,*] :count installs',
    'free_price'                => 'Free',
    'install_free_btn'          => 'Install free',
    'install_paid_btn'          => 'Install — :price :currency',
];
