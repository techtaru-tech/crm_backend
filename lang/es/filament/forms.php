<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| FormResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/forms.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/forms.php.
*/

return [

    // ----- Navigation -----
    'nav_label'                 => 'Formularios',
    'model_label'               => 'Formulario',
    'plural_model_label'        => 'Formularios',

    // ----- Basic Details -----
    'form_name'                 => 'Nombre del formulario',
    'slug'                      => 'Slug',
    'slug_help'                 => 'Se usa en la URL pública.',
    'display_title'             => 'Título visible',
    'description'               => 'Descripción',
    'submit_button_label'       => 'Etiqueta del botón de envío',
    'thank_you_message'         => 'Mensaje de agradecimiento',
    'redirect_url'              => 'URL de redirección tras el envío',
    'multi_step_form'           => 'Formulario multi-paso',
    'multi_step_form_help'      => 'Agrupa los campos en pasos numerados.',
    'active'                    => 'Activo',

    // ----- Appearance -----
    'background_color'          => 'Color de fondo',
    'background_image_url'      => 'URL de imagen de fondo',
    'google_font_name'          => 'Nombre de Google Font',
    'google_font_name_placeholder' => 'p. ej. Inter',
    'logo_url'                  => 'URL del logotipo',

    // ----- Pipeline Connection -----
    'pipeline'                  => 'Embudo',
    'stage'                     => 'Etapa',

    // ----- Spam Protection -----
    'enable_recaptcha'          => 'Habilitar reCAPTCHA v3',
    'recaptcha_site_key'        => 'Clave del sitio de reCAPTCHA',
    'recaptcha_secret_key'      => 'Clave secreta de reCAPTCHA',

    // ----- Form Fields -----
    'fields_section_description' => 'Arrastre y suelte para reordenar. El campo de consentimiento GDPR se añade siempre el último y no se puede eliminar ni editar.',
    'add_field'                 => 'Añadir campo',
    'field_type'                => 'Tipo de campo',
    'field_label'               => 'Etiqueta',
    'field_placeholder'         => 'Marcador / ayuda',
    'field_key'                 => 'Clave del campo',
    'field_key_placeholder'     => 'p. ej. email, first_name',
    'field_key_help'            => 'Asigna el campo a la propiedad del cliente potencial.',
    'step_number'               => 'Paso n.º',
    'required'                  => 'Obligatorio',
    'options'                   => 'Opciones (una por línea)',
    'options_help'              => 'Introduzca cada opción en una nueva línea.',
    'field_gdpr_consent_default_label' => 'Consentimiento GDPR',
    // Default sentence saved on a form's GDPR consent FormField row at
    // create/edit time. Distinct from the column-header label above —
    // this is the consent text the end-user ticks on the public form.
    'gdpr_default_field_label'  => 'Acepto el tratamiento de mis datos personales conforme a la Política de Privacidad.',
    'field_gdpr_locked_suffix'  => '(bloqueado — no se puede eliminar)',

    // ----- Embed Snippet -----
    'embed_section_description' => 'Pegue este fragmento en cualquier página web para mostrar un widget flotante de captura de clientes potenciales.',
    'widget_embed_code'         => 'Código de inserción del widget',

    // ----- Live Preview -----
    'live_preview_description'  => 'Vista previa en vivo de cómo aparecerá el formulario a los visitantes.',
    'live_preview_open_in_new_tab' => 'Abrir en pestaña nueva',
    'live_preview_iframe_title' => 'Vista previa del formulario',

    // ----- Table columns -----
    'col_form_name'             => 'Nombre del formulario',
    'col_slug'                  => 'Slug',
    'col_active'                => 'Activo',
    'col_multi_step'            => 'Multi-paso',
    'col_submissions'           => 'Envíos',
    'col_created'               => 'Creado',

    // ----- Empty State -----
    'empty_heading'             => 'Aún no hay formularios',
    'empty_description'         => 'Cree un formulario de captura insertable en minutos. Coloque el fragmento en cualquier sitio — los envíos llegan como clientes potenciales automáticamente.',
    'create_first_form'         => 'Cree su primer formulario',

    // ----- Sub-pages: Actions -----
    'view_public_form'          => 'Ver formulario público',
    'analytics'                 => 'Analítica',
    'copy_embed_snippet'        => 'Copiar fragmento de inserción',
    'live_preview'              => 'Vista previa en vivo',

    // ----- Embed snippet copy toast (Alpine.js $dispatch notify) -----
    'snippet_copied_toast'      => '¡Fragmento copiado!',

    // ----- Embed snippet panel (Placeholder content) -----
    'save_form_first_for_snippet' => 'Guarde el formulario primero para generar el fragmento de inserción.',
    'copy'                      => 'Copiar',
    'copied'                    => '¡Copiado!',
    'public_url'                => 'URL pública',

    // ----- Analytics page -----
    'analytics_back_to_form'        => '← Volver al formulario',
    'analytics_breadcrumb_prefix'   => 'Analítica: :name',
    'analytics_total_submissions'   => 'Total de envíos',
    'analytics_form_status'         => 'Estado del formulario',
    'analytics_status_active'       => 'Activo',
    'analytics_status_inactive'     => 'Inactivo',
    'analytics_total_fields'        => 'Total de campos',
    'analytics_submissions_30d'     => 'Envíos (últimos 30 días)',
    'analytics_field_completion'    => 'Tasa de cumplimentación de campos',
    'analytics_step_dropoff'        => 'Embudo de abandono por paso',
    'analytics_step_label'          => 'Paso :n',
    'analytics_step_reached'        => ':count alcanzados',
    'analytics_embed_snippet'       => 'Fragmento de inserción',
    'analytics_embed_snippet_intro' => 'Pegue este fragmento en cualquier sitio web para insertar el widget flotante de captura de clientes potenciales:',
    'analytics_public_form_url'     => 'URL pública del formulario:',

];
