<?php

return [
    'categories' => [
        'automation'            => 'Automatización',
        'crm'                   => 'CRM',
        'email_marketing'       => 'Email Marketing',
        'communication'         => 'Comunicación',
        'data_and_productivity' => 'Datos y Productividad',
        'other'                 => 'Otro',
    ],

    'labels' => [
        'generic_webhook' => 'Webhook genérico',
        'rest_api_push'   => 'Envío a REST API',
    ],

    'descriptions' => [
        'zapier'          => 'Envía clientes potenciales a más de 5.000 aplicaciones mediante activadores de webhook de Zapier',
        'n8n'             => 'Automatiza flujos de trabajo con nodos de webhook de n8n',
        'make'            => 'Conéctate a módulos de webhook de Make (Integromat)',
        'pabbly'          => 'Envía clientes potenciales a flujos de Pabbly Connect',
        'activepieces'    => 'Activa flujos de Activepieces con nuevos clientes potenciales',
        'workato'         => 'Envía clientes potenciales a recetas de automatización de Workato',
        'hubspot'         => 'Crea o actualiza contactos y negocios en HubSpot CRM',
        'salesforce'      => 'Envía clientes potenciales como Leads o Contactos de Salesforce',
        'pipedrive'       => 'Crea Personas y Negocios en Pipedrive',
        'zoho_crm'        => 'Crea clientes potenciales en Zoho CRM',
        'freshsales'      => 'Crea contactos y negocios en Freshsales',
        'monday'          => 'Crea elementos en tableros de Monday.com',
        'copper'          => 'Crea clientes potenciales o personas en Copper CRM',
        'close'           => 'Crea clientes potenciales y contactos en Close CRM',
        'streak'          => 'Crea cajas en pipelines de Streak',
        'insightly'       => 'Crea clientes potenciales o contactos en Insightly',
        'bitrix24'        => 'Crea clientes potenciales de CRM en Bitrix24',
        'sugarcrm'        => 'Crea registros de cliente potencial en SugarCRM',
        'vtiger'          => 'Crea registros de cliente potencial en Vtiger',
        'mailchimp'       => 'Suscribe clientes potenciales a audiencias de Mailchimp',
        'activecampaign'  => 'Añade contactos a listas de ActiveCampaign',
        'klaviyo'         => 'Añade perfiles a listas de Klaviyo',
        'brevo'           => 'Crea o actualiza contactos en Brevo (Sendinblue)',
        'convertkit'      => 'Suscribe clientes potenciales a secuencias de ConvertKit',
        'drip'            => 'Crea o actualiza suscriptores en Drip',
        'getresponse'     => 'Añade contactos a listas de GetResponse',
        'moosend'         => 'Suscribe clientes potenciales a listas de correo de Moosend',
        'mailerlite'      => 'Añade suscriptores a grupos de MailerLite',
        'slack'           => 'Publica tarjetas de cliente potencial en canales de Slack',
        'microsoft_teams' => 'Publica tarjetas de cliente potencial en canales de Microsoft Teams',
        'twilio'          => 'Envía notificaciones SMS mediante Twilio',
        'vonage'          => 'Envía notificaciones SMS mediante Vonage/Nexmo',
        'intercom'        => 'Crea o actualiza contactos en Intercom',
        'zendesk'         => 'Crea tickets o contactos en Zendesk',
        'google_sheets'   => 'Añade datos del cliente potencial como filas en Google Sheets',
        'notion'          => 'Crea páginas en bases de datos de Notion',
        'airtable'        => 'Crea registros en bases de Airtable',
        'generic_webhook' => 'Envía clientes potenciales a cualquier URL con una carga útil JSON personalizada',
        'rest_api_push'   => 'Envía datos del cliente potencial a un endpoint REST API',
    ],

    'fields' => [
        'generic_webhook' => [
            'webhook_url' => [
                'label' => 'URL del Webhook',
            ],
            'method' => [
                'label' => 'Método HTTP',
            ],
            'auth_type' => [
                'label'   => 'Tipo de autenticación',
                'options' => [
                    'none'    => 'Ninguna',
                    'bearer'  => 'Token Bearer',
                    'api_key' => 'Encabezado de API Key',
                    'basic'   => 'Autenticación básica',
                ],
            ],
            'auth_value' => [
                'label' => 'Token de autenticación / valor de clave',
            ],
            'auth_header' => [
                'label'       => 'Nombre del encabezado de API Key',
                'placeholder' => 'X-API-Key (se usa cuando el tipo de autenticación = encabezado de API Key)',
            ],
            'body_template' => [
                'label' => 'Plantilla del cuerpo JSON',
            ],
            'custom_headers' => [
                'label' => 'Encabezados personalizados (arreglo JSON de objetos {key,value})',
            ],
        ],

        'microsoft_teams' => [
            'webhook_url' => [
                'label' => 'URL del Webhook',
            ],
            'message_template' => [
                'label'       => 'Plantilla del mensaje',
                'placeholder' => 'Opcional: plantilla de texto plano usando {{lead.email}}, {{lead.pipeline_stage}}, etc.',
            ],
        ],

        'slack' => [
            'webhook_url' => [
                'label' => 'URL del Webhook',
            ],
            'message_template' => [
                'label' => 'Plantilla del mensaje',
            ],
        ],

        'hubspot' => [
            '_oauth_info' => [
                'label' => 'Conexión OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'access_token' => [
                'label' => 'Access Token (anulación manual)',
            ],
            'create_deal' => [
                'label'   => 'Crear negocio en la sincronización',
                'options' => [
                    'yes' => 'Sí',
                    'no'  => 'No',
                ],
            ],
            'deal_pipeline' => [
                'label' => 'ID del pipeline de negocios',
            ],
            'deal_stage' => [
                'label' => 'ID de la etapa del negocio',
            ],
        ],

        'salesforce' => [
            '_oauth_info' => [
                'label' => 'Conexión OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'instance_url' => [
                'label' => 'URL de la instancia',
            ],
            'access_token' => [
                'label' => 'Access Token (anulación manual)',
            ],
            'sf_object' => [
                'label' => 'Tipo de objeto',
            ],
        ],

        'pipedrive' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'zoho_crm' => [
            '_oauth_info' => [
                'label' => 'Conexión OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'access_token' => [
                'label' => 'Access Token (anulación manual)',
            ],
            'region' => [
                'label' => 'Región (com, eu, in, com.au)',
            ],
        ],

        'freshsales' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'domain' => [
                'label' => 'Subdominio (p. ej. tuempresa)',
            ],
            'create_deal' => [
                'label'   => 'Crear negocio al sincronizar contacto',
                'options' => [
                    'yes' => 'Sí',
                    'no'  => 'No',
                ],
            ],
            'deal_pipeline_id' => [
                'label' => 'ID del pipeline de negocios (opcional)',
            ],
            'deal_stage_id' => [
                'label' => 'ID de la etapa del negocio (opcional)',
            ],
        ],

        'monday' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'board_id' => [
                'label' => 'ID del tablero',
            ],
        ],

        'copper' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'user_email' => [
                'label' => 'Email del usuario',
            ],
        ],

        'close' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'insightly' => [
            'api_key' => [
                'label' => 'API Key',
            ],
        ],

        'streak' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'pipeline_key' => [
                'label' => 'Clave del pipeline',
            ],
        ],

        'sugarcrm' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'URL de la instancia',
            ],
        ],

        'vtiger' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
            'instance_url' => [
                'label' => 'URL de la instancia',
            ],
        ],

        'mailchimp' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'ID de la audiencia',
            ],
            'data_center' => [
                'label' => 'Centro de datos (p. ej. us1)',
            ],
            'tags' => [
                'label' => 'Etiquetas (separadas por comas)',
            ],
        ],

        'activecampaign' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_url' => [
                'label' => 'URL de la API',
            ],
            'list_id' => [
                'label' => 'ID de la lista (opcional)',
            ],
            'tags' => [
                'label' => 'Etiquetas (separadas por comas)',
            ],
        ],

        'klaviyo' => [
            'api_key' => [
                'label' => 'API Key privada',
            ],
            'list_id' => [
                'label' => 'ID de la lista',
            ],
        ],

        'brevo' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'ID de la lista',
            ],
        ],

        'convertkit' => [
            'api_key' => [
                'label' => 'API Key (pública)',
            ],
            'form_id' => [
                'label' => 'ID del formulario / secuencia',
            ],
        ],

        'drip' => [
            'api_token' => [
                'label' => 'API Token',
            ],
            'account_id' => [
                'label' => 'ID de la cuenta',
            ],
        ],

        'getresponse' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'ID de la campaña',
            ],
        ],

        'moosend' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'list_id' => [
                'label' => 'ID de la lista de correo',
            ],
        ],

        'mailerlite' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'group_id' => [
                'label' => 'ID del grupo',
            ],
        ],

        'twilio' => [
            'account_sid' => [
                'label' => 'Account SID',
            ],
            'auth_token' => [
                'label' => 'Auth Token',
            ],
            'from_number' => [
                'label' => 'Número de remitente',
            ],
            'sms_template' => [
                'label'       => 'Plantilla SMS',
                'placeholder' => 'Nuevo cliente potencial: {{lead.first_name}} ({{lead.email}})',
            ],
        ],

        'vonage' => [
            'api_key' => [
                'label' => 'API Key',
            ],
            'api_secret' => [
                'label' => 'API Secret',
            ],
            'from_number' => [
                'label' => 'Número de remitente',
            ],
            'sms_template' => [
                'label' => 'Plantilla SMS',
            ],
        ],

        'intercom' => [
            'access_token' => [
                'label' => 'Access Token',
            ],
        ],

        'zendesk' => [
            'subdomain' => [
                'label'       => 'Subdominio',
                'placeholder' => 'tuempresa',
            ],
            'email' => [
                'label' => 'Email del administrador',
            ],
            'api_token_zendesk' => [
                'label' => 'API Token',
            ],
            'create_ticket' => [
                'label'   => 'Crear como ticket (en vez de contacto)',
                'options' => [
                    'contact' => 'Contacto (usuario final)',
                    'ticket'  => 'Ticket',
                ],
            ],
            'ticket_subject_template' => [
                'label'       => 'Plantilla del asunto del ticket',
                'placeholder' => 'Nuevo cliente potencial: {{lead.first_name}} {{lead.last_name}}',
            ],
        ],

        'google_sheets' => [
            '_oauth_info' => [
                'label' => 'Conexión OAuth',
            ],
            'client_id' => [
                'label'       => 'OAuth Client ID',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'client_secret' => [
                'label'       => 'OAuth Client Secret',
                'placeholder' => 'Necesario para el flujo OAuth',
            ],
            'access_token' => [
                'label' => 'OAuth Access Token (se completa automáticamente tras OAuth)',
            ],
            'spreadsheet_id' => [
                'label' => 'ID de la hoja de cálculo',
            ],
            'sheet_name' => [
                'label' => 'Nombre de la hoja',
            ],
        ],

        'notion' => [
            'api_key' => [
                'label' => 'Token de integración',
            ],
            'database_id' => [
                'label' => 'ID de la base de datos',
            ],
            'property_mapping' => [
                'label' => 'Mapeo de propiedades (arreglo JSON — dejar en blanco para usar valores predeterminados)',
            ],
        ],

        'airtable' => [
            'api_key' => [
                'label' => 'Personal Access Token',
            ],
            'base_id' => [
                'label' => 'ID de la base',
            ],
            'table_id' => [
                'label' => 'Nombre de la tabla',
            ],
        ],
    ],

    'exceptions' => [
        'unknown_type' => 'Tipo de integración desconocido: :type',
    ],
];
