<?php

declare(strict_types=1);

return [

    // ─── Navigation / page header ─────────────────────────────────────
    'nav_label'                 => 'Mercado',
    'title'                     => 'Mercado de plantillas',
    'heading'                   => 'Mercado de plantillas',
    'subheading'                => 'Instale automatizaciones, embudos, secuencias de correo y formularios preconstruidos compartidos por otros espacios de trabajo. Los elementos destacados han sido revisados por el operador.',

    // ─── Install flow notifications ───────────────────────────────────
    'notif_not_found_title'     => 'Plantilla no encontrada o no publicada.',
    'notif_no_workspace_title'  => 'Sin contexto de espacio de trabajo.',
    'notif_paid_title'          => 'Las plantillas de pago aún no están soportadas',
    'notif_paid_body'           => 'Esta plantilla cuesta :price :currency. El flujo de pago llegará en una actualización posterior.',
    'notif_installed_title'     => 'Plantilla instalada',
    'notif_installed_body'      => 'Se han creado :count registro(s) de tipo :type. Encuéntrelo en la sección correspondiente — revise el contenido antes de activarlo.',
    'notif_install_failed_title' => 'Error en la instalación',
    'notif_install_unknown_error' => 'Error desconocido',

    // wire:confirm
    'confirm_install_template'  => '¿Instalar «:name» en su espacio de trabajo? Lo encontrará en la sección correspondiente, configurado como borrador para que pueda revisarlo antes de activarlo.',

    // ─── Page body (resources/views/filament/pages/marketplace.blade.php) ──
    'search_placeholder'        => 'Buscar plantillas por nombre o descripción…',
    'all_types'                 => 'Todos los tipos',
    'all_categories'            => 'Todas las categorías',
    'featured_only'             => 'Solo destacadas',
    'empty_no_matches_title'    => 'Sin coincidencias',
    'empty_no_templates_title'  => 'El mercado está vacío',
    'empty_no_matches_body'     => 'Intente quitar filtros o usar otros términos de búsqueda.',
    'empty_no_templates_body'   => 'Aún no se han publicado plantillas. Sea el primero — cree una plantilla y compártala desde su editor de automatización, formulario o secuencia de correo.',
    'templates_count'           => '{1} :count plantilla disponible|[2,*] :count plantillas disponibles',
    'featured_tag'              => '⭐ Destacada',
    'by_owner_prefix'           => 'por',
    'installs_count'            => '{1} :count instalación|[2,*] :count instalaciones',
    'free_price'                => 'Gratis',
    'install_free_btn'          => 'Instalar gratis',
    'install_paid_btn'          => 'Instalar — :price :currency',
];
