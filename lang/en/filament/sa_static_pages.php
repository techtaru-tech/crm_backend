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
    'static_page'                  => 'Static Page',
    'static_pages'                 => 'Static Pages',
    'tabs_outer'                   => 'Locale',

    // ----- Page content section -----
    'page_content_description'     => 'The title and body that appears at /pages/{slug}. Rich editor supports headings, lists, links, and basic formatting.',
    'slug_helper'                  => 'URL-safe. Auto-filled from the title.',
    'excerpt_helper'               => 'Optional one-liner shown under the title on the page.',
    'content'                      => 'Content',

    // ----- SEO section -----
    'seo_description'              => 'Meta tags search engines and social previews use.',
    'meta_description_helper'      => '1-2 sentences. Used for <meta name="description"> and social previews. 150 chars is ideal.',

    // ----- Translations section -----
    'translations_description'     => 'Per-language overrides. Fill a non-English locale to show translated copy on that language; empty fields fall back to the English content above.',
    'title'                        => 'Title',
    'excerpt'                      => 'Excerpt',
    'meta_description'             => 'Meta description',

    // ----- Visibility section -----
    'published'                    => 'Published',
    'published_helper'             => 'Unpublished pages 404 for the public.',
    'show_in_nav'                  => 'Show in main navigation',
    'show_in_nav_helper'           => 'Adds a link to this page in the marketing-site navbar.',
    'show_in_footer'               => 'Show in footer',
    'show_in_footer_helper'        => 'Adds a link to this page in the footer of every public page.',
    'nav_order'                    => 'Display order',
    'nav_order_helper'             => 'Lower number = earlier. Controls both nav and footer order.',

    // ----- Table columns -----
    'column_live'                  => 'Live',
    'column_nav'                   => 'Nav',
    'column_footer'                => 'Footer',
    'column_order'                 => 'Order',

    // ----- Actions -----
    'view'                         => 'View',
    'view_live'                    => 'View live',
    'new_static_page'              => 'New static page',

    // ----- Field labels (form + table) -----
    'slug'                         => 'Slug',
    'updated_at'                   => 'Last updated',

    // ----- Model labels -----
    'model_label'                  => 'Static Page',
    'plural_model_label'           => 'Static Pages',

];
