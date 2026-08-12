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
    'nav_label' => 'لوحة Kanban',

    // ----- Pipeline selector -----
    'pipeline_label'         => 'خط الأنابيب:',

    // ----- Empty state -----
    'no_pipeline_configured' => 'لا يوجد خط أنابيب مُهيَّأ',
    'create_a_pipeline'      => 'أنشئ خط أنابيب',
    'to_get_started'         => 'للبدء.',

    // ----- Stage badges -----
    'badge_won'              => 'تم الكسب',
    'badge_lost'             => 'مفقود',

    // ----- العمود غير المُعيَّن -----
    'unassigned_label'       => 'غير مُعيَّن',
    'unassigned_hint'        => 'اسحب إلى مرحلة لتعيينه',

    // ----- Card meta -----
    'leads_suffix'           => 'عميل محتمل',
    'days_in_stage'          => ':days في المرحلة',

    // Localised "Nd" suffix used on kanban card footer for days-in-stage.
    // Routed through __() so non-English locales render their own
    // abbreviation instead of the literal English "d".
    'days_short_suffix'      => ':count ي',

    // ----- Echo toast (real-time lead moves) -----
    'toast_lead_moved'       => 'انتقل :lead_name إلى :to_stage',
    'default_lead_name'      => 'عميل محتمل',
    'default_stage_name'     => 'مرحلة جديدة',
];
