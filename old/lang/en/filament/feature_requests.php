<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — FeatureRequestResource translation strings
|--------------------------------------------------------------------------
|
| Labels, column copy and action copy for the Feature Requests resource
| at /admin/feature-requests.
| Consumed via __('filament/feature_requests.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Feature Requests',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Feature Request',
    'plural_model_label'                => 'Feature Requests',

    // ─── Table column labels ──────────────────────────────────────────
    'col_title'                         => 'Title',
    'col_status'                        => 'Status',

    // ─── Form ──────────────────────────────────────────────────────────
    'title'                             => 'Title',
    'description'                       => 'Description',
    'status'                            => 'Status',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_status'               => 'Status',

    // ─── Table columns ─────────────────────────────────────────────────
    'votes'                             => 'Votes',
    'submitted_by'                      => 'Submitted by',
    'when'                              => 'When',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_upvote'                     => 'Upvote',
    'action_remove_vote'                => 'Remove vote',
    'vote_updated'                      => 'Vote updated.',
    'submit_request'                    => 'Submit Request',
];
