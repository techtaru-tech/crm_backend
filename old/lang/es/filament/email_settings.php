<?php

declare(strict_types=1);

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Correo',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Configuración de correo (SMTP)',

    // --- SMTP fields ---------------------------------------------------
    'host'            => 'Host',
    'port'            => 'Puerto',
    'encryption'      => 'Cifrado',
    'username'        => 'Usuario',
    'password'        => 'Contraseña',
    'password_helper' => 'Deje en blanco para mantener la contraseña actual. Escriba un nuevo valor para reemplazarla.',
    'from_name'       => 'Nombre del remitente',
    'from_email'      => 'Correo del remitente',

    // --- Test email action ---------------------------------------------
    'send_test_email'  => 'Enviar correo de prueba',
    'recipient'        => 'Enviar correo de prueba a',
    'recipient_helper' => 'Cualquier dirección — útil para enviar la prueba a sí mismo o al propietario de la cuenta para verificación.',
    'modal_submit'     => 'Enviar prueba',

    // --- Test notifications --------------------------------------------
    'no_recipient' => 'Sin destinatario — añada primero una dirección de correo.',
    'test_sent_to' => 'Correo de prueba enviado a :recipient',
    'send_failed'  => 'Error de envío: :error',

    // --- Header actions ------------------------------------------------
    'save_settings' => 'Guardar configuración',

    // ─── Blade view — override notice banner ───────────────────────────
    'override_notice_title' => 'El correo de este espacio de trabajo se envía a través del servicio de forma predeterminada',
    'override_notice_body_html' => "Deje esta página vacía y el correo saliente (restablecimientos de contraseña, automatizaciones, mensajes de secuencias, mensajes de prueba, notificaciones) se entrega mediante el propio servidor de correo del servicio — no tiene que configurar nada.\n                Rellene un host SMTP a continuación <strong>solo si</strong> desea que cada correo que envíe este espacio de trabajo pase por <em>su propio</em> servidor de correo (p. ej. para autenticación de dominio del remitente, control estricto de entregabilidad o cumplimiento normativo).\n                Cuando se guarde, su configuración SMTP anulará la predeterminada del servicio para este espacio de trabajo — otros espacios no se ven afectados, y si más adelante borra estos campos, el correo vuelve automáticamente al valor predeterminado del servicio.",

    // ─── Select options ────────────────────────────────────────────
    'option_encryption_none' => 'Ninguno',

];
