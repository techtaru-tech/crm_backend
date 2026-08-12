<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Visual Flow Builder — canvas UI strings
|------------------------------------------------------------
| Used by the automation-flow-builder blade: PHP chrome via __() and the
| Drawflow canvas JS via the @js($flowStrings) bridge, so the whole builder
| is translatable (LeadHub ships in many locales).
*/

return [
    // Toolbar
    'add_step'        => 'Add step:',
    'add_action'      => '+ Action',
    'add_condition'   => '+ Condition',
    'add_delay'       => '+ Delay',
    'delete_selected' => 'Delete selected',
    'save_flow'       => 'Save flow',

    // Inspector
    'step_settings'   => 'Step settings',
    'inspector_hint'  => 'Fine-tune advanced action settings in the classic editor; this canvas controls the flow shape and the primary type of each step.',
    'field_trigger'   => 'Trigger',
    'field_action'    => 'Action',
    'field_condition' => 'Condition',
    'field_value'     => 'Value',
    'field_amount'    => 'Amount',
    'field_unit'      => 'Unit',

    // Canvas node summaries
    'summary_when'      => 'When:',
    'summary_do'        => 'Do:',
    'summary_if'        => 'If:',
    'summary_wait'      => 'Wait:',
    // The engine treats a condition as a GATE: matches -> continue, else the
    // run stops. This makes that honest on the canvas (no faked "else" path).
    'summary_else_stop' => '— else stop',

    // Delay units
    'unit_minutes'    => 'Minutes',
    'unit_hours'      => 'Hours',
    'unit_days'       => 'Days',
];
