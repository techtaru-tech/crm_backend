<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LandingPageResource translation strings
|--------------------------------------------------------------------------
|
| Labels, helper texts, placeholders and tab/section copy for the
| Landing Pages resource at /admin/landing-pages.
| Consumed via __('filament/landing_pages.<key>').
|
| Keys are snake_case; grouped by purpose-comment headers.
|
*/

return [

    // ─── Navigation ──────────────────────────────────────────────────
    'nav_label'                         => 'Páginas de aterrizaje',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Página de aterrizaje',
    'plural_model_label'                => 'Páginas de aterrizaje',

    // ─── Top-level tabs ─────────────────────────────────────────────
    'landing_page'                   => 'Página de aterrizaje',
    'tab_basics'                     => 'Datos básicos',
    'tab_sections'                   => 'Secciones',
    'tab_appearance'                 => 'Apariencia',
    'tab_integration'                => 'Integración',
    'tab_advanced'                   => 'Avanzado',

    // ─── Basics tab ────────────────────────────────────────────────
    'internal_name'                  => 'Nombre interno',
    'slug'                           => 'Slug',
    'slug_help_with_workspace'       => 'Se usa en la URL pública: :url',
    'slug_help_generic'              => 'Se usa en la URL pública: /{workspace}/{slug}',
    'browser_title'                  => 'Título del navegador',
    'meta_description'               => 'Meta descripción',
    'og_image_url'                   => 'URL de imagen Open Graph',
    'favicon_url'                    => 'URL del favicon',
    'status'                         => 'Estado',

    // ─── Sections tab — repeater ───────────────────────────────────
    'add_section'                    => 'Añadir sección',
    'new_section'                    => 'Nueva sección',
    'section_type'                   => 'Tipo de sección',
    'visible'                        => 'Visible',

    // ─── HERO section ──────────────────────────────────────────────
    'hero_eyebrow'                   => 'Antetítulo (etiqueta pequeña)',
    'headline'                       => 'Titular',
    'subheadline'                    => 'Subtítulo',
    'cta_label'                      => 'Etiqueta del CTA',
    'cta_url'                        => 'URL del CTA',
    'image_url'                      => 'URL de la imagen',
    'alignment'                      => 'Alineación',
    'background'                     => 'Fondo',

    // ─── FEATURES section ─────────────────────────────────────────
    'feature_items'                  => 'Elementos de características',
    'icon_key_optional'              => 'Clave del icono (opcional)',
    'feature_title'                  => 'Título',
    'feature_body'                   => 'Cuerpo',

    // ─── FORM section ─────────────────────────────────────────────
    'form'                           => 'Formulario',
    'success_message'                => 'Mensaje de éxito',

    // ─── TESTIMONIALS section ─────────────────────────────────────
    'testimonials'                   => 'Testimonios',
    'avatar_url'                     => 'URL del avatar',
    'testimonial_quote'              => 'Cita',
    'testimonial_author'             => 'Autor',
    'testimonial_role'               => 'Cargo',

    // ─── CTA section ─────────────────────────────────────────────
    'body'                           => 'Cuerpo',
    'button_label'                   => 'Etiqueta del botón',
    'button_url'                     => 'URL del botón',

    // ─── GALLERY section ─────────────────────────────────────────
    'images'                         => 'Imágenes',
    'image_url_placeholder'          => 'https://example.com/image.jpg',
    'caption'                        => 'Pie de imagen',

    // ─── PRICING section ─────────────────────────────────────────
    'plans'                          => 'Planes',
    'features_one_per_line'          => 'Características (una por línea)',
    'highlight_this_plan'            => 'Destacar este plan',
    'plan_name'                      => 'Nombre',
    'plan_price'                     => 'Precio',
    'plan_interval'                  => 'Intervalo',
    'plan_cta_label'                 => 'Etiqueta del CTA',
    'plan_cta_url'                   => 'URL del CTA',
    'plan_cta_default'               => 'Elegir plan',

    // ─── FAQ section ─────────────────────────────────────────────
    'q_and_a'                        => 'Preguntas y respuestas',
    'faq_question'                   => 'Pregunta',
    'faq_answer'                     => 'Respuesta',

    // ─── HTML section ────────────────────────────────────────────
    'raw_html'                       => 'HTML sin procesar',
    'raw_html_help'                  => 'Solo administradores. Los atributos de script y manejadores de eventos se eliminan. Coloque los fragmentos de seguimiento en la pestaña Avanzado.',

    // ─── Appearance tab ──────────────────────────────────────────
    'primary_color'                  => 'Color primario',
    'background_color'               => 'Color de fondo',
    'font_family'                    => 'Familia tipográfica',

    // ─── Integration tab ─────────────────────────────────────────
    'linked_form'                    => 'Formulario vinculado',
    'linked_form_help'               => 'Se usa para la sección de formulario predeterminada y como manejador de envío de respaldo.',
    'pipeline'                       => 'Embudo',
    'stage'                          => 'Etapa',
    'redirect_on_submit'             => 'URL de redirección tras el envío',

    // ─── Advanced tab ────────────────────────────────────────────
    'custom_css'                     => 'CSS personalizado',
    'custom_css_help'                => 'Se inyecta en <style> antes del cuerpo de la página. Solo administradores.',
    'custom_js'                      => 'JS personalizado (fragmentos de seguimiento)',
    'custom_js_help'                 => 'Se inyecta justo antes de </body>. Solo administradores — se ejecuta en cada visitante.',

    // ─── Table columns ───────────────────────────────────────────
    'name'                           => 'Nombre',
    'slug_copied'                    => 'Slug copiado',
    'views'                          => 'Visitas',
    'conversions'                    => 'Conversiones',
    'created'                        => 'Creada',

    // ─── Row actions ─────────────────────────────────────────────
    'preview'                        => 'Vista previa',
    'duplicate'                      => 'Duplicar',

    // ─── Use-Template header action ──────────────────────────────
    'use_template'                   => 'Usar plantilla',
    'template'                       => 'Plantilla',
    'page_name'                      => 'Nombre de la página',
    'template_not_found'             => 'Plantilla no encontrada',
    'template_created_title'         => 'Página de aterrizaje creada desde plantilla',
    'template_created_body'          => 'Edite la página para personalizar el contenido.',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'            => 'Borrador',
    'option_status_published'        => 'Publicada',
    'option_status_archived'         => 'Archivada',
    'option_align_left'              => 'Izquierda',
    'option_align_center'            => 'Centro',
    'option_bg_gradient'             => 'Degradado',
    'option_bg_solid'                => 'Sólido',
    'option_bg_image'                => 'Imagen',
    'option_palette_indigo'          => 'Índigo',
    'option_palette_gray'            => 'Gris',
    'option_palette_white'           => 'Blanco',

    // ─── Status badge labels (table column) ────────────────────────
    'status_draft'                   => 'Borrador',
    'status_published'               => 'Publicada',
    'status_archived'                => 'Archivada',

    // ─── Font fallback option ──────────────────────────────────────
    'font_system_default'            => 'Predeterminada del sistema',

    // ─── Plan interval short suffix (pricing section default) ──────
    'plan_interval_short_mo'         => 'mes',
];
