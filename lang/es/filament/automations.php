<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — Cadenas de traducción de AutomationResource
|--------------------------------------------------------------------------
|
| Etiquetas, textos de ayuda, marcadores de posición, descripciones y
| copia de acciones masivas para el recurso de Automatizaciones en
| /admin/automations. Consumido mediante __('filament/automations.<key>').
|
*/

return [

    // ----- Navegación -----
    'nav_label'           => 'Automatizaciones',
    'nav_badge_tooltip'   => 'Fallos de automatización en las últimas 24 horas',

    // ----- Etiquetas de modelo (migas de pan / títulos de página) -----
    'model_label'         => 'Automatización',
    'plural_model_label'  => 'Automatizaciones',

    // ----- Plantillas de itemLabel del repetidor (iconos emoji literales) -----
    'item_label_condition' => '🔍 Condición: ',
    'item_label_action'    => '⚡ Acción: ',
    'item_label_delay_wait' => '⏱ Esperar ',
    'item_label_delay_default_unit' => 'minutos',

    // ─── Detalles básicos ────────────────────────────────────────────
    'automation_name'                   => 'Nombre de la automatización',
    'description'                       => 'Descripción',
    'active'                            => 'Activa',
    'respect_business_hours'            => 'Respetar el horario laboral',
    'respect_business_hours_help'       => 'Omitir disparadores fuera de la ventana del horario laboral del tenant.',

    // ─── Sección de disparador ──────────────────────────────────────
    'trigger_description'               => 'Defina cuándo se activa esta automatización.',
    'trigger_event'                     => 'Evento disparador',

    // ─── Sección de pasos ──────────────────────────────────────────
    'steps_description'                 => 'Añada condiciones (filtros), acciones y retardos. Los pasos se ejecutan de arriba abajo. Arrastre para reordenar.',
    'add_step'                          => 'Añadir paso',
    'step_type'                         => 'Tipo de paso',

    // ─── Configuración del disparador — por tipo ───────────────────
    'filter_by_sources'                 => 'Filtrar por origen(es)',
    'filter_by_sources_help'            => 'Deje en blanco para activarse en todos los orígenes.',
    'from_stage'                        => 'Desde la etapa',
    'to_stage'                          => 'Hasta la etapa',
    'tag_name'                          => 'Nombre de la etiqueta',
    'score_threshold'                   => 'Umbral de puntuación',
    'crosses'                           => 'Cruza',
    'no_activity_for'                   => 'Sin actividad durante',
    'unit'                              => 'Unidad',
    'form_blank_for_any'                => 'Formulario (deje en blanco para cualquiera)',

    // ─── Configuración de condición ────────────────────────────────
    'condition_type'                    => 'Tipo de condición',
    'source'                            => 'Origen',
    'field_name'                        => 'Nombre del campo',
    'field_name_placeholder'            => 'p. ej. email, status',
    'value'                             => 'Valor',
    'score'                             => 'Puntuación',
    'user'                              => 'Usuario',
    'time_range'                        => 'Rango horario (HH:MM-HH:MM)',
    'time_range_placeholder'            => '09:00-17:00',
    'days'                              => 'Día(s)',
    'days_placeholder'                  => 'Lunes,Martes',

    // ─── Configuración de acción ───────────────────────────────────
    'action'                            => 'Acción',
    'email_template'                    => 'Plantilla de correo electrónico',
    'notify_users'                      => 'Notificar a usuarios',
    'notify_assigned_agent'             => 'Notificar también al agente asignado',
    'custom_message'                    => 'Mensaje personalizado',
    'assignment_mode'                   => 'Modo de asignación',
    'users_round_robin_pool'            => 'Usuarios (grupo de turno rotativo)',
    'target_stage'                      => 'Etapa de destino',
    'new_status'                        => 'Nuevo estado',
    'webhook_url'                       => 'URL del Webhook',
    'hmac_secret'                       => 'Secreto HMAC (opcional)',
    'task_title'                        => 'Título de la tarea',
    'task_title_help'                   => 'Admite {first_name}, {last_name}, {full_name}, {email}, {lead_score}.',
    'due_in_hours'                      => 'Vence en (horas desde ahora)',
    'due_in_hours_help'                 => 'La tarea vencerá este número de horas después de que se active la automatización.',
    'priority'                          => 'Prioridad',
    'assign_task_to'                    => 'Asignar tarea a',
    'assign_task_to_help'               => 'Deje en blanco para recurrir al usuario asignado del cliente potencial.',
    'slack_webhook_url'                 => 'URL del Webhook de Slack',
    'slack_message'                     => 'Mensaje (admite {{lead.first_name}}, etc.)',
    'sms_message'                       => 'Mensaje SMS',
    'sms_message_help'                  => 'Admite {{first_name}}, {{last_name}}, {{full_name}}, {{email}}, {{company}}',

    // ─── Configuración de retardo ──────────────────────────────────
    'wait'                              => 'Esperar',

    // ─── Columnas de tabla ─────────────────────────────────────────
    'name'                              => 'Nombre',
    'trigger'                           => 'Disparador',
    'steps'                             => 'Pasos',
    'runs'                              => 'Ejecuciones',
    'created'                           => 'Creado',

    // ─── Acciones de fila ─────────────────────────────────────────
    'history'                           => 'Historial',

    // ─── Acciones masivas ─────────────────────────────────────────
    'enable_selected'                   => 'Activar seleccionados',
    'disable_selected'                  => 'Desactivar seleccionados',

    // ─── Acciones de cabecera ─────────────────────────────────────
    'browse_templates'                  => 'Explorar plantillas',
    'run_history'                       => 'Historial de ejecuciones',
    'back_to_automation'                => 'Volver a la automatización',

    // ─── Página de historial de ejecuciones ───────────────────────
    'run_history_heading'               => 'Historial de ejecuciones — :name',
    'runs_count'                        => ':count ejecuciones',
    'no_runs_yet'                       => 'Aún no hay ejecuciones. Esta automatización no se ha activado.',
    'col_lead'                          => 'Cliente potencial',
    'col_started'                       => 'Iniciada',
    'col_duration'                      => 'Duración',
    'col_status'                        => 'Estado',
    'col_steps'                         => 'Pasos',
    'btn_hide'                          => 'Ocultar',
    'btn_show'                          => 'Mostrar',
    'btn_show_steps'                    => ':count paso(s)',
    'no_log'                            => 'Sin registro',

    // ─── Opciones de selección ─────────────────────────────────────
    'option_above_threshold'            => 'Por encima del umbral',
    'option_below_threshold'            => 'Por debajo del umbral',
    'option_minutes'                    => 'Minutos',
    'option_hours'                      => 'Horas',
    'option_days'                       => 'Días',
    'option_specific_user'              => 'Usuario específico',
    'option_round_robin'                => 'Turno rotativo',
    'option_lead_status_new'            => 'Nuevo',
    'option_lead_status_contacted'      => 'Contactado',
    'option_lead_status_qualified'      => 'Cualificado',
    'option_lead_status_lost'           => 'Perdido',
    'option_lead_status_won'            => 'Ganado',
];
