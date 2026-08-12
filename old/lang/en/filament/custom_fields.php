<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| CustomFieldDefinitionResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/custom_fields.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'              => 'Custom Fields',
    'model_label'            => 'Custom Field',
    'plural_model_label'     => 'Custom Fields',

    // ----- Section descriptions -----
    'definition_description' => 'Define a custom field that applies to Leads or Companies.',
    'options_description'    => 'Add the choices users can pick from.',

    // ----- Definition fields -----
    'applies_to'        => 'Applies To',
    'field_label'       => 'Field Label',
    'key_identifier'    => 'Key / Identifier',
    'key_help'          => 'Used as the JSON key. Lowercase letters, numbers, and underscores only.',
    'field_type'        => 'Field Type',
    'placeholder'       => 'Placeholder',
    'helper_text'       => 'Helper Text',

    // ----- Options -----
    'options'           => 'Options',
    'options_help'      => 'Press Enter after each option. Applies to Select and Multi-Select fields.',

    // ----- Visibility & Behavior -----
    'required'          => 'Required',
    'show_in_form'      => 'Show in Create/Edit Form',
    'show_in_table'     => 'Show in Table',
    'show_in_filters'   => 'Show in Filters',
    'sort_order'        => 'Sort Order',

    // ----- Table -----
    'col_entity'        => 'Entity',
    'col_field'         => 'Field',
    'col_key'           => 'Key',
    'col_type'          => 'Type',
    'col_req'           => 'Req.',
    'col_form'          => 'Form',
    'col_table'         => 'Table',
    'col_filters'       => 'Filters',
    'col_order'         => 'Order',

    // ----- Filter labels -----
    'filter_entity'     => 'Entity',
    'filter_type'       => 'Type',

    // ─── Select options ────────────────────────────────────────────
    'option_entity_lead'    => 'Lead',
    'option_entity_company' => 'Company',

];
