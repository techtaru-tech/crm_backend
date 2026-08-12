<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin LandingPageEditor — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_landing_page_editor.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_landing_page_editor.php.
*/

return [

    // ----- Page title -----
    'title'                       => 'Editor de página de aterrizaje',
    'navigation_label'            => 'Página de aterrizaje',
    'tabs_outer'                  => 'Aterrizaje',
    'tabs_locale'                 => 'Idioma',

    // ----- Hero tab -----
    'tab_hero'                    => 'Hero',
    'hero_eyebrow'                => 'Antetítulo / texto de distintivo',
    'hero_eyebrow_placeholder'    => 'p. ej. Nuevo — facturación multidivisa disponible',
    'hero_headline'               => 'Titular principal',
    'hero_headline_placeholder'   => 'El espacio de clientes potenciales que su equipo',
    'hero_headline_highlight'     => 'Frase destacada (degradado)',
    'hero_headline_highlight_placeholder' => 'realmente quiere usar',
    'hero_subtext'                => 'Subtítulo',
    'hero_subtext_placeholder'    => 'Capte clientes potenciales desde cualquier origen…',
    'hero_cta_label'              => 'Etiqueta del botón CTA principal',
    'hero_cta_label_placeholder'  => 'Comience su prueba gratuita',
    'hero_note'                   => 'Nota de confianza (debajo del CTA)',
    'hero_note_placeholder'       => 'Sin tarjeta de crédito · Cancele en cualquier momento',

    // ----- Features tab -----
    'tab_features'                => 'Características',
    'features_headline'           => 'Titular de la sección',
    'features_subtext'            => 'Subtexto de la sección',
    'feature_cards'               => 'Tarjetas de características',
    'feature_title'               => 'Título',
    'feature_body'                => 'Cuerpo',

    // ----- Pricing tab -----
    'tab_pricing'                 => 'Resumen de precios',
    'pricing_headline'            => 'Titular de la sección',
    'pricing_subtext'             => 'Subtexto de la sección',

    // ----- Final CTA tab -----
    'tab_final_cta'               => 'CTA final',
    'cta_headline'                => 'Titular',
    'cta_subtext'                 => 'Subtexto',

    // ----- Stats tab -----
    'tab_stats'                   => 'Estadísticas',
    'stats_blank_helper'          => 'Déjalo en blanco para usar el valor predeterminado de los archivos de traducción de marketing.',
    'stat3_number_helper'         => 'El valor por defecto es el símbolo "∞". Anúlalo solo si quieres mostrar un tope real.',
    'stat1_number'                => 'Estadística 1 — Número',
    'stat1_label'                 => 'Estadística 1 — Etiqueta',
    'stat2_number'                => 'Estadística 2 — Número',
    'stat2_label'                 => 'Estadística 2 — Etiqueta',
    'stat3_number'                => 'Estadística 3 — Número',
    'stat3_label'                 => 'Estadística 3 — Etiqueta',
    'stat4_number'                => 'Estadística 4 — Número',
    'stat4_label'                 => 'Estadística 4 — Etiqueta',

    // ----- Navigation tab -----
    'tab_navigation'              => 'Navegación',
    'built_in_navbar_description' => 'Desactive cualquier enlace integrado que no quiera en la barra de navegación de marketing. Las páginas estáticas creadas en Sistema → Páginas estáticas se gestionan allí.',
    'show_pricing_link'           => 'Mostrar enlace «Precios»',
    'show_docs_link'              => 'Mostrar enlace «Documentación»',
    'show_sign_in_link'           => 'Mostrar enlace «Iniciar sesión»',
    'registration_legal_description' => 'A dónde deberían apuntar los enlaces «Términos del servicio» y «Política de privacidad» del formulario público /register. Déjelo en blanco para enlazar a las páginas estáticas /pages/terms y /pages/privacy que el seeder crea en la primera instalación.',
    'register_terms_url'          => 'URL de Términos del servicio',
    'register_terms_url_helper'   => 'Relativa (p. ej. /pages/terms) o absoluta (p. ej. https://legal.example.com/terms).',
    'register_privacy_url'        => 'URL de Política de privacidad',
    'register_privacy_url_helper' => 'Relativa o absoluta. Se abre en una nueva pestaña desde el formulario de registro.',

    // ----- Footer tab -----
    'tab_footer'                  => 'Pie de página',
    'columns_footer_description'  => 'Cada columna puede ocultarse de forma independiente. El pie enriquecido se usa en la variante de aterrizaje Light (predeterminada); las variantes más simples usan un pie mínimo integrado que estos interruptores también controlan.',
    'brand_tagline_column'        => 'Columna de marca + lema',
    'product_links'               => 'Enlaces de producto (Precios / Documentación / Iniciar sesión)',
    'static_pages_column'         => 'Columna de páginas estáticas (Recursos)',
    'static_pages_column_helper'  => 'Enlaces a las páginas estáticas que tenga marcadas como «Mostrar en pie» en Sistema → Páginas estáticas.',
    'social_icons_description'    => 'Rellene la URL del icono que quiera mostrar. Déjelo en blanco para ocultar ese icono. El interruptor maestro siguiente desactiva los tres a la vez.',
    'show_social_icons'           => 'Mostrar cualquier icono social',
    'social_x_url'                => 'URL de X (Twitter)',
    'social_github_url'           => 'URL de GitHub',
    'social_linkedin_url'         => 'URL de LinkedIn',
    'placeholder_x_url'           => 'https://x.com/suusuario',
    'placeholder_github_url'      => 'https://github.com/sucuenta',
    'placeholder_linkedin_url'    => 'https://linkedin.com/company/suempresa',
    'social_facebook_url'         => 'URL de Facebook',
    'social_instagram_url'        => 'URL de Instagram',
    'social_youtube_url'          => 'URL de YouTube',
    'placeholder_facebook_url'    => 'https://facebook.com/yourpage',
    'placeholder_instagram_url'   => 'https://instagram.com/yourhandle',
    'placeholder_youtube_url'     => 'https://youtube.com/@yourchannel',

    // ----- SEO tab -----
    'tab_seo'                     => 'SEO',
    'seo_section'                 => 'Metadatos para buscadores y redes sociales',
    'seo_section_description'     => 'Controla el título de la página, la metadescripción, las palabras clave y la imagen que se muestra cuando se comparte su página de inicio en redes sociales. Deje cualquier campo en blanco para mantener el valor predeterminado integrado.',
    'seo_meta_title'              => 'Metatítulo',
    'seo_meta_title_placeholder'  => 'YourApp — CRM autohospedado que le pertenece',
    'seo_meta_title_helper'       => 'Hasta ~60 caracteres. Se usa como título de la pestaña del navegador y como título al compartir en redes sociales. En blanco = el nombre de su aplicación.',
    'seo_meta_description'        => 'Metadescripción',
    'seo_meta_description_placeholder' => 'Un CRM autohospedado con orígenes de clientes potenciales, automatizaciones y multi-tenancy de marca blanca.',
    'seo_meta_description_helper' => 'Hasta ~160 caracteres. Se muestra bajo su título en los resultados de Google y en las vistas previas al compartir en redes sociales.',
    'seo_meta_keywords'           => 'Metapalabras clave',
    'seo_meta_keywords_placeholder' => 'crm, crm autohospedado, gestión de clientes potenciales',
    'seo_meta_keywords_helper'    => 'Separadas por comas. Opcional: la mayoría de los buscadores las ignoran, pero algunos directorios las usan.',
    'seo_og_image_url'            => 'URL de la imagen para compartir en redes',
    'seo_og_image_url_placeholder' => 'https://yourdomain.com/og-image.png',
    'seo_og_image_url_helper'     => 'Un PNG/JPG de 1200x630 que se muestra al compartir su página de inicio en redes sociales (Open Graph / tarjeta de Twitter). En blanco = el logotipo de su marca.',
    'footer_tagline'              => 'Lema (dentro de la columna de marca)',
    'footer_tagline_placeholder'  => 'Un CRM autohospedado que realmente le pertenece.',
    'footer_copyright'            => 'Línea de copyright (barra inferior)',
    'footer_copyright_placeholder'=> 'Predeterminado: © :year YourAppName. Todos los derechos reservados.',
    'footer_copyright_helper'     => 'Texto plano o HTML. Déjelo en blanco para el valor predeterminado.',

    // ----- Translations tab -----
    'tab_translations'            => 'Traducciones',
    'per_language_overrides_description' => 'El inglés se edita en las pestañas anteriores. Rellene aquí cada idioma no inglés para mostrar copia traducida a los visitantes en ese idioma. Los campos vacíos vuelven al inglés automáticamente.',
    'tr_hero_headline'            => 'Titular hero',
    'tr_hero_headline_highlight'  => 'Frase destacada del hero',
    'tr_hero_subtext'             => 'Subtexto del hero',
    'tr_hero_cta_label'           => 'Etiqueta del CTA del hero',
    'tr_hero_note'                => 'Nota del hero (línea de confianza)',
    'tr_hero_eyebrow'             => 'Antetítulo / distintivo del hero',
    'tr_features_headline'        => 'Titular de características',
    'tr_features_subtext'         => 'Subtexto de características',
    'tr_pricing_headline'         => 'Titular de precios',
    'tr_pricing_subtext'          => 'Subtexto de precios',
    'tr_cta_headline'             => 'Titular de CTA de cierre',
    'tr_cta_subtext'              => 'Subtexto de CTA de cierre',
    'tr_footer_tagline'           => 'Lema del pie',

    // ----- Save notification -----
    'saved_title'                 => 'Contenido de la página de aterrizaje guardado.',
    'saved_body'                  => 'Visite la página de aterrizaje para verificar sus cambios.',

    // ----- Header actions -----
    'preview'                     => 'Previsualizar página de aterrizaje',
    'save_changes'                => 'Guardar cambios',

    // ----- Hero -----
    'hero_eyebrow'                => 'Contenido de marketing',
    'page_hero_title'             => 'Reescriba su página de aterrizaje pública sin tocar código',
    'hero_body_html'              => 'Cada campo siguiente se renderiza en vivo en la página de aterrizaje pública en <code class="lpe-code">/</code>. Deje un campo en blanco para usar la copia predeterminada integrada, de modo que su sitio permanezca completo incluso mientras edita.',
];
