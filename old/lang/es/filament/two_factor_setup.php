<?php

declare(strict_types=1);

return [

    // ----- Navegación -----
    'nav_label' => 'Autenticación 2FA',

    // ----- Título de la página -----
    'page_title' => 'Autenticación de dos factores',

    // ----- Encabezados de sección y wire:confirm -----
    'recovery_codes_heading'   => 'Códigos de recuperación',
    'confirm_disable_2fa'      => '¿Está seguro de que desea desactivar 2FA? Su cuenta será menos segura.',

    // ----- Estado activado -----
    'section_2fa_active'           => 'La autenticación de dos factores está activa',
    'account_protected'            => 'Su cuenta está protegida con 2FA',
    'codes_required_each_login'    => 'Se requieren códigos de autenticación en cada inicio de sesión.',
    'save_codes_safe_place'        => 'Guarde estos códigos en un lugar seguro. Cada código solo se puede usar una vez.',
    'btn_regenerate_codes'         => 'Regenerar códigos de recuperación',
    'btn_disable_2fa'              => 'Desactivar 2FA',

    // ----- Estado de configuración (código QR) -----
    'section_scan_qr'              => 'Escanear código QR',
    'scan_with_authenticator'      => 'Escanee este código QR con su aplicación de autenticación (Google Authenticator, Authy, 1Password, etc.)',
    'manual_code_label'            => 'Código manual:',
    'enter_verification_code'      => 'Introduzca el código de verificación de la aplicación de autenticación',
    'btn_verify_and_enable'        => 'Verificar y activar',

    // ----- Estado inicial -----
    'section_enable_2fa'           => 'Activar autenticación de dos factores',
    'initial_state_lede'           => 'Añada una capa extra de seguridad a su cuenta activando la autenticación de dos factores. Necesitará una aplicación de autenticación como Google Authenticator o Authy.',
    'btn_set_up_2fa'               => 'Configurar autenticación de dos factores',
];
