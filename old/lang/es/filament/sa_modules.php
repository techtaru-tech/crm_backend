<?php

declare(strict_types=1);

return [
    'title'                          => 'Módulos',
    'navigation_label'               => 'Módulos',

    // Install section
    'install_section_description'    => 'Suba un zip que contenga un manifiesto module.json. La carpeta del módulo se copia en Modules/, se regenera el cargador automático de nwidart y el módulo se inicia en estado deshabilitado hasta que lo habilite.',
    'module_zip_label'               => 'Zip del módulo',
    'module_zip_helper'              => 'Cualquier zip cuyo nivel superior contenga un archivo module.json (directamente o dentro de una carpeta envolvente única).',

    // Notifications
    'module_installed_title'         => 'Módulo instalado correctamente.',
    'module_installed_body'          => 'Se ha instalado :name. Habilítelo desde la lista de abajo.',

    // Header actions
    'action_regenerate'              => 'Regenerar autoload',
    'action_install'                 => 'Instalar el módulo subido',
    'action_install_confirmation'    => 'La carpeta del módulo se copiará en Modules/. Si ya existe un módulo con el mismo nombre, será reemplazado. ¿Continuar?',

    // Hero / page content
    'hero_eyebrow'                   => 'Extensiones HMVC',
    'hero_title'                     => 'Módulos',
    'unavailable_warning_strong'     => 'Sistema de módulos no disponible.',
    'installed_section_title'        => 'Módulos instalados',
    'empty_no_modules'               => 'No hay módulos instalados. Suba un zip arriba para empezar.',

    // Table column headers
    'col_module'                     => 'Módulo',
    'col_version'                    => 'Versión',
    'col_status'                     => 'Estado',
    'col_actions'                    => 'Acciones',

    // Action buttons + confirmations
    'btn_disable'                    => 'Deshabilitar',
    'btn_enable'                     => 'Habilitar',
    'btn_delete'                     => 'Eliminar',
    'confirm_permanently_delete'     => '¿Eliminar permanentemente el módulo :name? Esta acción no se puede deshacer.',

    // ─── Hero body + warning + status pills ───
    'hero_subtitle_html'             => 'Amplíe su LeadHub en 30 segundos. Suba un zip de módulo, haga clic en Habilitar y su nueva función estará activa. Sin composer, sin tiempo de inactividad, sin necesidad de un desarrollador. Cada módulo vive en una carpeta autónoma bajo <code class="mod-hero-code">Modules/&lt;Nombre&gt;</code> con sus propias rutas, migraciones, vistas, traducciones y recursos de administración.',
    'unavailable_warning_body_html'  => 'El entorno de ejecución de módulos no se cargó. Esto suele significar que la subida estaba incompleta &mdash; vuelva a subir el zip completo de distribución de LeadHub y asegúrese de que se incluya la carpeta <code class="mod-warn-code">vendor/</code>. Si el problema persiste, contacte con el soporte. La lista de abajo recurre a un escaneo del sistema de archivos para que cualquier módulo copiado manualmente siga siendo visible.',
    'pill_enabled'                   => 'habilitado',
    'pill_disabled'                  => 'deshabilitado',
];
