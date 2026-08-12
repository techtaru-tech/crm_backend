<?php

declare(strict_types=1);

return [
    'calendar_oauth' => [
        'consent_rejected'       => 'El proveedor rechazó el consentimiento: :error',
        'no_authorization_code'  => 'No se recibió código de autorización del proveedor.',
        'token_exchange_failed'  => 'No se pudo intercambiar el código de autorización. Verifica las credenciales de cliente de :provider en tu configuración de servicios.',
        'connection_save_failed' => 'No se pudo guardar la conexión. Inténtalo de nuevo.',
        'connected_success'      => 'Calendario de :provider conectado como :email.',
    ],

    'recaptcha' => [
        'verification_failed'       => 'La verificación de reCAPTCHA falló. Actualiza la página e inténtalo de nuevo.',
        'verification_failed_short' => 'La verificación de reCAPTCHA falló. Actualiza e inténtalo de nuevo.',
    ],

    'invoice_payment' => [
        'gateway_not_supported' => 'La pasarela seleccionada aún no admite el pago de facturas. Elige otra.',
        'start_failed'          => 'No se pudo iniciar el pago. Prueba con otro método.',
    ],

    'password_setup' => [
        'invalid_or_expired' => 'Este enlace de configuración no es válido o ha caducado. Usa el enlace «¿Olvidaste tu contraseña?» de la página de inicio de sesión para solicitar uno nuevo.',
    ],

    'coupon' => [
        'prefix'       => 'Cupón: ',
        'invalid_code' => 'Código no válido.',
    ],

    'oauth' => [
        'no_token_url'             => 'No hay URL de token para :type',
        'token_exchange_failed'    => 'El intercambio de token falló (:status): :body',
        'salesforce_invalid_url'   => 'El instance_url de Salesforce debe estar en *.salesforce.com o *.force.com (se obtuvo :host).',
        'salesforce_safety_failed' => 'El instance_url de Salesforce no pasó la comprobación de seguridad: :error',
        'salesforce_token_failed'  => 'El intercambio de token de Salesforce falló (:status): :body',
        'meta_token_failed'        => 'El intercambio de token de Meta falló: :body',
        'meta_no_access_token'     => 'No se devolvió access_token desde Meta',
    ],
];
