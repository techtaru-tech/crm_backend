<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin LocaleResource — cadenas del panel de Filament (es)
|------------------------------------------------------------
| Acceso vía __('filament/sa_locales.<clave>').
*/

return [

    // ----- Etiquetas del recurso -----
    'language'                    => 'Idioma',
    'languages'                   => 'Idiomas',

    // ----- Formulario de detalles del idioma -----
    'code_helper'                 => 'Código ISO de dos letras (o etiqueta de Locale): en, fr, de, el, pt-br, zh-tw. En minúsculas.',
    'native_name'                 => 'Nombre nativo',
    'native_name_helper'          => 'Se muestra en el selector de idiomas. Escríbalo en el propio idioma: Français, Ελληνικά, Deutsch.',
    'native_name_placeholder'     => 'Français',
    'english_name'                => 'Nombre en inglés',
    'english_name_helper'         => 'Se muestra en esta lista del panel. Opcional pero útil.',
    'english_name_placeholder'    => 'French',
    'flag_label'                  => 'Bandera (emoji)',
    'flag_helper'                 => 'Pegue una bandera emoji. Déjelo en blanco para un globo genérico.',
    'sort_order_helper'           => 'Más bajo = aparece antes en el selector.',

    // ----- Sección de comportamiento -----
    'enabled_helper'              => 'Desactive para ocultar el idioma del selector sin eliminarlo. Las traducciones existentes se conservan.',
    'rtl_label'                   => 'De derecha a izquierda',
    'rtl_helper'                  => 'Active para árabe, hebreo, persa, urdu. Añade dir="rtl" a la etiqueta <html> para este Locale.',
    'is_default_label'            => 'Idioma predeterminado',
    'is_default_helper'           => 'Los nuevos visitantes llegarán a este idioma. Solo un Locale puede ser predeterminado: al guardar este como predeterminado se quita la marca del anterior.',

    // ----- Columnas de la tabla -----
    'column_native'               => 'Nativo',
    'column_english'              => 'Inglés',
    'column_on'                   => 'Activo',
    'column_rtl'                  => 'RTL',
    'column_default'              => 'Predeterminado',
    'column_order'                => 'Orden',
    'column_admin_ui'             => 'UI del panel',
    'admin_ui_shipped'            => 'Incluido',
    'admin_ui_installed'          => 'Instalado',
    'admin_ui_english_fallback'   => 'Respaldo en inglés',
    'admin_ui_tooltip'            => 'Indica si la interfaz del panel de administración tiene cadenas traducidas para este idioma. Las traducciones del sitio de marketing se gestionan por separado desde los editores de Landing / Páginas estáticas.',

    // ----- Acción de establecer predeterminado -----
    'make_default'                => 'Establecer como predeterminado',
    'make_default_description'    => 'Los nuevos visitantes llegarán a :name de forma predeterminada.',
    'default_language_set'        => 'Idioma predeterminado establecido en :name.',

    // ----- Acción de eliminar -----
    'delete_description'          => 'Eliminar un idioma lo quita del selector, pero CONSERVA cualquier contenido por Locale que haya escrito en el Editor de Landing o en Páginas estáticas; si vuelve a añadir el idioma más tarde, se restaurará.',

    // ----- Acción de lista -----
    'add_language'                => 'Añadir idioma',

    // ----- Notificaciones de CreateLocale -----
    'created_marketing_ready_title'   => ':name añadido — sitio de marketing listo',
    'created_marketing_ready_body'    => 'El selector de idiomas y las pestañas del editor de Landing / Páginas estáticas ya están disponibles para este Locale. El propio panel de administración seguirá mostrándose en inglés para los usuarios que elijan este idioma, porque no hay ningún directorio `lang/:code/` instalado. Pídale a su proveedor de servicios que añada un paquete de idioma de Filament si necesita la traducción completa de la UI del panel.',
    'created_success_title'           => ':name añadido.',

    // ----- Etiquetas de campo (formulario + tabla) -----
    'code'                            => 'Código',
    'sort_order'                      => 'Orden',
    'enabled'                         => 'Activado',
    'flag'                            => 'Bandera',

    // ----- Etiquetas del modelo -----
    'model_label'                     => 'Locale',
    'plural_model_label'              => 'Locales',

];
