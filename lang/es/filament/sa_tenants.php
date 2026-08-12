<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin TenantResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_tenants.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_tenants.php.
*/

return [

    // ----- Resource labels -----
    'workspace'                       => 'Espacio de trabajo',
    'workspaces'                      => 'Espacios de trabajo',

    // ----- Workspace details form -----
    'reserved_slug_error'             => '«:value» está reservado. Elija otro slug para el espacio de trabajo.',
    'workspace_url_helper'            => 'URL del espacio de trabajo: :url — también se usa para las páginas de aterrizaje públicas. Autorrellenado desde el nombre.',
    'max_seats_helper'                => 'Límite máximo de miembros del equipo.',
    'subscription_status_helper'      => 'La suspensión se establece mediante el botón «Suspender» — no aquí. Prueba caducada y Caducado se establecen normalmente de forma automática por el cron del ciclo de vida.',
    'trial_ends_at_label'             => 'La prueba termina el',
    'trial_ends_at_helper'            => 'Cuándo termina la prueba gratuita. Autorrellenado desde el trial_days del plan al seleccionar un plan.',
    'subscription_ends_at_label'      => 'La suscripción termina el',
    'subscription_ends_at_helper'     => 'Próxima fecha de facturación para suscripciones activas, o la fecha en que termina el acceso para las canceladas/caducadas.',

    // ----- Subscription status options -----
    'status_trial'                    => 'Prueba',
    'status_trial_expired'            => 'Prueba caducada',
    'status_active_paid'              => 'Activa (de pago)',
    'status_cancelled'                => 'Cancelada',
    'status_expired'                  => 'Caducada',
    'status_active'                   => 'Activa',
    'status_suspended'                => 'Suspendida',
    'status_unknown'                  => 'Desconocida',

    // ----- Tenant admin section -----
    'tenant_admin_description'        => 'El usuario administrador que poseerá y gestionará este espacio de trabajo.',
    'admin_name'                      => 'Nombre del administrador',
    'admin_email'                     => 'Correo del administrador',
    'admin_password_mode'             => 'Configuración de contraseña',
    'admin_password_mode_email_link'  => 'Enviar al administrador un enlace de configuración (recomendado)',
    'admin_password_mode_generate'    => 'Generar automáticamente y mostrar aquí',
    'admin_password_mode_manual'      => 'Establecer contraseña ahora',
    'admin_password'                  => 'Contraseña',
    'admin_password_helper'           => 'Al menos 10 caracteres. Comuníquela de forma segura al administrador.',

    // ----- Table columns -----
    'owner'                           => 'Propietario',
    'owner_email'                     => 'Correo del propietario',
    'status_column'                   => 'Estado',
    'seats'                           => 'Plazas',
    'trial_ends'                      => 'Termina la prueba',
    'sub_ends'                        => 'Termina la sub.',

    // ----- Filters -----
    'filter_suspension'               => 'Suspensión',

    // ----- Suspend action -----
    'suspend'                         => 'Suspender',
    'suspend_modal_heading'           => 'Suspender espacio de trabajo',
    'suspend_modal_description'       => '¿Suspender «:name»? Todos los miembros perderán el acceso en su próxima solicitud y verán la página de suscripción requerida. Es reversible.',
    'suspend_reason_label'            => 'Motivo (opcional, interno — para el registro de auditoría)',
    'suspend_demo_guard'              => 'Demo: no se pueden suspender inquilinos (enviaría un correo de notificación real).',
    'suspend_notification_title'      => 'Espacio de trabajo «:name» suspendido',
    'suspend_notification_body_base'  => 'Los miembros serán redirigidos en su próxima solicitud.',
    'suspend_notification_body_owner_notified'  => ' Propietario notificado en :email.',
    'suspend_notification_body_owner_failed'    => ' El correo al propietario no se pudo enviar (consulte los registros).',
    'suspend_notification_body_no_owner'        => ' El inquilino no tiene propietario — no se envió notificación.',

    // ----- Reactivate action -----
    'reactivate'                      => 'Reactivar',
    'reactivate_modal_heading'        => 'Reactivar espacio de trabajo',
    'reactivate_modal_description'    => '¿Reactivar «:name»? Los miembros recuperan el acceso de inmediato en su próxima solicitud.',
    'reactivate_demo_guard'           => 'Demo: no se pueden reactivar inquilinos.',
    'reactivate_notification_title'   => 'Espacio de trabajo «:name» reactivado',

    // ----- Impersonate action -----
    'impersonate'                     => 'Suplantar',
    'impersonate_modal_heading'       => 'Suplantar al administrador del inquilino',
    'impersonate_modal_description'   => 'Iniciará sesión como «:owner_name» (:owner_email) en el espacio de trabajo «:tenant_name». Todas las acciones se realizarán como este usuario. Puede volver a Super Admin en cualquier momento mediante la pancarta.',
    'impersonate_demo_guard'          => 'Demo: la suplantación está deshabilitada.',

    // ----- CreateTenant page notifications -----
    'workspace_created'               => 'Espacio de trabajo creado',
    'workspace_created_password_body' => "Contraseña para :email: \n\n  :password\n\nCópiela ahora — no se mostrará de nuevo.",
    'workspace_created_manual_body'   => 'La contraseña de administrador que eligió está activa. Compártala de forma segura con :email.',
    'workspace_created_email_body'    => 'Se ha enviado un enlace de configuración por correo a :email',
    'workspace_created_email_failed_title' => 'Espacio de trabajo creado pero el correo falló',
    'workspace_created_email_failed_body'  => 'El espacio de trabajo se creó, pero el correo de configuración no pudo enviarse a :email. El usuario puede usar el enlace «Olvidé mi contraseña» en la página de inicio de sesión para obtener acceso.',
    'workspace_created_existing_user' => 'El usuario existente :email ha sido asignado como administrador. No se envió correo de configuración.',

    // ----- Field labels (form + table) -----
    'name'                            => 'Nombre',
    'slug'                            => 'Slug',
    'max_seats'                       => 'Máximo de plazas',
    'plan'                            => 'Plan',
    'subscription_status'             => 'Estado de la suscripción',
    'created_at'                      => 'Creado el',

    // ----- Model labels -----
    'model_label'                     => 'Inquilino',
    'plural_model_label'              => 'Inquilinos',

];
