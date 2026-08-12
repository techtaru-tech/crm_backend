<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadSourceConnectionResource translation strings
|--------------------------------------------------------------------------
|
| Labels, descriptions, helper text, placeholders and action copy for the
| Lead Sources resource at /admin/lead-source-connections.
| Consumed via __('filament/lead_source_connections.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Orígenes de clientes potenciales',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Origen de cliente potencial',
    'plural_model_label'                => 'Orígenes de clientes potenciales',

    // ─── Form fields & column labels ───────────────────────────────────
    'source'                            => 'Origen',
    'active'                            => 'Activo',
    'col_name'                          => 'Nombre',
    'col_source'                        => 'Origen',
    'col_status'                        => 'Estado',

    // ─── Form: connection details ──────────────────────────────────────
    'connection_name'                   => 'Nombre de la conexión',
    'connection_name_helper'            => 'p. ej., «Facebook – Página principal»',

    // ─── Form: webhook URL section ─────────────────────────────────────
    'your_webhook_url'                  => 'Su URL de Webhook',
    'webhook_url_placeholder_default'   => 'Guarde la conexión primero para obtener la URL',
    'webhook_url_helper'                => 'Copie esta URL y péguela en la configuración de Webhook de la plataforma de origen.',

    // ─── Form: OAuth authorization section ─────────────────────────────
    'oauth_description'                 => 'Guarde primero la conexión y luego haga clic en «Conectar mediante OAuth» en la lista para autorizar el acceso. Las credenciales (token de acceso, token de actualización) se almacenarán automáticamente.',
    'oauth_instruction_text'            => 'Después de guardar esta conexión, use el botón «Conectar mediante OAuth» en la lista de conexiones para autorizar. Se requieren su App ID / Client ID y App Secret / Client Secret antes de redirigir.',

    // ─── Form: API credentials section ─────────────────────────────────
    'credentials_description'           => 'Rellene las credenciales requeridas por este origen. Se almacenan cifradas.',
    'credentials_select_source'         => 'Seleccione un origen arriba para ver las credenciales requeridas.',
    'credentials_none_required'         => 'Este origen no requiere credenciales.',

    // ─── Form: message source settings ─────────────────────────────────
    'message_source_description'        => 'Configure cómo se capturan los mensajes como clientes potenciales.',
    'qualification_keywords'            => 'Palabras clave de cualificación',
    'qualification_keywords_helper'     => 'Se crea un cliente potencial cuando alguien envía un mensaje que contiene una de estas palabras. Déjelo vacío para capturar todos los mensajes.',
    'qualification_keywords_placeholder' => 'Añadir palabra clave…',

    // ─── Form: Meta page selection ─────────────────────────────────────
    'meta_page_description'             => 'Después de conectar mediante OAuth, seleccione qué página de Facebook/Instagram se usará para recuperar clientes potenciales.',
    'active_page'                       => 'Página activa',
    'active_page_helper'                => 'El token de acceso de esta página se usará para recuperar clientes potenciales de la Meta Lead Ads API.',

    // ─── Form: field mapping ───────────────────────────────────────────
    'field_mapping_description'         => 'Asigne los campos del formulario de origen a los campos de cliente potencial de LeadHub (first_name, last_name, email, phone).',
    'field_mapping'                     => 'Asignación de campos',
    'source_field_name'                 => 'Nombre del campo de origen',
    'leadhub_field_value'               => 'Campo de LeadHub (first_name / last_name / email / phone)',

    // ─── Table columns ─────────────────────────────────────────────────
    'leads'                             => 'Clientes potenciales',
    'last_lead'                         => 'Último cliente potencial',
    'webhook_url'                       => 'URL de Webhook',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_source'               => 'Origen',
    'filter_label_status'               => 'Estado',
    'status_connected'                  => 'Conectado',
    'status_disconnected'               => 'Desconectado',
    'status_error'                      => 'Error',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_connect_oauth'              => 'Conectar mediante OAuth',
    'action_connect_oauth_tooltip'      => 'Autorice el acceso mediante OAuth para autorrellenar credenciales',
    'action_test'                       => 'Probar',
    'test_failed_message'               => 'Falló la prueba de conexión',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'No hay orígenes de clientes potenciales conectados',
    'empty_description'                 => 'Conecte un origen de clientes potenciales para empezar a capturarlos.',
];
