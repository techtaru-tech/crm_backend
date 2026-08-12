<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Kanban Board page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/kanban_board.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label' => 'Kanban Board',

    // ----- Pipeline selector -----
    'pipeline_label'         => 'Pipeline:',

    // ----- Empty state -----
    'no_pipeline_configured' => 'No pipeline configured',
    'create_a_pipeline'      => 'Create a pipeline',
    'to_get_started'         => 'to get started.',

    // ----- Stage badges -----
    'badge_won'              => 'Won',
    'badge_lost'             => 'Lost',

    // ----- Unassigned column -----
    'unassigned_label'       => 'Unassigned',
    'unassigned_hint'        => 'Drag into a stage to assign',

    // ----- Card meta -----
    'leads_suffix'           => 'leads',
    'days_in_stage'          => ':days in stage',

    // Localised "Nd" suffix used on kanban card footer for days-in-stage.
    // Routed through __() so non-English locales render their own
    // abbreviation instead of the literal English "d".
    'days_short_suffix'      => ':count d',

    // ----- Echo toast (real-time lead moves) -----
    'toast_lead_moved'       => ':lead_name moved to :to_stage',
    'default_lead_name'      => 'Lead',
    'default_stage_name'     => 'new stage',
];
