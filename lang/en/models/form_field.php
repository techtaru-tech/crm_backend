<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| FormField model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/form_field.<key>').
*/

return [
    // ─── TYPES ─────────────────────────────────────
    'type_text'           => 'Short Text',
    'type_textarea'       => 'Long Text',
    'type_email'          => 'Email',
    'type_phone'          => 'Phone',
    'type_number'         => 'Number',
    'type_select'         => 'Dropdown',
    'type_checkbox'       => 'Checkbox',
    'type_multi_checkbox' => 'Multi-Checkbox',
    'type_radio'          => 'Radio Buttons',
    'type_file'           => 'File Upload',
    'type_date'           => 'Date Picker',
    'type_hidden'         => 'Hidden Field',
    'type_gdpr'           => 'GDPR Consent',
    'type_divider'        => 'Section Divider',
];
