<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin StaticPageResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_static_pages.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_static_pages.php.
*/

return [

    // ----- Resource labels -----
    'static_page'                  => 'Página estática',
    'static_pages'                 => 'Páginas estáticas',
    'tabs_outer'                   => 'Idioma',

    // ----- Page content section -----
    'page_content_description'     => 'El título y el cuerpo que aparecen en /pages/{slug}. El editor enriquecido admite encabezados, listas, enlaces y formato básico.',
    'slug_helper'                  => 'Seguro para URL. Autorrellenado desde el título.',
    'excerpt_helper'               => 'Texto opcional de una línea mostrado bajo el título en la página.',
    'content'                      => 'Contenido',

    // ----- SEO section -----
    'seo_description'              => 'Metaetiquetas que usan los motores de búsqueda y las vistas previas sociales.',
    'meta_description_helper'      => '1-2 frases. Se usa para <meta name="description"> y vistas previas sociales. 150 caracteres es lo ideal.',

    // ----- Translations section -----
    'translations_description'     => 'Sustituciones por idioma. Rellene un idioma no inglés para mostrar copia traducida en ese idioma; los campos vacíos vuelven al contenido en inglés anterior.',
    'title'                        => 'Título',
    'excerpt'                      => 'Extracto',
    'meta_description'             => 'Meta descripción',

    // ----- Visibility section -----
    'published'                    => 'Publicada',
    'published_helper'             => 'Las páginas no publicadas devuelven 404 al público.',
    'show_in_nav'                  => 'Mostrar en la navegación principal',
    'show_in_nav_helper'           => 'Añade un enlace a esta página en la barra de navegación del sitio de marketing.',
    'show_in_footer'               => 'Mostrar en el pie',
    'show_in_footer_helper'        => 'Añade un enlace a esta página en el pie de cada página pública.',
    'nav_order'                    => 'Orden de visualización',
    'nav_order_helper'             => 'Número menor = primero. Controla el orden tanto en la navegación como en el pie.',

    // ----- Table columns -----
    'column_live'                  => 'En vivo',
    'column_nav'                   => 'Navegación',
    'column_footer'                => 'Pie',
    'column_order'                 => 'Orden',

    // ----- Actions -----
    'view'                         => 'Ver',
    'view_live'                    => 'Ver en vivo',
    'new_static_page'              => 'Nueva página estática',

    // ----- Field labels (form + table) -----
    'slug'                         => 'Slug',
    'updated_at'                   => 'Última actualización',

    // ----- Model labels -----
    'model_label'                  => 'Página estática',
    'plural_model_label'           => 'Páginas estáticas',

];
