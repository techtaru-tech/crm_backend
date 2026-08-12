<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — Cadenas de traducción de IntegrationResource (es)
|--------------------------------------------------------------------------
|
| Etiquetas, marcadores, textos de ayuda y copy de acciones para el recurso
| Integraciones en /admin/integrations.
| Acceso vía __('filament/integrations.<clave>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Integraciones',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Integración',
    'plural_model_label'                => 'Integraciones',

    // ─── Table column labels ──────────────────────────────────────────
    'col_status'                        => 'Estado',

    // ─── Form: integration setup ───────────────────────────────────────
    'integration_type'                  => 'Tipo de integración',
    'display_name'                      => 'Nombre visible',
    'enable_this_integration'           => 'Activar esta integración',

    // ─── Form: connection configuration ────────────────────────────────
    'webhook_url'                       => 'URL del webhook',
    'webhook_url_placeholder'           => 'https://...',
    'api_key'                           => 'Clave API',
    'access_token_oauth'                => 'Token de acceso / Token OAuth',
    'domain_subdomain'                  => 'Dominio / Subdominio',
    'domain_placeholder'                => 'suempresa',
    'instance_url'                      => 'URL de la instancia',
    'instance_url_placeholder'          => 'https://suinstancia.salesforce.com',
    'list_audience_id'                  => 'ID de lista / audiencia',
    'board_id'                          => 'ID del tablero',
    'spreadsheet_id'                    => 'ID de la hoja de cálculo',
    'sheet_name'                        => 'Nombre de la hoja',
    'notion_database_id'                => 'ID de base de datos de Notion',
    'airtable_base_id'                  => 'ID de base de Airtable',
    'airtable_table_name'               => 'Nombre de tabla de Airtable',
    'account_sid'                       => 'SID de la cuenta',
    'auth_token'                        => 'Token de autenticación',
    'from_number'                       => 'Número remitente',
    'api_secret'                        => 'Secreto API',
    'user_email'                        => 'Correo del usuario',
    'streak_pipeline_key'               => 'Clave del embudo de Streak',
    'activecampaign_api_url'            => 'URL de la API de ActiveCampaign',
    'activecampaign_api_url_placeholder' => 'https://sucuenta.api-us1.com',
    'convertkit_form_id'                => 'ID de formulario / secuencia de ConvertKit',
    'drip_account_id'                   => 'ID de cuenta de Drip',
    'api_token'                         => 'Token API',
    'mailerlite_group_id'               => 'ID de grupo de MailerLite',
    'mailchimp_data_center'             => 'Centro de datos de Mailchimp (p. ej. us1)',
    'zendesk_subdomain'                 => 'Subdominio de Zendesk',
    'zendesk_admin_email'               => 'Correo de administrador de Zendesk',
    'zendesk_api_token'                 => 'Token API de Zendesk',
    'salesforce_object_type'            => 'Tipo de objeto de Salesforce',
    'salesforce_object_lead'            => 'Cliente potencial',
    'salesforce_object_contact'         => 'Contacto',
    'sms_template'                      => 'Plantilla SMS',
    'sms_template_placeholder'          => 'Nuevo cliente potencial: {{lead.first_name}} {{lead.last_name}} ({{lead.email}})',
    'slack_message_template'            => 'Plantilla de mensaje de Slack',
    'slack_message_template_placeholder' => 'Use {{lead.first_name}}, {{lead.email}}, etc.',
    'auth_type'                         => 'Tipo de autenticación',
    'auth_type_none'                    => 'Ninguno',
    'auth_type_bearer'                  => 'Token Bearer',
    'auth_type_api_key'                 => 'Cabecera de clave API',
    'auth_type_basic'                   => 'Autenticación básica',
    'auth_value'                        => 'Valor de autenticación (token/clave/credenciales)',
    'json_body_template'                => 'Plantilla de cuerpo JSON',
    'json_body_template_placeholder'    => '{"name": "{{lead.first_name}} {{lead.last_name}}", "email": "{{lead.email}}"}',

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_label'               => 'Asignar campos de LeadHub a campos destino',
    'leadhub_field'                     => 'Campo de LeadHub',
    'target_field_name'                 => 'Nombre del campo destino',
    'target_field_placeholder'          => 'p. ej. email, FIRST_NAME, properties.email',
    'add_field_mapping'                 => 'Añadir asignación de campo',
    'lh_field_first_name'               => 'Nombre',
    'lh_field_last_name'                => 'Apellido',
    'lh_field_email'                    => 'Correo electrónico',
    'lh_field_phone'                    => 'Teléfono',
    'lh_field_company'                  => 'Empresa',
    'lh_field_source'                   => 'Origen del cliente potencial',
    'lh_field_status'                   => 'Estado',
    'lh_field_lead_score'               => 'Puntuación del cliente potencial',
    'lh_field_address'                  => 'Dirección',
    'lh_field_city'                     => 'Ciudad',
    'lh_field_country'                  => 'País',
    'lh_field_notes'                    => 'Notas',

    // ─── Form: filter leads ────────────────────────────────────────────
    'filter_sources'                    => 'Sincronizar solo desde estos orígenes (dejar en blanco para todos)',
    'filter_tags'                       => 'Sincronizar solo clientes potenciales con estas etiquetas',

    // ─── Table columns ─────────────────────────────────────────────────
    'name'                              => 'Nombre',
    'category'                          => 'Categoría',
    'last_sync'                         => 'Última sincronización',
    'last_sync_never'                   => 'Nunca',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_status'               => 'Estado',
    'status_connected'                  => 'Conectado',
    'status_disconnected'               => 'Desconectado',
    'status_error'                      => 'Error / Roto',
    'enabled'                           => 'Activado',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_test'                       => 'Probar',
    'action_sync_logs'                  => 'Registros de sincronización',
    'connection_successful'             => '¡Conexión correcta!',
    'connection_failed'                 => 'La conexión falló. Compruebe sus credenciales.',
    'error_prefix'                      => 'Error: ',

    // ─── Bulk actions ──────────────────────────────────────────────────
    'bulk_enable'                       => 'Activar seleccionados',
    'bulk_disable'                      => 'Desactivar seleccionados',

    // ─── ListIntegrations notifications ────────────────────────────────
    'notify_saved'                      => 'Integración guardada.',
    'notify_enabled'                    => 'Integración activada.',
    'notify_disabled'                   => 'Integración desactivada.',
    'notify_removed'                    => 'Integración eliminada.',

    // ─── Sync logs header actions ──────────────────────────────────────
    'retry_all_failed'                  => 'Reintentar todas las fallidas',
    'back_to_integrations'              => 'Volver a Integraciones',
    'retrying_failed_syncs'             => 'Reintentando :count sincronización(es) fallida(s).',

    // ─── List page: modal + cards ──────────────────────────────────────
    'connection_settings_heading'       => 'Configuración de conexión',
    'modal_cancel'                      => 'Cancelar',
    'modal_save_integration'            => 'Guardar integración',
    'confirm_remove_integration'        => '¿Eliminar esta integración?',

    // ─── Component: integration-config-notice ──────────────────────────
    'config_notice_note_label'          => 'Nota:',
    'config_notice_body'                => 'Las credenciales se cifran en reposo. Las integraciones OAuth (HubSpot, Salesforce, Zoho, Google Sheets) requieren un token de acceso de su flujo OAuth. Péguelo en el campo Token de acceso de arriba.',

    // ─── List page: status pills ───────────────────────────────────────
    'status_connected_label'            => 'Conectado',
    'status_error_label'                => 'Error',
    'status_inactive_label'             => 'Inactivo',
    'action_test_connection'            => 'Probar conexión',
    'action_configure'                  => 'Configurar',
    'action_sync_logs_title'            => 'Registros de sincronización',
    'action_remove'                     => 'Eliminar',
    'action_disable'                    => 'Desactivar',
    'action_enable'                     => 'Activar',
    'last_sync_prefix'                  => 'Última sincronización: :time',
    'btn_connect'                       => '+ Conectar',
    'setup_title_prefix'                => 'Configuración: :type',

    // ─── List page: OAuth config ───────────────────────────────────────
    'oauth_connected_pill'              => 'Conectado vía OAuth',
    'oauth_reconnect'                   => 'Reconectar vía OAuth',
    'oauth_connect'                     => 'Conectar vía OAuth',
    'oauth_hint'                        => 'Introduzca primero su Client ID y Secret abajo, luego haga clic para autorizar.',

    // ─── List page: field mapping ──────────────────────────────────────
    'field_mapping_heading'             => 'Asignación de campos',
    'field_optional_suffix'             => '(opcional)',
    'field_mapping_desc'                => 'Asigne los campos de :app a los nombres de campo destino en la aplicación conectada.',
    'select_source_field'               => 'Campo de :app',
    'select_target_field'               => 'Seleccionar campo destino',
    'target_field_input_placeholder'    => 'Nombre del campo destino',
    'btn_add_mapping'                   => '+ Añadir asignación',

    // ─── List page: source filter ──────────────────────────────────────
    'source_filter_heading'             => 'Filtro de origen',
    'source_filter_desc'                => 'Sincronizar solo clientes potenciales de estos orígenes (dejar vacío para sincronizar todos).',

    // ─── List page: tag filter ─────────────────────────────────────────
    'tag_filter_heading'                => 'Filtro de etiquetas',
    'tag_filter_desc'                   => 'Sincronizar solo clientes potenciales que tengan al menos una de estas etiquetas (dejar vacío para sincronizar todos).',
    'no_tags_created'                   => 'Aún no se han creado etiquetas.',

    // ─── List page: pipeline filter ────────────────────────────────────
    'pipeline_filter_heading'           => 'Filtro de embudo',
    'pipeline_filter_desc'              => 'Sincronizar solo clientes potenciales actualmente en estos embudos (dejar vacío para sincronizar todos).',
    'no_pipelines_created'              => 'Aún no se han creado embudos.',

    // ─── Sync logs page ────────────────────────────────────────────────
    'sync_logs_heading'                 => 'Registros de sincronización — :name',
    'sync_logs_total'                   => ':count en total',
    'no_sync_logs'                      => 'Aún no hay registros de sincronización. Los clientes potenciales se sincronizarán aquí una vez que se active la integración.',
    'col_lead'                          => 'Cliente potencial',
    'col_event'                         => 'Evento',
    'col_status'                        => 'Estado',
    'col_attempts'                      => 'Intentos',
    'col_time'                          => 'Hora',
    'col_details'                       => 'Detalles',
    'btn_show'                          => 'Mostrar',
    'btn_hide'                          => 'Ocultar',
    'detail_payload'                    => 'Carga útil',
    'detail_response'                   => 'Respuesta',
];
