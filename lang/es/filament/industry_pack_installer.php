<?php

declare(strict_types=1);

return [

    // ----- Navegación -----
    'nav_label' => 'Paquetes de industria',

    // ----- Título de la página -----
    'title'     => 'Paquetes de industria',

    'notif_no_tenant'            => 'Sin contexto de inquilino.',
    'notif_install_failed_title' => 'No se pudo instalar el paquete de industria',
    'notif_installed_title'      => 'Paquete de industria instalado',
    'notif_summary_body'         => ':pipelines embudos, :stages etapas, :custom_fields campos personalizados, :tags etiquetas, :email_templates plantillas de correo, :automations automatizaciones, :forms formularios.',

    // wire:confirm
    'confirm_install_pack'       => '¿Instalar el paquete :name? Esto creará embudos, campos personalizados, etiquetas, automatizaciones y formularios en su espacio de trabajo.',

    // ─── Cuerpo de la página ───
    'intro'                      => 'Los paquetes de industria preparan su espacio de trabajo con un conjunto listo para usar de embudos, campos personalizados, etiquetas, plantillas de correo, automatizaciones y formularios adaptados para un sector específico. Las automatizaciones se instalan desactivadas para que pueda revisarlas antes de activarlas. Ejecutar un paquete dos veces es seguro — los elementos existentes se detectan por nombre/slug/clave y se omiten.',
    'stat_pipelines'             => 'Embudos',
    'stat_custom_fields'         => 'Campos personalizados',
    'stat_tags'                  => 'Etiquetas',
    'stat_email_templates'       => 'Plantillas de correo',
    'stat_automations'           => 'Automatizaciones',
    'stat_forms'                 => 'Formularios',
    'install_pack'               => 'Instalar paquete',
];
