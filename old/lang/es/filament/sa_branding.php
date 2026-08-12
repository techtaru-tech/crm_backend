<?php

declare(strict_types=1);

/*
|-----------------------------------------------------------------
| SuperAdmin BrandingPage - Filament admin strings
|-----------------------------------------------------------------
| Accessed via __('filament/sa_branding.<key>').
*/

return [
    'navigation_label'              => 'Marca',
    'title'                         => 'Marca',

    'section_identity'              => 'Identidad de marca',
    'section_identity_description'  => 'El nombre y los recursos visuales que representan tu plataforma en la página de inicio pública, la pantalla de inicio de sesión y los paneles de inquilinos.',
    'app_name_label'                => 'Nombre de marca',
    'app_name_helper'               => 'El nombre del producto mostrado en la pestaña del navegador, la pantalla de inicio de sesión y el encabezado del panel. Cámbialo de "LeadHub" a tu propia marca.',
    'logo_url_label'                => 'URL del logo',
    'logo_url_helper'               => 'URL pública de tu logo de marca (PNG o SVG). Déjalo en blanco para usar la marca predeterminada.',
    'logo_file_label'               => 'O sube un archivo de logo',
    'logo_file_helper'              => 'PNG / SVG / JPG hasta 2 MB. Subir un archivo reemplaza la URL de arriba con el archivo subido.',
    'favicon_url_label'             => 'URL del favicon',
    'favicon_url_helper'            => 'URL pública de un ICO / PNG de 32x32 (o mayor) para la pestaña del navegador.',
    'favicon_file_label'            => 'O sube un archivo de favicon',
    'favicon_file_helper'           => 'PNG / ICO / SVG cuadrado (32x32 o mayor). Subir un archivo reemplaza la URL de arriba.',
    'footer_text_label'             => 'Texto del pie de página',
    'footer_text_helper'            => 'Eslogan corto o línea de copyright mostrada en el pie de página público. No se procesa Markdown.',

    'section_colors'                => 'Colores de marca',
    'section_colors_description'    => 'Paleta de colores aplicada a botones, enlaces y acentos en el sitio público y los paneles de administración.',
    'primary_color_label'           => 'Color primario',
    'accent_color_label'            => 'Color de acento',

    'section_login'                 => 'Pantalla de inicio de sesión',
    'section_login_description'     => 'Personaliza el fondo de las pantallas de inicio de sesión /admin y /super-admin.',
    'login_bg_color_label'          => 'Color de fondo del inicio de sesión',
    'login_bg_image_label'          => 'URL de imagen de fondo del inicio de sesión',

    'action_save'                   => 'Guardar marca',
    'saved_title'                   => 'Marca guardada',
    'saved_body'                    => 'Tus cambios están activos en el sitio público y los paneles de administración.',
    'save_failed_title'             => 'No se pudo guardar la marca',
    'save_failed_body'              => 'El guardado falló y se revirtió. Revisa storage/logs/laravel.log para ver el error detallado.',
];
