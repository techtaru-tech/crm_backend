<?php

declare(strict_types=1);

return [
    'no_key_provided'        => 'No se proporcionó clave de licencia.',
    'no_key_configured'      => 'No hay clave de licencia configurada.',
    'verify_url_missing'     => 'El servidor de verificación de licencia no está configurado. Establece LEADHUB_LICENSE_VERIFY_URL en tu archivo .env.',
    'key_not_found'          => 'Clave de licencia no encontrada. Comprueba tu clave e inténtalo de nuevo.',
    'not_valid_for_domain'   => 'La licencia no es válida para este dominio.',
    'server_error_http'      => 'El servidor de licencias devolvió un error (HTTP :status). Inténtalo de nuevo o contacta con soporte.',
    'key_not_valid'          => 'La clave de licencia no es válida.',
    'verified'               => 'Licencia verificada.',
    'default_license_type'   => 'Licencia regular',
    'cannot_reach_server'    => 'No se pudo contactar con el servidor de licencias. Si ya está verificada, tu estado en caché seguirá activo.',
    'demo_mode_active'       => 'El modo demo está activo — se omite la verificación de licencia en la vista previa pública.',
    'item_id_mismatch'       => 'Este código de compra es para el producto CodeCanyon #:actual, no para LeadHub (#:expected). Usa el código de tu compra de LeadHub.',
    'activated_via_heartbeat' => 'Licencia aceptada — el servidor de licencias está temporalmente inaccesible, pero tu instalación queda desbloqueada mientras reintentamos en segundo plano.',
    'unexpected_response'    => 'El servidor de licencias devolvió una respuesta inesperada. Inténtalo de nuevo o contacta con soporte.',
    'token_decode_failed'    => 'El servidor de licencias devolvió un token de verificación que no pudimos decodificar con tu código de compra. Vuelve a introducirlo e inténtalo de nuevo.',
    'token_claims_mismatch'  => 'El token de verificación no coincide con el registro de compra. Reactiva volviendo a introducir tu código de compra.',
    'cached_recent'          => 'El estado de la licencia se verificó recientemente — usando el resultado en caché.',
    'not_yet_activated'      => 'Esta instalación aún no está activada. Introduce tu código de compra en Ajustes → Licencia.',
    'invalid_verification_id_format' => 'El identificador de verificación almacenado está malformado. Reactiva volviendo a introducir tu código de compra.',
    'purchase_code_mismatch' => 'El código de compra registrado no coincide con el registro de verificación. Reactiva volviendo a introducir tu código.',
];
