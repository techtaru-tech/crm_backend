<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — OutboundWebhookResource translation strings
|--------------------------------------------------------------------------
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Webhooks salientes',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Webhook saliente',
    'plural_model_label'                => 'Webhooks salientes',

    // ─── Table columns ─────────────────────────────────────────────────
    'col_name'                          => 'Nombre',
    'col_url'                           => 'URL',
    'col_event'                         => 'Evento',
    'col_events'                        => 'Eventos',
    'col_enabled'                       => 'Habilitado',
    'col_created'                       => 'Creado',
    'col_status'                        => 'Estado',

    // ─── Form: webhook configuration ───────────────────────────────────
    'webhook_name'                      => 'Nombre del Webhook',
    'webhook_name_placeholder'          => 'p. ej. Notificar Slack ante nuevo cliente potencial',
    'endpoint_url'                      => 'URL del endpoint',
    'endpoint_url_placeholder'          => 'https://su-endpoint.com/webhook',
    'trigger_events'                    => 'Eventos disparadores',
    'signing_secret'                    => 'Secreto de firma',
    'signing_secret_helper'             => 'Se usa para la firma HMAC-SHA256. Generado automáticamente si se deja vacío.',
    'payload_filters'                   => 'Filtros de payload (opcional)',
    'payload_filters_helper'            => 'Objeto JSON para filtrar qué eventos disparan este Webhook. Solo se dispara cuando TODOS los filtros coinciden. Déjelo vacío para recibir todos los eventos. Claves soportadas: source, status, pipeline_id, pipeline_stage_id, assigned_user_id, tags. El filtro tags se dispara cuando el cliente potencial tiene al menos una etiqueta coincidente. Ejemplo: {"source":["facebook","api"],"status":["new"],"tags":["Hot Lead"]}',
    'payload_filters_placeholder'       => '{"source":["facebook"],"status":["new"]}',
    'enabled'                           => 'Habilitado',

    // ─── Table columns ─────────────────────────────────────────────────
    'deliveries'                        => 'Entregas',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_send_test'                  => 'Enviar prueba',
    'action_view_deliveries'            => 'Ver entregas',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'Aún no hay Webhooks',
    'empty_description'                 => 'Añada un Webhook saliente para recibir notificaciones de eventos en tiempo real.',

    // ─── Delivery Log page ─────────────────────────────────────────────
    'delivery_log_title_prefix'         => 'Registro de entregas: ',
    'action_back_to_webhooks'           => 'Volver a Webhooks',
    'col_http'                          => 'HTTP',
    'col_latency'                       => 'Latencia',
    'col_attempts'                      => 'Intentos',
    'col_sent'                          => 'Enviado',
    'action_payload'                    => 'Payload',
    'sent_payload_modal_prefix'         => 'Payload enviado — ',
    'modal_close'                       => 'Cerrar',
    'action_response'                   => 'Respuesta',
    'response_body_modal_prefix'        => 'Cuerpo de la respuesta — HTTP ',
    'no_response_body'                  => 'No se registró cuerpo de respuesta.',
    'action_retry'                      => 'Reintentar',
    'log_empty_heading'                 => 'Aún no hay entregas',
    'log_empty_description'             => 'Las entregas de prueba y los eventos en vivo aparecerán aquí una vez disparados.',
    'new_webhook'                       => 'Nuevo Webhook',

    // ─── Delivery log: stats cards ─────────────────────────────────────
    'webhook_title_prefix'              => 'Webhook: :name',
    'stat_total_deliveries'             => 'Entregas totales',
    'stat_successful'                   => 'Exitosas',
    'stat_failed'                       => 'Fallidas',

    // ─── Test delivery payload body ────────────────────────────────────
    'test_delivery_body'                => 'Esta es una entrega de prueba desde LeadHub.',

    // ─── Delivery status badges ────────────────────────────────────────
    'status_success'                    => 'Éxito',
    'status_failed'                     => 'Fallida',
    'status_retrying'                   => 'Reintentando',

    // ─── Latency suffix ────────────────────────────────────────────────
    'latency_ms_suffix'                 => 'ms',

    // ─── Event badge labels ────────────────────────────────────────────
    'event_test'                        => 'Prueba',
    'event_lead_created'                => 'Cliente potencial creado',
    'event_lead_updated'                => 'Cliente potencial actualizado',
    'event_lead_deleted'                => 'Cliente potencial eliminado',
    'event_lead_stage_changed'          => 'Etapa del cliente potencial cambiada',
    'event_form_submitted'              => 'Formulario enviado',
    'event_automation_triggered'        => 'Automatización disparada',
];
