<?php

declare(strict_types=1);

return [
    'title'                              => 'Marca de correo',
    'navigation_label'                   => 'Marca de correo',

    // Header section
    'header_section_description'         => 'La banda coloreada en la parte superior de cada correo saliente. Las anulaciones del color primario específicas del inquilino siguen prevaleciendo cuando un inquilino define el suyo propio en Marca — estos valores son los predeterminados a nivel de script.',
    'header_style_label'                 => 'Estilo',
    'header_style_solid'                 => 'Color sólido',
    'header_style_gradient'              => 'Degradado lineal',
    'header_color_primary_gradient'      => 'Color inicial del degradado',
    'header_color_primary_solid'         => 'Color de fondo',
    'header_color_secondary_label'       => 'Color final del degradado',
    'header_gradient_angle_label'        => 'Ángulo del degradado (grados)',
    'header_gradient_angle_helper'       => '0 = de abajo arriba · 90 = de izquierda a derecha · 135 = diagonal (predeterminado) · 180 = de arriba abajo.',

    // Footer section
    'footer_section_description'         => 'La pequeña banda al final de cada correo. Color plano por diseño — un degradado aquí compite con el bloque de CTA de arriba.',
    'footer_color_label'                 => 'Color de fondo',
    'footer_text_color_label'            => 'Color del texto',
    'footer_text_color_helper'           => 'Debe contrastar con el fondo superior para legibilidad.',

    // Notifications & actions
    'save_failed_title'                  => 'No se pudo guardar la marca de correo',
    'save_failed_body'                   => 'Detalles en el registro del servidor. Causa más común: la migración de configuración aún no se ha ejecutado — ejecute `php artisan migrate --force` en el servidor.',
    'saved_title'                        => 'Marca de correo guardada',
    'saved_body'                         => 'Los nuevos colores se aplican a cada correo enviado a partir de ahora.',
    'action_save'                        => 'Guardar',

    // ─── Live preview strip ───
    'preview_title'                      => 'Vista previa',
    'preview_subtitle'                   => 'Reflejo del diseño del correo saliente — se actualiza mientras cambia los selectores.',
    'preview_sample_greeting'            => 'Hola Jane,',
    'preview_sample_body'                => 'Texto de cuerpo de correo de muestra. Los mensajes reales reemplazan esto con su propio contenido.',
    'preview_footer_reason'              => 'Ha recibido este correo porque es usuario de :app.',
];
