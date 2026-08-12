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
    'nav_label'              => 'Campos personalizados',
    'model_label'            => 'Campo personalizado',
    'plural_model_label'     => 'Campos personalizados',

    // ----- Section descriptions -----
    'definition_description' => 'Defina un campo personalizado que se aplique a clientes potenciales o empresas.',
    'options_description'    => 'Añada las opciones que los usuarios podrán elegir.',

    // ----- Definition fields -----
    'applies_to'        => 'Se aplica a',
    'field_label'       => 'Etiqueta del campo',
    'key_identifier'    => 'Clave / Identificador',
    'key_help'          => 'Se usa como clave JSON. Solo letras minúsculas, números y guiones bajos.',
    'field_type'        => 'Tipo de campo',
    'placeholder'       => 'Marcador de posición',
    'helper_text'       => 'Texto de ayuda',

    // ----- Options -----
    'options'           => 'Opciones',
    'options_help'      => 'Pulse Intro después de cada opción. Aplica a los campos Selección y Selección múltiple.',

    // ----- Visibility & Behavior -----
    'required'          => 'Obligatorio',
    'show_in_form'      => 'Mostrar en formulario Crear/Editar',
    'show_in_table'     => 'Mostrar en tabla',
    'show_in_filters'   => 'Mostrar en filtros',
    'sort_order'        => 'Orden de clasificación',

    // ----- Table -----
    'col_entity'        => 'Entidad',
    'col_field'         => 'Campo',
    'col_key'           => 'Clave',
    'col_type'          => 'Tipo',
    'col_req'           => 'Oblig.',
    'col_form'          => 'Formulario',
    'col_table'         => 'Tabla',
    'col_filters'       => 'Filtros',
    'col_order'         => 'Orden',

    // ----- Filter labels -----
    'filter_entity'     => 'Entidad',
    'filter_type'       => 'Tipo',

    // ─── Select options ────────────────────────────────────────────
    'option_entity_lead'    => 'Cliente potencial',
    'option_entity_company' => 'Empresa',

];
