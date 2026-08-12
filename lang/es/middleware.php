<?php

declare(strict_types=1);

return [
    'enforce_2fa' => [
        'required' => 'Se requiere la autenticación en dos pasos. Actívala para continuar.',
    ],

    'login_lockout' => [
        'too_many_attempts' => 'Demasiados intentos fallidos. Inténtalo de nuevo en :count minuto.|Demasiados intentos fallidos. Inténtalo de nuevo en :count minutos.',
    ],

    'not_suspended' => [
        'scheduled_for_deletion' => 'Este espacio de trabajo está programado para eliminación. Ponte en contacto con el propietario del espacio si necesitas cancelarlo.',
        'suspended'              => 'Tu cuenta ha sido suspendida. Ponte en contacto con el administrador del espacio de trabajo.',
    ],

    'seat_limit' => [
        'reached_short'     => 'Se ha alcanzado el límite de asientos.',
        'reached_short_alt' => 'Tu espacio de trabajo ha alcanzado el número máximo de asientos.',
        'reached_full'      => 'Este espacio de trabajo ha alcanzado su número máximo de asientos (:max). Mejora tu plan para añadir más miembros.',
    ],

    'impersonation' => [
        'expired'     => 'La sesión de suplantación ha caducado después de :minutes minutos.',
        'invalidated' => 'Sesión de suplantación invalidada: el superadministrador ya no es válido.',
    ],

    'resolve_tenant' => [
        'not_found'        => 'Espacio de trabajo no encontrado',
        'not_found_detail' => 'No hay ningún espacio de trabajo registrado para el dominio: :host',
    ],
];
