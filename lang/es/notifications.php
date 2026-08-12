<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Cadenas de notificación para Admin / Super-Admin (toasts Filament)
|--------------------------------------------------------------------------
|
| Mensajes breves de retroalimentación mostrados a través de
| Filament\Notifications\Notification::make()->title(__('notifications.X'))
| en las páginas Filament de tenant-admin y super-admin.
|
| Los compradores traducen o adaptan el texto editando este archivo, o
| copiándolo a lang/<locale>/notifications.php y traduciendo los valores.
|
| Los marcadores de posición usan la convención :placeholder de Laravel;
| pase valores mediante __('notifications.key', ['placeholder' => $value]).
|
*/

return [

    // --- Permisos genéricos / no encontrado -----------------------------
    'no_tenant_found'                      => 'No se encontró el tenant.',
    'no_workspace_active'                  => 'No hay ningún espacio de trabajo activo.',
    'no_workspace_context'                 => 'No se encontró contexto del espacio de trabajo.',
    'permission_denied'                    => 'Permiso denegado',
    'permission_required'                  => 'No tienes permiso.',
    'no_remove_permission'                 => 'No tienes permiso para eliminar miembros del equipo.',
    'no_suspend_permission'                => 'No tienes permiso para suspender miembros del equipo.',
    'cannot_remove_self'                   => 'No puedes eliminarte a ti mismo.',
    'cannot_suspend_self'                  => 'No puedes suspenderte a ti mismo.',
    'admin_only_suspend_admin'             => 'Solo un administrador puede suspender a otro administrador.',
    'user_not_in_workspace'                => 'Usuario no encontrado en este espacio de trabajo.',
    'no_owner_for_workspace'               => 'No se encontró propietario para este espacio de trabajo',

    // --- Configuración guardada -----------------------------------------
    'profile_updated'                      => 'Perfil actualizado correctamente.',
    'password_changed'                     => 'Contraseña cambiada correctamente.',
    'branding_saved'                       => 'Ajustes de marca guardados.',
    'email_saved'                          => 'Ajustes de correo guardados.',
    'general_saved'                        => 'Ajustes generales guardados.',
    'messaging_saved'                      => 'Ajustes de mensajería guardados.',
    'realtime_saved'                       => 'Ajustes en tiempo real guardados.',
    'security_saved'                       => 'Ajustes de seguridad guardados para este espacio de trabajo.',
    'storage_saved'                        => 'Ajustes de almacenamiento guardados.',
    'availability_saved'                   => 'Disponibilidad guardada',

    // --- Fallos genéricos al guardar / conectar (con :error) ------------
    'save_failed'                          => 'Error al guardar: :error',
    'test_failed_error'                    => 'Prueba fallida: :error',
    'storage_test_failed'                  => 'Prueba de almacenamiento fallida: :error',
    'storage_test_ok'                      => 'La conexión de almacenamiento funciona.',

    // --- Sesiones / 2FA -------------------------------------------------
    'session_revoked'                      => 'Sesión revocada.',
    'all_other_sessions_revoked'           => 'Todas las demás sesiones revocadas.',
    'cannot_revoke_current_session'        => 'No puedes revocar tu sesión actual.',
    'twofa_enabled'                        => '¡Autenticación de dos factores activada!',
    'twofa_disabled'                       => 'Autenticación de dos factores desactivada.',
    'twofa_invalid_code'                   => 'Código de verificación no válido. Inténtalo de nuevo.',
    'twofa_recovery_regenerated'           => 'Códigos de recuperación regenerados.',
    'twofa_generate_secret_first'          => 'Genera primero un secreto.',

    // --- Onboarding / Admin varios --------------------------------------
    'onboarding_revisit_hint'              => 'Puedes volver al onboarding en cualquier momento desde Ajustes → Equipo.',
    'preset_loaded'                        => 'Preajuste cargado. Pulsa Guardar para aplicar.',

    // --- Claves API -----------------------------------------------------
    'api_key_revoked'                      => 'Clave API revocada.',

    // --- Conexiones / Webhooks ------------------------------------------
    'connection_ok'                        => '¡Conexión exitosa!',
    'connection_failed'                    => '¡Conexión fallida!',
    'test_delivered_http'                  => 'Prueba entregada — HTTP :status',
    'test_failed_http'                     => 'Prueba fallida — HTTP :status',
    'delivery_requeued'                    => 'Entrega encolada para reintento.',
    'webhook_requeued'                     => 'Webhook reencolado para procesar',
    'webhook_source_missing'               => 'No se puede reintentar: conexión de origen no encontrada',

    // --- Reuniones / Reservas -------------------------------------------
    'booking_cancelled'                    => 'Reserva cancelada',
    'booking_completed'                    => 'Marcada como completada',
    'booking_no_show'                      => 'Marcada como ausencia',

    // --- Licencia -------------------------------------------------------
    'license_required'                     => 'Introduce tu clave de licencia.',

    // --- Dominio --------------------------------------------------------
    'domain_saved'                         => 'Dominio guardado. La verificación se está ejecutando en segundo plano.',
    'domain_verification_retriggered'      => 'Verificación de dominio reactivada.',
    'domain_already_taken'                 => 'Este dominio ya está registrado en otro espacio de trabajo.',

    // --- Tenant / Límites de plazas / Suplantación ----------------------
    'seat_limit_updated'                   => 'Límite de plazas actualizado a :limit.',
    'already_impersonating'                => 'Ya estás suplantando — detén primero la sesión actual',

    // --- Módulos (SA) ---------------------------------------------------
    'module_enabled'                       => 'Módulo habilitado: :name',
    'module_disabled'                      => 'Módulo deshabilitado: :name',
    'module_deleted'                       => 'Módulo eliminado: :name',
    'module_enable_failed'                 => 'Error al habilitar',
    'module_disable_failed'                => 'Error al deshabilitar',
    'module_delete_failed'                 => 'Error al eliminar',
    'module_install_failed'                => 'Error al instalar',
    'module_caches_cleared'                => 'Cachés del módulo borradas.',
    'module_regenerate_failed'             => 'Error al regenerar',
    'module_upload_zip_first'              => 'Sube primero un archivo zip.',

    // --- Fallback genérico (trait SafeAction) ---------------------------
    'something_went_wrong'                 => 'Algo salió mal',

    // --- Centro de notificaciones (livewire/notification-center) --------
    'panel_title'                          => 'Notificaciones',
    'mark_all_read'                        => 'Marcar todas como leídas',
    'aria_label'                           => 'Notificaciones',
    'dismiss'                              => 'Descartar',
    'empty_state'                          => 'Aún no hay notificaciones',
    'load_more'                            => 'Cargar más',

    /*
    |--------------------------------------------------------------------------
    | Texto de notificación orientado al usuario (app/Notifications/*.php)
    |--------------------------------------------------------------------------
    |
    | Cadenas renderizadas al usuario desde clases Notification encolables —
    | asuntos toMail(), titulares, líneas de cuerpo, etiquetas de botones,
    | mensajes toDatabase() mostrados en el desplegable del icono de
    | campana y títulos push de broadcast. Marcadores :placeholder.
    |
    */

    // --- Etiquetas / fallbacks de botón compartidos ---------------------
    'btn_view_lead'                        => 'Ver lead',
    'system_fallback'                      => 'Sistema',

    // --- ExportFailedNotification ---------------------------------------
    'export_failed_mail_subject'           => 'Exportación de leads fallida',
    'export_failed_mail_headline'          => 'Exportación fallida',
    'export_failed_mail_line_intro'        => 'Lamentablemente tu exportación de leads no pudo completarse.',
    'export_failed_mail_line_reason'       => '<strong>Motivo:</strong> :reason',
    'export_failed_mail_line_retry'        => 'Inténtalo de nuevo. Si el problema persiste, contacta con tu administrador.',
    'export_failed_db_message'             => 'Tu exportación de leads falló: :error',

    // --- IntegrationSyncFailedNotification ------------------------------
    'integration_sync_failed_broadcast_title' => 'Fallo de sincronización de :integration en :lead',
    'integration_sync_failed_push_title'   => 'Sincronización de integración fallida',
    'integration_sync_failed_mail_subject' => 'Sincronización de integración fallida: :integration',
    'integration_sync_failed_mail_headline' => 'Sincronización de integración fallida',
    'integration_sync_failed_mail_line_intro' => 'Una sincronización de integración falló para uno de tus leads.',
    'integration_sync_failed_mail_line_integration' => '<strong>Integración:</strong> :integration',
    'integration_sync_failed_mail_line_lead' => '<strong>Lead:</strong> :name',
    'integration_sync_failed_mail_line_error' => '<strong>Error:</strong> :error',

    // --- LeadAssignedNotification ---------------------------------------
    'lead_assigned_broadcast_title'        => 'Lead asignado: :name',
    'lead_assigned_push_title'             => 'Lead asignado a ti',
    'lead_assigned_mail_subject'           => 'Lead asignado a ti',
    'lead_assigned_mail_headline'          => 'Lead asignado a ti',
    'lead_assigned_mail_line_intro'        => 'Se te ha asignado un lead.',
    'lead_assigned_mail_line_lead'         => '<strong>Lead:</strong> :name',
    'lead_assigned_mail_line_assigned_by'  => '<strong>Asignado por:</strong> :name',

    // --- LeadAutomationNotification -------------------------------------
    'automation_push_title'                => 'Automatización activada',
    'automation_mail_subject'              => 'Automatización activada: :message',
    'automation_mail_headline'             => 'Automatización activada',
    'automation_mail_line_intro'           => 'Se activó una automatización para un lead de tu pipeline.',
    'automation_mail_line_lead'            => '<strong>Lead:</strong> :name',
    'automation_mail_line_message'         => '<strong>Mensaje:</strong> :message',

    // --- LeadReceivedNotification ---------------------------------------
    'lead_received_broadcast_title'        => 'Nuevo lead: :name',
    'lead_received_push_title'             => 'Nuevo lead recibido',
    'lead_received_mail_subject'           => 'Nuevo lead: :name',
    'lead_received_mail_headline'          => 'Nuevo lead recibido',
    'lead_received_mail_line_intro'        => 'Se ha recibido un nuevo lead en tu pipeline.',
    'lead_received_mail_line_name'         => '<strong>Nombre:</strong> :name',
    'lead_received_mail_line_email'        => '<strong>Correo:</strong> :email',
    'lead_received_mail_line_phone'        => '<strong>Teléfono:</strong> :phone',
    'lead_received_mail_line_source'       => '<strong>Origen:</strong> :source',

    // --- LeadStageChangedNotification -----------------------------------
    'lead_stage_changed_broadcast_title'   => 'Lead movido a :stage: :name',
    'lead_stage_changed_push_title'        => 'Etapa del lead cambiada',
    'lead_stage_changed_push_body'         => ':name movido a :stage',
    'lead_stage_changed_mail_subject'      => 'Etapa del lead actualizada',
    'lead_stage_changed_mail_headline'     => 'Etapa del lead actualizada',
    'lead_stage_changed_mail_line_intro'   => 'Un lead ha pasado a una nueva etapa del pipeline.',
    'lead_stage_changed_mail_line_lead'    => '<strong>Lead:</strong> :name',
    'lead_stage_changed_mail_line_from'    => '<strong>Desde:</strong> :stage',
    'lead_stage_changed_mail_line_to'      => '<strong>Hasta:</strong> :stage',

    // --- LeadTaskReminderNotification -----------------------------------
    'task_reminder_db_message'             => 'Recordatorio de tarea: :title',
    'task_reminder_mail_subject'           => 'Recordatorio de tarea: :title',
    'task_reminder_mail_greeting'          => 'Recordatorio de tarea',
    'task_reminder_mail_line_intro'        => 'Tienes una tarea próxima:',
    'task_reminder_mail_line_lead'         => 'Lead relacionado: :name',
    'task_reminder_mail_line_due'          => 'Vence: :due',
    'task_reminder_mail_line_priority'     => 'Prioridad: :priority',
    'task_reminder_mail_line_description'  => 'Descripción: :description',
    'task_reminder_mail_line_outro'        => 'Gracias por usar :app.',
    'task_reminder_due_no_date'            => 'sin fecha',
    'task_reminder_lead_fallback'          => 'un lead',
    'task_reminder_priority_normal'        => 'normal',

    // --- LeadsExportReady -----------------------------------------------
    'export_ready_db_message'              => 'Tu exportación de leads está lista para descargar.',
    'export_ready_push_title'              => 'Exportación lista',
    'export_ready_push_body'               => 'Tu exportación de leads (:filename) está lista para descargar.',
    'export_ready_mail_subject'            => 'Tu exportación está lista: :filename',
    'export_ready_mail_headline'           => 'Exportación lista para descargar',
    'export_ready_mail_line_intro'         => 'Tu exportación de leads se ha generado y está lista para descargar.',
    'export_ready_mail_line_file'          => '<strong>Archivo:</strong> :filename',
    'export_ready_btn_download'            => 'Descargar exportación',

    // --- MarketplaceTemplateInstalledNotification -----------------------
    'marketplace_template_installed_message' => ':installer instaló tu plantilla ":template" (:type).',

    // --- TenantDataExportReady ------------------------------------------
    'tenant_data_export_ready_message'     => 'Tu exportación de datos para ":name" está lista. El enlace de descarga expira en 24 horas.',

    // --- InvoiceDueReminderNotification ---------------------------------
    'invoice_due_reminder_db_message'         => 'La factura :number vence pronto.',
    'invoice_due_reminder_customer_fallback'  => 'tu cliente',
    'invoice_due_reminder_due_no_date'        => 'sin fecha',
    'invoice_due_reminder_mail_subject'       => 'La factura :number vence pronto',
    'invoice_due_reminder_mail_greeting'      => 'Recordatorio de vencimiento de factura',
    'invoice_due_reminder_mail_line_intro'    => 'Una de tus facturas se acerca a su fecha de vencimiento:',
    'invoice_due_reminder_mail_line_number'   => 'Factura: :number',
    'invoice_due_reminder_mail_line_customer' => 'Cliente: :name',
    'invoice_due_reminder_mail_line_amount'   => 'Importe: :amount',
    'invoice_due_reminder_mail_line_due'      => 'Vence: :due',
    'invoice_due_reminder_btn_view'           => 'Ver factura',
    'invoice_due_reminder_mail_line_outro'    => 'Gracias por usar :app.',
];
