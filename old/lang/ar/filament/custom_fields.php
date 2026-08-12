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
    'nav_label'              => 'الحقول المخصصة',
    'model_label'            => 'حقل مخصص',
    'plural_model_label'     => 'الحقول المخصصة',

    // ----- Section descriptions -----
    'definition_description' => 'حدّد حقلًا مخصصًا يُطبَّق على العملاء المحتملين أو الشركات.',
    'options_description'    => 'أضف الخيارات التي يمكن للمستخدمين الاختيار من بينها.',

    // ----- Definition fields -----
    'applies_to'        => 'ينطبق على',
    'field_label'       => 'اسم الحقل',
    'key_identifier'    => 'المفتاح / المعرّف',
    'key_help'          => 'يُستخدم كمفتاح JSON. أحرف صغيرة وأرقام وشرطات سفلية فقط.',
    'field_type'        => 'نوع الحقل',
    'placeholder'       => 'نص توضيحي',
    'helper_text'       => 'نص مساعد',

    // ----- Options -----
    'options'           => 'الخيارات',
    'options_help'      => 'اضغط Enter بعد كل خيار. ينطبق على حقول التحديد والتحديد المتعدد.',

    // ----- Visibility & Behavior -----
    'required'          => 'إلزامي',
    'show_in_form'      => 'إظهار في نموذج الإنشاء/التحرير',
    'show_in_table'     => 'إظهار في الجدول',
    'show_in_filters'   => 'إظهار في المرشحات',
    'sort_order'        => 'ترتيب الفرز',

    // ----- Table -----
    'col_entity'        => 'الكيان',
    'col_field'         => 'الحقل',
    'col_key'           => 'المفتاح',
    'col_type'          => 'النوع',
    'col_req'           => 'إلز.',
    'col_form'          => 'النموذج',
    'col_table'         => 'الجدول',
    'col_filters'       => 'المرشحات',
    'col_order'         => 'الترتيب',

    // ----- Filter labels -----
    'filter_entity'     => 'الكيان',
    'filter_type'       => 'النوع',

    // ─── Select options ────────────────────────────────────────────
    'option_entity_lead'    => 'عميل محتمل',
    'option_entity_company' => 'شركة',

];
