<?php

declare(strict_types=1);

return [
    'title'                              => 'Configuración de seguridad',
    'navigation_label'                   => 'Seguridad',

    // Sección de autenticación
    'auth_section_description'           => 'Estos ajustes se aplican a todos los usuarios de su equipo y entran en vigor inmediatamente.',
    'enforce_2fa_label'                  => 'Imponer autenticación de dos factores',
    'enforce_2fa_helper_prefix'          => 'Cuando está activado, cada usuario de su equipo es redirigido a la configuración con código QR en su próxima solicitud y no puede usar el panel hasta completar la inscripción.',
    'enforce_2fa_helper_link'            => 'Configure su propio 2FA ahora →',
    'session_lifetime_label'             => 'Duración de la sesión (minutos)',
    'minutes_suffix'                     => 'min',

    // Sección de límite de velocidad
    'max_login_attempts_label'           => 'Máximo de intentos de inicio de sesión fallidos',
    'lockout_duration_label'             => 'Duración del bloqueo (minutos)',

    // Sección de lista blanca de IP
    'ip_whitelist_section_description'   => 'Permita el acceso al panel de administración solo desde estas direcciones IP. Déjelo vacío para permitir todas.',
    'ip_whitelist_label'                 => 'Direcciones IP permitidas',
    'ip_whitelist_placeholder'           => 'p. ej. 192.168.1.1',
    'ip_whitelist_helper'                => 'Introduzca IP (IPv4 o IPv6) o rangos CIDR y pulse Intro para añadir cada una.',

    // Acciones
    'action_save'                        => 'Guardar configuración',
];
