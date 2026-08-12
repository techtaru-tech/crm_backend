<?php

declare(strict_types=1);

return [

    // ─── Configuración predeterminada del widget ──────────────────────
    'default_headline'        => 'Póngase en contacto',
    'default_button_label'    => 'Enviar mensaje',
    'default_success_message' => '¡Gracias! Nos pondremos en contacto.',

    // ─── Marcadores de campos del formulario embebido ─────────────────
    'field_first_name' => 'Nombre *',
    'field_email'      => 'Correo electrónico *',
    'field_phone'      => 'Teléfono',
    'field_company'    => 'Empresa',
    'field_message'    => 'Mensaje',

    // ─── Estados de ejecución del formulario embebido ─────────────────
    'try_again' => 'Inténtelo de nuevo',

    // ─── Página HTML de vista previa ──────────────────────────────────
    'preview_title'   => 'Vista previa del widget',
    'preview_suffix'  => ' — vista previa',
    'preview_banner'  => '🔍 Vista previa del widget — esta es una página de prueba que muestra cómo se ve el widget cuando se incrusta en un sitio real.',

    'preview_mock_h1'    => 'Sitio web de cliente de ejemplo',
    'preview_mock_intro' => 'Esta es una página simulada. El widget debe aparecer en la esquina configurada.',

    'preview_how_heading'              => 'Cómo funciona la vista previa',
    'preview_how_paragraph_html'       => 'El cargador del widget está incrustado al final de esta página. Haga clic en él, complete el formulario y envíelo — un lead real <strong>se</strong> capturará en su espacio de trabajo (use datos de prueba).',
    'preview_main_content_placeholder' => 'Aquí iría el contenido principal de su sitio',

    'preview_settings_heading'       => 'Ajustes del widget en uso',
    'preview_setting_position'       => 'Posición:',
    'preview_setting_primary_colour' => 'Color principal:',
    'preview_setting_active'         => 'Activo en producción:',

    'preview_active_yes' => 'sí',
    'preview_active_no'  => 'no (no se mostrará en un sitio real)',
];
