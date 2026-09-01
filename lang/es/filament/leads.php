<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | Cadenas de traducción de LeadResource (es)
 |--------------------------------------------------------------------------
 |
 | Todas las cadenas visibles para el usuario utilizadas por LeadResource y
 | sus Páginas + Relation Managers. Las claves son snake_case en inglés y se
 | conservan idénticas a lang/en/filament/leads.php; sólo se traducen los
 | valores. Acceso vía __('filament/leads.<clave>').
 |
 */

return [
    // ─── Navegación ───
    'nav_label'                     => 'Todos los clientes potenciales',

    // ─── Etiquetas del modelo ───
    'model_label'                   => 'Cliente potencial',
    'plural_model_label'            => 'Clientes potenciales',

    // ─── Búsqueda global ───
    'search_result_fallback'        => 'Cliente potencial n.º :id',
    'search_result_email'           => 'Correo electrónico',
    'search_result_source'          => 'Fuente',
    'search_result_status'          => 'Estado',
    'search_result_score'           => 'Puntuación',
    'search_result_score_value'     => ':score pts',

    // ─── Formulario: información de contacto ───
    'first_name'                    => 'Nombre',
    'last_name'                     => 'Apellidos',
    'email'                         => 'Correo electrónico',
    'phone'                         => 'Teléfono',
    'company'                       => 'Empresa',
    'company_name'                  => 'Nombre de la empresa',
    'domain'                        => 'Dominio',
    'industry'                      => 'Sector',

    // ─── Formulario: detalles del cliente potencial ───
    'source'                        => 'Fuente',
    'status'                        => 'Estado',
    'assigned_to'                   => 'Asignado a',
    'pipeline'                      => 'Embudo',
    'stage'                         => 'Etapa',
    'stage_needs_pipeline'      => 'Selecciona primero un pipeline',
    'stage_placeholder'         => 'Selecciona una etapa',
    'stage_needs_pipeline_help' => 'Las etapas pertenecen a un pipeline: elige un Pipeline arriba para poder elegir una Etapa.',
    'score'                         => 'Puntuación',
    'starred'                       => 'Destacado',
    'lead_notes'                    => 'Notas',

    // ─── Formulario: oportunidad ───
    'deal_value'                    => 'Valor de la oportunidad',
    'currency'                      => 'Moneda',
    'expected_close_date'           => 'Fecha de cierre prevista',
    'lost_reason'                   => 'Motivo de pérdida',

    // ─── Formulario: información adicional ───
    'source_reference_id'           => 'ID de referencia de la fuente',
    'last_contacted'                => 'Último contacto',

    // ─── Formulario: atribución ───
    'attribution_description'       => 'Capturado del formulario o widget que creó este cliente potencial.',
    'utm_source'                    => 'UTM Source',
    'utm_medium'                    => 'UTM Medium',
    'utm_campaign'                  => 'UTM Campaign',
    'utm_content'                   => 'UTM Content',
    'utm_term'                      => 'UTM Term',
    'landing_page'                  => 'Página de aterrizaje',
    'referrer'                      => 'Referente',
    'custom_fields_description'     => 'Campos definidos por el inquilino. Configúralos en Ajustes → Campos personalizados.',

    // ─── Columnas de la tabla ───
    'name'                          => 'Nombre',
    'expected_close'                => 'Cierre previsto',
    'tags'                          => 'Etiquetas',
    'assigned'                      => 'Asignado',
    'dup'                           => 'Dup.',
    'waiting_on'                    => 'A la espera de',
    'created_at'                    => 'Creado el',

    // ─── Filtros ───
    'filter_label_source'           => 'Fuente',
    'filter_label_status'           => 'Estado',
    'tag'                           => 'Etiqueta',
    'starred_only'                  => 'Sólo destacados',
    'not_starred'                   => 'No destacados',
    'duplicates_only'               => 'Sólo duplicados',
    'waiting_us'                    => 'Nosotros (ya respondieron)',
    'waiting_them'                  => 'Ellos (les contactamos)',
    'waiting_new'                   => 'Nuevo (sin contacto)',
    'created_from'                  => 'Creado desde',
    'created_until'                 => 'Creado hasta',
    'min_score'                     => 'Puntuación mínima',
    'min_deal_value'                => 'Valor mínimo de la oportunidad',
    'max_deal_value'                => 'Valor máximo de la oportunidad',

    // ─── Acciones de fila ───
    'tooltip_unstar'                => 'Quitar destacado',
    'tooltip_star_this_lead'        => 'Destacar este cliente potencial',
    'tooltip_view_lead'             => 'Ver cliente potencial',
    'tooltip_edit'                  => 'Editar',
    'tooltip_delete'                => 'Eliminar',
    'view_detail_action_label'      => 'Ver detalle',

    // ─── Acciones masivas ───
    'bulk_assign_agent'             => 'Asignar agente',
    'bulk_assign_to'                => 'Asignar a',
    'bulk_leads_assigned'           => 'Clientes potenciales asignados.',
    'bulk_change_status'            => 'Cambiar estado',
    'bulk_status_updated'           => 'Estado actualizado.',
    'bulk_add_tag'                  => 'Añadir etiqueta',
    'bulk_tag_added'                => 'Etiqueta añadida a los clientes potenciales seleccionados.',
    'bulk_remove_tag'               => 'Quitar etiqueta',
    'bulk_tag_removed'              => 'Etiqueta quitada de los clientes potenciales seleccionados.',
    'bulk_move_to_stage'            => 'Mover a etapa',
    'bulk_leads_moved'              => 'Clientes potenciales movidos a la etapa.',
    'bulk_export_csv'               => 'Exportar selección (CSV)',
    'bulk_export_queued'            => 'Exportación en cola: el enlace de descarga llegará en breve.',
    'bulk_run_automation'           => 'Ejecutar automatización',
    'bulk_select_automation'        => 'Seleccionar automatización',
    'bulk_enroll_in_sequence'       => 'Inscribir en secuencia',
    'bulk_sequence'                 => 'Secuencia',
    'bulk_automation_queued'        => 'Automatización en cola para :count cliente(s) potencial(es).',
    'bulk_enrolled_skipped'         => 'Inscritos :added cliente(s) potencial(es). Omitidos :skipped ya inscritos.',

    // ─── Estado vacío ───
    'empty_heading'                 => 'Aún no hay clientes potenciales',
    'empty_description'             => 'Captura tu primer cliente potencial importando un CSV, creando un formulario incrustable o añadiéndolo manualmente.',
    'empty_add_lead'                => 'Añadir cliente potencial',
    'empty_import_csv'              => 'Importar desde CSV',
    'empty_build_form'              => 'Crear formulario de captura',

    // ─── Página de vista: acciones de cabecera ───
    'add_line_item'                 => 'Añadir línea',
    'product'                       => 'Producto',
    'item_name'                     => 'Nombre del artículo',
    'unit_price'                    => 'Precio unitario',
    'discount_percent'              => 'Descuento %',
    'line_item_added'               => 'Línea añadida.',

    'create_task'                   => 'Crear tarea',
    'task_title'                    => 'Título de la tarea',
    'description'                   => 'Descripción',
    'due_at'                        => 'Vence el',
    'priority'                      => 'Prioridad',
    'reminder_at'                   => 'Recordatorio el',
    'reminder_help'                 => 'Por defecto, una hora antes del vencimiento.',
    'task_created'                  => 'Tarea creada.',

    'send_email'                    => 'Enviar correo',
    'load_template'                 => 'Cargar desde plantilla',
    'load_template_help'            => 'Elige una plantilla de correo guardada para rellenar el asunto y el cuerpo. Puedes editarlos antes de enviar.',
    'attachments'                   => 'Adjuntos',
    'no_email_address'              => 'El cliente potencial no tiene dirección de correo electrónico.',
    'email_log_mode_title'          => 'Correo en cola, pero el envío saliente está en modo registro.',
    'email_log_mode_body'           => 'SMTP no está configurado: el mensaje sólo se escribirá en storage/logs/laravel.log. Visita Ajustes → Correo para configurar SMTP y comenzar la entrega.',
    'email_queued'                  => 'Correo en cola para la entrega.',

    'call_lead'                     => 'Llamar',
    'call_modal_heading'            => 'Iniciar una llamada',
    'call_modal_description'        => '¿Iniciar una llamada a :phone? Tu propio teléfono sonará primero y luego se conectará con el cliente potencial.',
    'call_now'                      => 'Llamar ahora',
    'call_no_phone'                 => 'Tu perfil de usuario no tiene número de teléfono: no se puede conectar la llamada.',
    'call_failed_to_start'          => 'No se pudo iniciar la llamada: revisa Mensajería → Configuración de voz.',
    'call_initiated'                => 'Llamada iniciada: contesta tu teléfono.',
    'call_failed_prefix'            => 'La llamada falló: ',

    'send_message'                  => 'Enviar mensaje',
    'conversation_count'            => 'Conversación (:count)',
    'channel'                       => 'Canal',
    'message'                       => 'Mensaje',
    'no_phone_number'               => 'El cliente potencial no tiene número de teléfono.',
    'message_queued'                => 'Mensaje en cola para la entrega.',

    'log_call'                      => 'Registrar llamada',
    'inbound'                       => 'Entrante',
    'outbound'                      => 'Saliente',
    'outcome_connected'             => 'Conectada',
    'outcome_voicemail'             => 'Buzón de voz',
    'outcome_no_answer'             => 'Sin respuesta',
    'outcome_not_interested'        => 'No interesado',
    'outcome_callback'              => 'Solicitó devolución de llamada',
    'call_logged'                   => 'Llamada registrada.',
    'duration'                      => 'Duración',
    'duration_minutes_suffix'       => 'min',
    'outcome'                       => 'Resultado',

    'add_note'                      => 'Añadir nota',
    'mention_label'                 => 'Mencionar miembros del equipo (escribe @ para buscar)',
    'mention_placeholder'           => 'p. ej. @juana',
    'mention_help'                  => 'Separa varias menciones con comas.',
    'note'                          => 'Nota',
    'note_body_help'                => 'Usa @nombre para mencionar a miembros del equipo.',
    'note_added'                    => 'Nota añadida.',

    'move_stage'                    => 'Mover etapa',
    'lead_moved_to_stage'           => 'Cliente potencial movido a la nueva etapa.',

    'assign'                        => 'Asignar',
    'lead_assigned'                 => 'Cliente potencial asignado.',

    'enroll_in_sequence'            => 'Inscribir en secuencia',
    'sequence'                      => 'Secuencia',
    'already_enrolled'              => 'El cliente potencial ya está inscrito en esta secuencia.',
    'lead_enrolled'                 => 'Cliente potencial inscrito en la secuencia.',

    'apply_tags'                    => 'Etiquetas',
    'tags_updated'                  => 'Etiquetas actualizadas.',

    'star'                          => 'Destacar',
    'unstar'                        => 'Quitar destacado',

    'more'                          => 'Más',

    'create_quote'                  => 'Crear presupuesto',
    'create_invoice'                => 'Crear factura',

    'enrich_with_ai'                => 'Enriquecer con IA',
    're_enrich_with_ai'             => 'Volver a enriquecer con IA',
    're_enrich_modal_heading'       => 'Volver a enriquecer cliente potencial',
    're_enrich_modal_description'   => 'Este cliente potencial ya ha sido enriquecido. Ejecutar el enriquecimiento de nuevo sobrescribirá los datos de empresa, sector y ubicación.',
    'enrich_no_email'               => 'El cliente potencial no tiene correo electrónico: no se puede enriquecer sin una dirección de correo.',
    'enrich_queued'                 => 'Enriquecimiento en cola. Los datos aparecerán en breve.',

    'ai_draft_email'                => 'Borrador de correo con IA',
    'email_intent'                  => 'Intención del correo',
    'intent_introduction'           => 'Presentación inicial',
    'intent_follow_up'              => 'Seguimiento',
    'intent_proposal'               => 'Propuesta / próximos pasos',
    'intent_re_engage'              => 'Reactivar cliente potencial frío',
    'intent_closing'                => 'Cierre / confirmación',
    'additional_context'            => 'Contexto adicional (opcional)',
    'additional_context_placeholder'=> 'p. ej. Ayer vieron nuestra página de precios...',
    'ai_draft_failed'               => 'No se pudo generar el borrador. Asegúrate de que la clave API de OpenAI esté configurada.',
    'ai_draft_generated'            => 'Borrador generado',
    'subject_label'                 => 'Asunto',

    'merge_lead'                    => 'Fusionar cliente potencial',
    'merge_into_label'              => 'Fusionar este cliente potencial en (mantener como principal)',
    'merge_into_help'               => 'Se conservará el cliente potencial principal. Este cliente potencial será archivado.',
    'merge_primary_not_found'       => 'Cliente potencial principal no encontrado.',
    'merge_success'                 => 'Clientes potenciales fusionados. Redirigiendo al cliente potencial principal…',
    'merge_option_format'           => ':name — :email (coincidencia en :field)',
    'no_email'                      => 'sin correo electrónico',

    'export_data'                   => 'Exportar datos',

    'send_portal_link'              => 'Enviar enlace del portal',
    'portal_link_heading'           => 'Enviar enlace de acceso al portal',
    'portal_link_description'       => 'Envía un enlace mágico de 30 minutos al correo del cliente potencial para que pueda consultar el estado de su oportunidad y subir documentos a través del portal de clientes.',
    'portal_link_sent_prefix'       => 'Enlace del portal enviado a ',
    'portal_link_failed_prefix'     => 'Error al enviar: ',

    'gdpr_anonymize'                => 'Anonimizar (RGPD)',
    'gdpr_anonymize_heading'        => 'Anonimizar cliente potencial',
    'gdpr_anonymize_description'    => 'Reemplaza toda la información personal con marcadores de posición, pero conserva el registro del cliente potencial y las estadísticas agregadas (valor de la oportunidad, estado, embudo, fuente, etiquetas). Los adjuntos, correos, mensajes, notas y vistas de página se eliminan. Las filas de actividad se mantienen con los metadatos eliminados.',
    'gdpr_anonymize_confirm'        => 'Sí, anonimizar',
    'gdpr_anonymized_success'       => 'Cliente potencial anonimizado. Estadísticas agregadas conservadas.',

    'gdpr_erase'                    => 'Borrar (RGPD)',
    'gdpr_erase_heading'            => 'Borrado RGPD del cliente potencial',
    'gdpr_erase_description'        => 'Elimina permanentemente todo rastro de este cliente potencial: actividades, notas, tareas, adjuntos, correos, mensajes, vistas de páginas web, líneas de oportunidad e inscripciones a secuencias de correo. Esta acción no se puede deshacer.',
    'gdpr_erase_confirm'            => 'Sí, borrar permanentemente',
    'gdpr_erase_success'            => 'Datos del cliente potencial borrados permanentemente.',

    'attachment_uploaded'           => 'Adjunto(s) subido(s).',
    'attachment_deleted'            => 'Adjunto eliminado.',
    'line_item_removed'             => 'Línea eliminada.',

    // ─── Página de edición ───
    'full_detail_view'              => 'Vista completa de detalles',

    // ─── Página de listado ───
    'kanban_board'                  => 'Tablero Kanban',
    'save_filters'                  => 'Guardar filtros',
    'view_name'                     => 'Nombre de la vista',
    'view_name_placeholder'         => 'p. ej. Mis clientes potenciales calientes de esta semana',
    'email_alerts'                  => 'Enviarme un correo cuando coincidan nuevos clientes potenciales',
    'email_alerts_help'             => 'Comprobación horaria: recibirás un resumen cuando nuevos clientes potenciales coincidan con este filtro.',
    'share_with_team'               => 'Compartir con el equipo',
    'share_with_team_help'          => 'Todos los miembros de tu espacio de trabajo pueden cargar esta vista.',
    'filter_view_saved'             => 'Vista de filtros guardada',
    'filter_view_saved_as'          => 'Guardada como «:name».',
    'filter_view_loaded'            => 'Cargada «:name»',

    'saved_views'                   => 'Vistas guardadas',
    'no_saved_views_yet'            => 'Aún no hay vistas guardadas. Aplica filtros y pulsa «Guardar filtros» para crear una.',
    'select_saved_view'             => 'Selecciona una vista guardada',
    'saved_view_not_found'          => 'Vista guardada no encontrada.',
    'placeholder_empty_label'       => '',

    'delete_view'                   => 'Eliminar vista',
    'no_saved_views_to_delete'      => 'No hay vistas guardadas para eliminar.',
    'select_view_to_delete'         => 'Selecciona la vista a eliminar',
    'saved_view_deleted'            => 'Vista guardada eliminada.',

    'export_current_filters'        => 'Exportar (filtros actuales)',
    'export_queued_with_link'       => 'Exportación en cola: recibirás un enlace de descarga en breve.',

    'import_from_crm'               => 'Importar desde CRM',
    'import_modal_heading'          => 'Importar clientes potenciales desde otro CRM',
    'import_modal_description'      => 'Sube un CSV exportado de HubSpot, Pipedrive, Salesforce o cualquier CSV genérico. Detecta automáticamente el proveedor cuando se selecciona «Detección automática».',
    'source_crm'                    => 'CRM de origen',
    'auto_detect_option'            => 'Detectar automáticamente desde las cabeceras del CSV',
    'source_crm_help'               => 'La detección automática analiza la fila de cabeceras. Elige un proveedor específico si la detección automática no reconoce tu formato.',
    'csv_file'                      => 'Archivo CSV',
    'csv_file_help'                 => 'Hasta 20 MB. La primera fila debe contener las cabeceras de columna.',
    'no_workspace_context'          => 'Sin contexto de espacio de trabajo: vuelve a cargar la página.',
    'no_file_uploaded'              => 'No se ha subido ningún archivo.',
    'csv_import_complete'           => 'Importación de CSV completada',

    // ─── Líneas del cuerpo de la notificación de importación CSV ───
    'import_body_imported_count'    => ':count clientes potenciales importados desde :vendor.',
    'import_body_duplicate_count'    => ':count filas ya existían (omitidas como duplicadas).',

    'import_body_skipped_count'     => ':count filas omitidas (sin correo ni teléfono).',
    'import_body_batch_errors'      => ':count error(es) de lote: consulta los registros.',

    // ─── Relación: Correos ───
    'emails_title'                  => 'Correos',
    'from'                          => 'De',
    'body_text'                     => 'Cuerpo (texto)',
    'subject'                       => 'Asunto',
    'sent'                          => 'Enviado',
    'opened'                        => 'Abierto',
    'clicked'                       => 'Con clic',
    'received'                      => 'Recibido',
    'direction'                     => 'Dirección',
    'email_modal_default'           => 'Correo',
    'body'                          => 'Cuerpo',

    // ─── Relación: Mensajes ───
    'messages_title'                => 'Mensajes',
    'channel_whatsapp'              => 'WhatsApp',
    'channel_sms'                   => 'SMS',
    'channel_telegram'              => 'Telegram',
    'channel_viber'                 => 'Viber',
    'status_sent'                   => 'Enviado',
    'status_delivered'              => 'Entregado',
    'status_read'                   => 'Leído',
    'status_failed'                 => 'Fallido',
    'media_url'                     => 'URL del medio',
    'message_modal'                 => 'Mensaje',
    'message_status'                => 'Estado',
    'sent_at'                       => 'Enviado el',

    // ─── Relación: Tareas ───
    'tasks_title'                   => 'Tareas',
    'due'                           => 'Vencimiento',
    'done'                          => 'Hecho',
    'mark_complete'                 => 'Marcar como completada',
    'mark_incomplete'               => 'Marcar como incompleta',
    'reminder_help_short'           => 'Por defecto, una hora antes del vencimiento.',

    // ─── Relación: Líneas de oportunidad ───
    'line_items_title'              => 'Líneas',
    'discount'                      => 'Descuento',
    'quantity'                      => 'Cantidad',
    'total'                         => 'Total',

    // ─── Relación: Inscripciones en secuencias ───
    'email_sequences_title'         => 'Secuencias de correo',
    'step'                          => 'Paso',
    'next_send'                     => 'Próximo envío',
    'unenroll'                      => 'Cancelar inscripción',
    'lead_unenrolled'               => 'Cliente potencial dado de baja.',
    'sequence_status'               => 'Estado',
    'enrolled_at'                   => 'Inscrito el',
    'unenroll_reason_manual'        => 'Cancelado manualmente',
    // Wave BB: motivos persistidos escritos por LeadObserver (won/converted),
    // ProcessEmailSequences (datos faltantes / sin correo) y LeadEmail
    // (respuesta entrante). Traducidos al escribirse para que la columna
    // coincida con el idioma activo en el momento de la inserción.
    'unenroll_reason_converted'     => 'Cliente potencial marcado como convertido',
    'unenroll_reason_won'           => 'Cliente potencial marcado como ganado',
    'unenroll_reason_missing_data'  => 'Falta la secuencia o el cliente potencial',
    'unenroll_reason_no_email'      => 'El cliente potencial no tiene correo electrónico',
    'unenroll_reason_replied'       => 'El cliente potencial respondió',
    // Wave BB: marcadores de posición escritos en LeadActivity.metadata.i18n_params
    // por LeadObserver cuando falta la etapa anterior/nueva del embudo
    // (stage_none_placeholder) o no hay asignado
    // (unassigned_placeholder).
    'stage_none_placeholder'        => 'Ninguna',
    'unassigned_placeholder'        => 'Sin asignar',

    // ─── Relación: Vistas de página ───
    'web_activity_title'            => 'Actividad web',
    'viewed'                        => 'Visto',
    'page_path'                     => 'Ruta',
    'page_title'                    => 'Título',
    'page_utm_source'               => 'UTM Source',

    // ─── Página de vista: subtítulos y cadenas wire:confirm ───
    'view_quotes_heading'           => 'Presupuestos',
    'view_invoices_heading'         => 'Facturas',
    'confirm_delete_attachment'     => '¿Seguro que quieres eliminar este adjunto?',
    'confirm_remove_line_item'      => '¿Eliminar esta línea?',

    // ─── Página de vista: etiquetas Blade y títulos de sección ───
    'section_contact_info'          => 'Información de contacto',
    'section_attachments'           => 'Adjuntos',
    'section_line_items'            => 'Líneas',
    'section_quotes_invoices'       => 'Presupuestos y facturas',
    'section_internal_notes'        => 'Notas internas',
    'section_call_history'          => 'Historial de llamadas',
    'section_web_activity'          => 'Actividad web',
    'section_activity_timeline'     => 'Cronología de actividad',
    'section_custom_fields'         => 'Campos personalizados',
    'section_email_sequences'       => 'Secuencias de correo',
    'section_ai_coaching'           => 'Asesoramiento con IA',
    'section_conversations'         => 'Conversaciones',
    'view_name_label'               => 'Nombre',
    'view_email_label'              => 'Correo electrónico',
    'view_phone_label'              => 'Teléfono',
    'view_source_label'             => 'Fuente',
    'view_status_label'             => 'Estado',
    'view_lead_score_label'         => 'Puntuación del cliente potencial',
    'view_pts_unit'                 => '/ 100 pts',
    'view_no_name'                  => '(sin nombre)',
    'view_dash'                     => '—',
    'view_why_this_score'           => '¿Por qué esta puntuación?',
    'view_default_rule_name'        => 'Regla',
    'view_pts_suffix'               => 'pts',
    'view_assigned_to_label'        => 'Asignado a',
    'view_pipeline_stage_label'     => 'Etapa del embudo',
    'view_tags_label'               => 'Etiquetas',
    'view_company_label'            => 'Empresa',
    'view_job_title_label'          => 'Cargo',
    'view_industry_size_label'      => 'Sector / tamaño',
    'view_employees_suffix'         => 'empleados',
    'view_country_label'            => 'País',
    'view_linkedin_label'           => 'LinkedIn',
    'view_linkedin_view_profile'    => 'Ver perfil →',
    'view_ai_enriched_label'        => 'Enriquecido con IA',
    'view_created_label'            => 'Creado',
    'view_inbox_in'                 => 'Entrada',
    'view_inbox_out'                => 'Salida',
    'view_inbox_status_prefix'      => 'Estado:',

    // ─── Vista del cliente potencial: etiquetas de canal de la bandeja unificada (Pass 22) ───
    // Usadas por resources/views/filament/resources/leads/view.blade.php
    // junto con las claves channel_* existentes (whatsapp, sms, telegram,
    // viber).  Cubren los valores de correo y chat web producidos cuando
    // la bandeja fusiona filas de LeadEmail/LeadMessage.
    'channel_email'                 => 'Correo electrónico',
    'channel_webchat'               => 'Chat web',

    // ─── Vista del cliente potencial: insignia de estado de la bandeja unificada (Pass 22) ───
    // Mapa de los indicadores de estado a nivel de bandeja calculados en línea en
    // leads/view.blade.php (opened/clicked/bounced/sent etc.) para que el
    // texto de la insignia siga el idioma activo, no una forma cruda con ucfirst().
    'inbox_status_opened'           => 'Abierto',
    'inbox_status_clicked'          => 'Con clic',
    'inbox_status_bounced'          => 'Rebotado',
    'inbox_status_sent'             => 'Enviado',
    'inbox_status_delivered'        => 'Entregado',
    'inbox_status_read'             => 'Leído',
    'inbox_status_failed'           => 'Fallido',
    'inbox_status_pending'          => 'Pendiente',
    'view_open_conversation_view'   => 'Abrir vista completa de conversación',
    'view_step_label'               => 'Paso',
    'view_next_label'               => '· próximo :time',
    'view_completed_label'          => '· completado :time',
    'view_no_attachments_yet'       => 'Aún no hay adjuntos.',
    'view_attachment_download_title' => 'Descargar',
    'view_attachment_delete_title'  => 'Eliminar',
    'view_uploading'                => 'Subiendo...',
    'view_save_attachments'         => 'Guardar adjuntos',
    'view_table_item'               => 'Artículo',
    'view_table_qty'                => 'Cant.',
    'view_table_unit_price'         => 'Precio unitario',
    'view_table_discount'           => 'Descuento',
    'view_table_total'              => 'Total',
    'view_table_sku_prefix'         => 'SKU:',
    'view_table_total_label'        => 'Total:',
    'view_remove_item_title'        => 'Eliminar',
    'view_no_line_items'            => 'Aún no hay líneas. Pulsa «Añadir línea» arriba para adjuntar productos o servicios a esta oportunidad.',
    'view_no_quotes_invoices'       => 'Aún no hay presupuestos ni facturas. Usa las acciones «Más → Crear presupuesto / Crear factura» en la cabecera.',
    'view_invoice_due_label'        => 'Vence :date',
    // Insignias de estado para las listas embebidas de Presupuesto / Factura / Llamada en
    // la barra lateral de la vista del cliente potencial. La búsqueda primero por traductor
    // con respaldo ucfirst() para estados desconocidos mantiene legibles las extensiones
    // de enumeraciones personalizadas.
    'view_quote_status_draft'       => 'Borrador',
    'view_quote_status_sent'        => 'Enviado',
    'view_quote_status_accepted'    => 'Aceptado',
    'view_quote_status_declined'    => 'Rechazado',
    'view_quote_status_expired'     => 'Caducado',
    'view_quote_status_converted'   => 'Convertido',
    'view_invoice_status_draft'     => 'Borrador',
    'view_invoice_status_sent'      => 'Enviada',
    'view_invoice_status_partial'   => 'Parcial',
    'view_invoice_status_overdue'   => 'Vencida',
    'view_invoice_status_paid'      => 'Pagada',
    'view_invoice_status_cancelled' => 'Cancelada',
    'view_invoice_status_refunded'  => 'Reembolsada',
    'view_call_status_completed'    => 'Completada',
    'view_call_status_failed'       => 'Fallida',
    'view_call_status_canceled'     => 'Cancelada',
    'view_call_status_no_answer'    => 'Sin respuesta',
    'view_call_status_busy'         => 'Ocupado',
    'view_note_system'              => 'Sistema',
    'view_note_mentioned_label'     => 'Mencionados:',
    'view_no_internal_notes'        => 'Aún no hay notas internas.',
    'view_call_agent_default'       => 'Agente',
    'view_call_ai_summary'          => 'Resumen con IA',
    'view_utm_prefix'               => 'utm:',
    'view_activity_by'              => 'por :name',
    'view_no_activity_yet'          => 'Aún no se ha registrado actividad.',
    'view_custom_yes'               => 'Sí',
    'view_custom_no'                => 'No',
    'view_media_attachment'         => '[adjunto multimedia]',

    // ─── Columna «última respuesta por» de la conversación (tabla de clientes potenciales) ──
    'conversation_last_by_us'       => 'Nosotros',
    'conversation_last_by_them'     => 'Ellos',
    'conversation_last_by_new'      => 'Nuevo',

    // ─── Etiquetas de respaldo de insignias (Wave A — formatStateUsing de Filament)
    // Estas claves respaldan los callbacks formatStateUsing() en las insignias de
    // TextColumn en LeadResource y sus RelationManagers.  Cuando la columna se
    // asigna a un enum tipado (p. ej. App\Enums\LeadStatus), el método label()
    // del enum tiene prioridad y estas claves sirven como respaldo de cadena
    // sin procesar para valores heredados de la base de datos que evitan el cast.

    // Estado del cliente potencial — refleja enums/lead_status.php más el alias
    // heredado 'converted' mantenido por el docblock del enum H7.
    'status_new'                    => 'Nuevo',
    'status_contacted'              => 'Contactado',
    'status_qualified'              => 'Cualificado',
    'status_won'                    => 'Ganado',
    'status_converted'              => 'Convertido',
    'status_lost'                   => 'Perdido',

    // Insignias de dirección (Messages relation manager).  Neutrales respecto a marca.
    'direction_inbound'             => 'Entrante',
    'direction_outbound'            => 'Saliente',

    // Insignias de dirección de correo — mantenidas distintas de la dirección de mensaje
    // para que los traductores puedan adaptar la redacción por canal sin acoplamiento.
    'email_direction_inbound'       => 'Entrante',
    'email_direction_outbound'      => 'Saliente',

    // Insignias de estado de mensaje — prefijo de clave distinto del genérico 'status_*'
    // para mantener las traducciones de MessagesRelationManager independientes de las
    // claves de estado del cliente potencial.
    'message_status_sent'           => 'Enviado',
    'message_status_delivered'      => 'Entregado',
    'message_status_read'           => 'Leído',
    'message_status_failed'         => 'Fallido',

    // Insignias de prioridad de tarea (TasksRelationManager).
    'task_priority_urgent'          => 'Urgente',
    'task_priority_high'            => 'Alta',
    'task_priority_normal'          => 'Normal',
    'task_priority_low'             => 'Baja',

    // Insignias de estado de inscripción en secuencia de correo.
    'enrollment_status_active'      => 'Activa',
    'enrollment_status_completed'   => 'Completada',
    'enrollment_status_replied'     => 'Respondida',
    'enrollment_status_unenrolled'  => 'Cancelada',

    // Etiquetas de anonimización RGPD escritas en filas de BD durante el borrado
    // para que los marcadores de posición coincidan con el idioma activo del
    // operador en el momento de la anonimización.
    'gdpr_anonymous'                => 'Anónimo',
    'gdpr_task_label'               => 'Tarea n.º :id',

    // Phase 1 lead-funnel fields
    'city' => 'Ciudad',
    'assigned_team' => 'Equipo asignado',
    'assigned_team_help' => 'El equipo propietario de este lead, independiente del comercial que lo lleve.',
    'next_follow_up' => 'Próximo seguimiento',
    'meeting_generic_label' => 'Reunión',
    'section_history' => 'Historial de etapa y propiedad',
    'history_stage' => 'Cambios de etapa',
    'history_assignment' => 'Cambios de propiedad',
    'history_none' => 'Ninguno',
    'history_unassigned' => 'Sin asignar',
    'history_system' => 'Sistema',
];
