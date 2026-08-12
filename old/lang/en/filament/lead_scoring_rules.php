<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadScoringRuleResource translation strings
|--------------------------------------------------------------------------
|
| Helper-text strings for the Lead Scoring Rules resource.
| Consumed via __('filament/lead_scoring_rules.<key>').
|
*/

return [
    'nav_label'     => 'Scoring Rules',
    'model_label'        => 'Scoring Rule',
    'plural_model_label' => 'Scoring Rules',
    'points_helper' => 'Negative values subtract points.',

    // ─── Form fields ───────────────────────────────────────────────
    'name'          => 'Name',
    'field'         => 'Field',
    'operator'      => 'Operator',
    'value'         => 'Value',
    'points'        => 'Points',
    'active'        => 'Active',

    // ─── Select options ────────────────────────────────────────────
    'option_field_email'      => 'Email',
    'option_field_phone'      => 'Phone',
    'option_field_source'     => 'Source',
    'option_field_status'     => 'Status',
    'option_field_first_name' => 'First Name',
    'option_field_last_name'  => 'Last Name',
    'option_op_equals'        => 'Equals',
    'option_op_not_equals'    => 'Does not equal',
    'option_op_contains'      => 'Contains',
    'option_op_is_present'    => 'Is present',
    'option_op_is_absent'     => 'Is absent',
    'option_op_greater_than'  => 'Greater than',
    'option_op_less_than'     => 'Less than',
    'placeholder_any'         => '(any)',
];
