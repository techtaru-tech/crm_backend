<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — PipelineResource translation strings
|--------------------------------------------------------------------------
|
| Labels, table copy and empty-state copy for the Pipelines resource at
| /admin/pipelines.
| Consumed via __('filament/pipelines.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Pipelines',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Pipeline',
    'plural_model_label'                => 'Pipelines',

    // ─── Form ──────────────────────────────────────────────────────────
    'name'                              => 'Name',
    'description'                       => 'Description',
    'stage_name'                        => 'Stage Name',
    'stage_color'                       => 'Color',
    'created_at'                        => 'Created',
    'default_pipeline'                  => 'Default Pipeline',
    'win_stage'                         => 'Win Stage',
    'loss_stage'                        => 'Loss Stage',
    'add_stage'                         => 'Add Stage',

    // ─── Table columns ─────────────────────────────────────────────────
    'stages'                            => 'Stages',
    'leads'                             => 'Leads',
    'default'                           => 'Default',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'No pipelines yet',
    'empty_description'                 => 'Pipelines are the stages a deal moves through — e.g. New → Qualified → Proposal → Won. Create one to start tracking conversion.',
    'create_first_pipeline'             => 'Create first pipeline',
];
