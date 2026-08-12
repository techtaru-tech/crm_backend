<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| CustomFieldDefinition model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/custom_field_definition.<key>').
*/

return [
    // ─── FIELD TYPES ───────────────────────────────
    'field_type_text'         => 'Text',
    'field_type_textarea'     => 'Textarea',
    'field_type_number'       => 'Number',
    'field_type_date'         => 'Date',
    'field_type_select'       => 'Select (single)',
    'field_type_multi_select' => 'Select (multiple)',
    'field_type_checkbox'     => 'Checkbox',
    'field_type_url'          => 'URL',
    'field_type_email'        => 'Email',
];
