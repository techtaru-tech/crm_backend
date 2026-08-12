<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — TrackingSnippetResource translation strings
|--------------------------------------------------------------------------
|
| Labels, placeholders, helper text and modal copy for the
| Web Tracking resource at /admin/tracking-snippets.
| Consumed via __('filament/tracking_snippets.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                    => 'Web Tracking',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                  => 'Tracking Snippet',
    'plural_model_label'           => 'Tracking Snippets',

    // ─── Form ──────────────────────────────────────────────────────────
    'name'                         => 'Name',
    'allowed_domains'              => 'Allowed Domains',
    'created_at'                   => 'Created',
    'name_placeholder'             => 'e.g. Main website',
    'allowed_domains_placeholder'  => 'example.com',
    'allowed_domains_helper'       => 'Restrict tracking to these domains (leave empty to allow any).',
    'active'                       => 'Active',

    // ─── Table columns ─────────────────────────────────────────────────
    'col_id'                       => 'ID',
    'col_views'                    => 'Views',
    'col_last_view'                => 'Last View',

    // ─── Get Snippet action ────────────────────────────────────────────
    'get_snippet'                  => 'Get Snippet',
    'snippet_modal_heading'        => 'Embed Tracking Snippet',
    'snippet_modal_description'    => 'Copy this snippet into the <head> of every page you want to track.',
    'snippet_modal_close'          => 'Close',
    'snippet_embed_label'          => 'Embed Code',
    'snippet_identify_intro'       => 'To link a visitor to a known lead after signup or login, call:',
];
