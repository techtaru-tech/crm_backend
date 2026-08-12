<?php

return [
    'app'       => 'Aplicación',
    'dashboard' => 'Panel de control',
    'save'      => 'Guardar',
    'cancel'    => 'Cancelar',
    'delete'    => 'Eliminar',
    'edit'      => 'Editar',
    'create'    => 'Crear',
    'search'    => 'Buscar',
    'loading'   => 'Cargando…',
    'yes'       => 'Sí',
    'no'        => 'No',
    'back'      => 'Atrás',
    'submit'    => 'Enviar',
    'confirm'   => 'Confirmar',
    'success'   => 'Éxito',
    'error'     => 'Error',
    'warning'   => 'Advertencia',

    'billing_unknown_plan'          => 'Plan desconocido [:plan].',
    'calendar_unsupported_provider' => 'Proveedor no compatible: :provider',
    'registration_throttled'        => 'Demasiados intentos de registro. Inténtalo de nuevo en :seconds s.',

    // ─── DemoMode guard / abort copy (live demo lockdown) ───
    'demo_mode_title'            => '🛡️ Modo demo',
    'demo_action_disabled_body'  => 'Esta acción está deshabilitada en la demo en vivo. Obtén tu propia copia para desbloquear todo.',
    'demo_get_leadhub'           => 'Obtener :app',
    'demo_action_disabled_short' => 'Esta acción está deshabilitada en la demo en vivo.',

    // ─── License-required block screen (EnforceLicense middleware) ───
    'license_required_short'              => 'Licencia requerida.',
    'license_required_title'              => ':app — Licencia requerida',
    'license_required_heading'            => 'Licencia requerida',
    'license_required_lead'               => 'Tu licencia de :app debe verificarse de nuevo antes de poder usar el panel de administración.',
    'license_required_reason_label'       => 'Motivo',
    'license_required_step_codecanyon'    => 'Inicia sesión en tu cuenta de CodeCanyon y abre Descargas.',
    'license_required_step_purchase_code' => 'Copia el código de compra del certificado de licencia.',
    'license_required_step_paste_settings' => 'Pégalo en Super Admin → Ajustes → Licencia y haz clic en Verificar.',
    'license_required_cta_settings'       => 'Abrir ajustes de licencia',
    'license_required_cta_codecanyon'     => 'Ir a CodeCanyon',
    'license_required_item_label'         => 'Producto CodeCanyon',

    // ─── Enforce2Fa middleware (JSON 403 for mobile/API) ───
    'two_factor_required' => 'Se requiere autenticación en dos pasos. Activa la verificación en dos pasos en los ajustes de tu cuenta.',

    // ─── Billing controller errors ───
    'billing_checkout_failed'    => 'El proceso de pago ha fallado.',
    'billing_portal_stripe_only' => 'El portal de cliente solo está disponible para Stripe.',
    'billing_portal_unavailable' => 'El portal de cliente no está disponible. Completa primero un pago con Stripe o contacta con soporte.',

    // ─── Calendar OAuth errors ───
    'calendar_connection_not_found' => 'Conexión no encontrada.',
    'calendar_oauth_no_session'     => 'La devolución de llamada OAuth del calendario requiere una sesión autenticada.',
    'oauth_state_mismatch'          => 'Estado OAuth no coincide: posible intento de CSRF.',
    'calendar_disconnected_success' => 'Calendario desconectado.',

    // ─── Invitation errors ───
    'invitation_invalid_or_expired' => 'Esta invitación no es válida o ha expirado.',

    // ─── Tenant scope errors ───
    'no_tenant_assigned'      => 'Tu cuenta no está asociada a ningún espacio de trabajo.',
    'session_revoked'         => 'Tu sesión ha sido revocada. Inicia sesión de nuevo.',
    'no_workspace_resolved'   => 'No se ha resuelto ningún espacio de trabajo.',
    'no_workspace_found'      => 'No se ha encontrado ningún espacio de trabajo para :host.',

    // ─── Data export controller (GDPR Art. 20) ───
    'export_link_invalid'      => 'El enlace de descarga ha expirado o no es válido.',
    'export_link_expired'      => 'El enlace de descarga ha expirado.',
    'export_link_wrong_user'   => 'Este enlace de descarga pertenece a otro usuario.',
    'export_file_unavailable'  => 'El archivo de exportación ya no está disponible. Solicita una nueva exportación.',

    // ─── Portal (customer dashboard) ───
    'file_type_not_allowed'     => 'Tipo de archivo no permitido.',
    'portal_magic_link_invalid' => 'Este enlace de inicio de sesión no es válido, ha expirado o ya se ha utilizado. Solicita uno nuevo.',
    'portal_file_uploaded'      => 'Archivo subido.',

    // ─── Impersonation & super admin ───
    'only_super_admins_impersonate'   => 'Solo los superadministradores pueden suplantar identidad.',
    'impersonate_no_owner'            => 'Este espacio de trabajo no tiene propietario al que suplantar.',
    'impersonate_already_active'      => 'Ya estás suplantando una identidad. Finaliza la sesión actual primero.',
    'access_denied_super_admin_only'  => 'Acceso denegado. Solo superadministradores.',
    'signed_in_as_super_admin_info'   => 'Has iniciado sesión como superadministrador. Usa la acción Suplantar en un inquilino para acceder a su espacio de trabajo.',

    // ─── Security middleware ───
    'access_denied_ip_not_whitelisted' => 'Acceso denegado: tu dirección IP no está en la lista blanca de este espacio de trabajo.',
    'forbidden_generic'                => 'Prohibido.',

    // ─── Lead attachment guard ───
    'attachment_disk_not_allowed' => 'El disco del adjunto no está en la lista de permitidos.',

    // ─── Public quote (customer-facing) ───
    'quote_already_accepted'                  => 'Este presupuesto ya ha sido aceptado.',
    'quote_already_accepted_cannot_decline'   => 'Este presupuesto ya ha sido aceptado y no puede ser rechazado.',
    'quote_response_recorded'                 => 'Tu respuesta ha sido registrada. Gracias.',

    // ─── Public invoice (customer-facing) ───
    'invoice_already_paid'             => 'Esta factura ya ha sido pagada.',
    'invoice_pay_manual_instructions'  => 'Por favor, transfiere el importe utilizando los datos bancarios de esta página. Tu factura se marcará como pagada una vez que el inquilino concilie el pago.',

    // ─── Integration OAuth (CRM/marketing) ───
    'integration_oauth_unavailable'    => 'OAuth no está disponible para :type. Configura primero client_id y client_secret.',
    'integration_oauth_state_mismatch' => 'Estado OAuth no coincide. Inténtalo de nuevo.',
    'integration_oauth_denied'         => 'OAuth denegado: :reason',
    'integration_oauth_no_code'        => 'No se ha recibido ningún código de autorización.',
    'integration_oauth_exchange_failed'=> 'Error al intercambiar el token: :error',
    'integration_oauth_connected'      => ':label conectado correctamente mediante OAuth.',

    // ─── Lead-source OAuth connections ───
    'oauth_not_configured_for_source'  => 'OAuth no está configurado para :source. Añade primero client_id y client_secret.',
    'oauth_session_expired'            => 'La sesión OAuth ha expirado. Inténtalo de nuevo.',
    'oauth_state_invalid'              => 'Estado OAuth no válido. Inténtalo de nuevo.',
    'oauth_authorization_denied'       => 'Autorización OAuth denegada: :reason',
    'oauth_connection_not_found'       => 'Conexión no encontrada o falta el código de autorización.',
    'oauth_token_exchange_failed'      => 'Error al intercambiar el token: :error',
    'oauth_token_retrieval_failed'     => 'No se han podido obtener los tokens de acceso.',
    'oauth_connected_success'          => '¡Conexión OAuth realizada correctamente!',

    // ─── Public widget submission ───
    'widget_not_found'         => 'Widget no encontrado',
    'widget_submission_failed' => 'Error al enviar',

    // ─── Public form (reCAPTCHA) ───
    'recaptcha_token_missing'      => 'Falta el token reCAPTCHA.',
    'recaptcha_spam_check_failed'  => 'La verificación antispam ha fallado.',

    // ─── Public booking endpoints ───
    'booking_invalid_datetime'             => 'Fecha y hora no válidas.',
    'booking_time_advance_notice_failed'   => 'Esa hora ya no cumple con el requisito de antelación.',
    'booking_time_too_far_future'          => 'Esa hora está demasiado lejos en el futuro.',
    'booking_slot_taken'                   => 'Ese horario acaba de ser reservado. Elige otra hora.',

    // ─── InvitationService: MAIL=log warning notification ───
    'email_logged_title' => 'Correo registrado, no enviado',
    'email_logged_body'  => "El controlador de correo está configurado como 'log', por lo que el correo de invitación no llegará a :email. Copia este enlace firmado y compártelo manualmente:\n\n:url",

    // ─── PasswordSetupController: post-setup welcome notification ───
    'password_set_title' => 'Contraseña establecida',
    'password_set_body'  => '¡Bienvenido! Tu cuenta está lista.',

    // ─── InvitationController: post-accept welcome notification ───
    'welcome_to_app'         => 'Bienvenido a :app',
    'joined_workspace_body'  => 'Te has unido a :workspace. Aquí tienes tu panel de control.',
    'workspace_fallback'     => 'tu espacio de trabajo',

    'auth' => [
        'email'     => 'Correo electrónico',
        'password'  => 'Contraseña',
        'sign_in'   => 'Iniciar sesión',
        'sign_out'  => 'Cerrar sesión',
        'register'  => 'Crear cuenta',
    ],

    'onboarding' => [
        'subjects' => [
            'day_1'    => 'Bienvenido a :workspace — empieza con un cliente potencial',
            'day_3'    => '¿Cómo van las cosas en :workspace?',
            'day_5'    => '3 automatizaciones que todo equipo activa en la primera semana',
            'day_7'    => 'Revisión rápida: ¿:workspace está aportando valor?',
            'fallback' => 'Una nota de tu CRM',
        ],
    ],
];
