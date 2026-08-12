<?php

declare(strict_types=1);

return [
    'updates_blocked' => 'Demo: no se pueden aplicar paquetes de actualización.',

    // Guardas específicas de la página de copias de seguridad (una clave por sitio de llamada).
    'backups_create_method_blocked'  => 'Demo: no se pueden crear copias de seguridad (volcaría la BD y archivos de la demo en vivo).',
    'backups_delete_method_blocked'  => 'Demo: no se pueden eliminar archivos de copia de seguridad.',
    'backups_restore_method_blocked' => 'Demo: no se pueden restaurar copias de seguridad (sobrescribiría la BD de la demo en vivo).',
    'backups_create_action_blocked'  => 'Demo: no se pueden crear copias de seguridad.',
    'backups_restore_action_blocked' => 'Demo: no se pueden restaurar copias de seguridad.',
    'backups_delete_action_blocked'  => 'Demo: no se pueden eliminar copias de seguridad.',

    'gdpr_anonymize_blocked' => 'Demo: la anonimización GDPR está desactivada.',
    'gdpr_erase_blocked'     => 'Demo: la supresión GDPR está desactivada.',

    'checkout_blocked'       => 'Demo: no se puede iniciar un pago real.',
    'impersonation_disabled' => 'Demo: la suplantación está desactivada.',
    'test_email_blocked'     => 'Demo: no se pueden enviar correos de prueba a destinatarios arbitrarios.',
];
