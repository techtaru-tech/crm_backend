<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadCaptureWidgetResource translation strings
|--------------------------------------------------------------------------
|
| Labels, defaults, placeholders and action copy for the Lead Capture
| Widgets resource at /admin/lead-capture-widgets.
| Consumed via __('filament/lead_capture_widgets.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                     => 'Widgets de clientes potenciales',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                   => 'Widget de captura de clientes potenciales',
    'plural_model_label'            => 'Widgets de captura de clientes potenciales',

    // ─── Form fields ───────────────────────────────────────────────────
    'name'                          => 'Nombre',
    'headline'                      => 'Titular',
    'subheadline'                   => 'Subtítulo',
    'button_text'                   => 'Texto del botón',
    'success_message'               => 'Mensaje de éxito',
    'primary_color'                 => 'Color primario',
    'text_color'                    => 'Color del texto',
    'position'                      => 'Posición',
    'created_at'                    => 'Creado el',

    // ─── Form defaults & placeholders ──────────────────────────────────
    'default_headline'              => 'Contacte con nosotros',
    'subheadline_placeholder'       => 'Subtítulo opcional',
    'default_button_text'           => 'Enviar mensaje',
    'default_success_message'       => 'Gracias. Nos pondremos en contacto en breve.',

    // ─── Form: appearance options ──────────────────────────────────────
    'position_bottom_right'         => 'Abajo a la derecha',
    'position_bottom_left'          => 'Abajo a la izquierda',
    'position_top_right'            => 'Arriba a la derecha',
    'position_top_left'             => 'Arriba a la izquierda',

    // ─── Form: form fields ─────────────────────────────────────────────
    'show_phone'                    => 'Mostrar campo de teléfono',
    'require_phone'                 => 'Teléfono obligatorio',
    'show_company'                  => 'Mostrar campo de empresa',
    'show_message'                  => 'Mostrar campo de mensaje',
    'require_message'               => 'Mensaje obligatorio',

    // ─── Form: routing ─────────────────────────────────────────────────
    'route_leads_to_pipeline'       => 'Enrutar clientes potenciales al embudo',
    'initial_stage'                 => 'Etapa inicial',

    // ─── Form: status ──────────────────────────────────────────────────
    'widget_is_active'              => 'El widget está activo',

    // ─── Table columns ─────────────────────────────────────────────────
    'active'                        => 'Activo',
    'leads_captured'                => 'Clientes potenciales captados',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_preview'                => 'Vista previa',
    'action_get_snippet'            => 'Obtener código de inserción',
    'snippet_modal_heading'         => 'Código de inserción',
    'snippet_modal_description'     => 'Copie el fragmento siguiente y péguelo en el HTML de su sitio web, justo antes de la etiqueta </body> de cierre.',
    'snippet_label'                 => 'Fragmento de inserción del widget',
    'modal_close'                   => 'Cerrar',
];
