<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| EmailSequenceResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/email_sequences.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'          => 'Secuencias de correo',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'        => 'Secuencia de correo',
    'plural_model_label' => 'Secuencias de correo',

    // ----- Form fields -----
    'status'             => 'Estado',

    // ----- itemLabel template strings -----
    'item_label_step_prefix' => 'Paso — ',
    'item_label_day_short'   => 'd',
    'item_label_hour_short'  => 'h',
    'item_label_no_subject'  => '(sin asunto)',

    // ----- Sequence Info -----
    'sequence_name'      => 'Nombre de la secuencia',
    'description'        => 'Descripción',

    // ----- Behavior -----
    'stop_on_reply'      => 'Detener cuando el cliente potencial responda',
    'stop_on_reply_help' => 'Cancelar la inscripción automáticamente cuando se registre un correo entrante del cliente potencial.',
    'stop_on_won'        => 'Detener cuando el cliente potencial se gane',
    'stop_on_won_help'   => 'Cancelar la inscripción automáticamente cuando el estado del cliente potencial pase a «ganado».',

    // ----- Steps -----
    'steps_description'  => 'Los correos se envían de arriba abajo. El retraso se mide desde el paso anterior (o desde la inscripción para el primer paso).',
    'add_step'           => 'Añadir paso',
    'delay_days'         => 'Retraso (días)',
    'delay_hours'        => 'Retraso (horas)',
    'load_template'      => 'Cargar desde plantilla',
    'load_template_help' => 'Elige una plantilla de correo guardada para rellenar el asunto y el cuerpo de abajo. Puedes editarlos después de cargarlos.',
    'subject'            => 'Asunto',
    'subject_help'       => 'Marcadores: {first_name}, {last_name}, {company}, {email}',
    'body'               => 'Cuerpo',
    'body_help'          => 'Marcadores: {first_name}, {last_name}, {company}, {email}',

    // ----- Filter labels -----
    'filter_label_status' => 'Estado',

    // ----- Table -----
    'col_name'           => 'Nombre',
    'col_status'         => 'Estado',
    'col_steps'          => 'Pasos',
    'col_active_enroll'  => 'Inscripciones activas',
    'col_completed'      => 'Completadas',
    'col_created'        => 'Creada',

    // ----- Row actions -----
    'preview'            => 'Vista previa',
    'preview_modal_heading' => 'Vista previa: :name',
    'preview_description' => 'Sustitución de tokens mostrada con datos de muestra — {first_name}=Jane, {last_name}=Doe, {company}=Acme Inc, {email}=jane@acme.com.',
    'preview_close'      => 'Cerrar',
    'send_test'          => 'Enviar prueba',
    'send_test_to'       => 'Enviar prueba a',
    'which_step'         => '¿Qué paso?',
    'duplicate'          => 'Duplicar',

    // ----- Notifications -----
    'notif_step_not_found'        => 'Paso no encontrado.',
    'notif_test_email_sent'       => 'Correo de prueba enviado a :email',
    'notif_test_email_failed'     => 'El envío falló: :error',
    'notif_sequence_duplicated'   => 'Secuencia duplicada.',
    'notif_duplicate_failed'      => 'No se pudo duplicar la secuencia.',

    // ----- Enrollments relation manager -----
    'enrollments_relation_title'  => 'Inscripciones',
    'col_lead'                    => 'Cliente potencial',
    'col_email'                   => 'Correo electrónico',
    'col_step'                    => 'Paso',
    'col_next_send'               => 'Próximo envío',
    'col_next_send_at'            => 'Próximo envío en',
    'col_enrolled_at'             => 'Inscrito el',

    // ----- Preview view -----
    'preview_delay_label'         => 'Retraso',
    'preview_sample_lead'         => 'Vista previa con cliente potencial de muestra',
    'preview_no_steps'            => 'Aún no hay pasos definidos. Añada pasos en la página de edición.',

    // ----- Preview / test send micro-strings -----
    'preview_delay_immediate'     => 'inmediato',
    'test_send_step_option_label' => 'Paso :step — ',
    'test_subject_prefix'         => '[PRUEBA] :subject',
    'preview_sample_first_name'   => 'Jane',
    'preview_sample_last_name'    => 'Doe',
    'preview_sample_company_name' => 'Acme Inc',
    'preview_sample_email'        => 'jane@acme.com',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'          => 'Borrador',
    'option_status_active'         => 'Activa',
    'option_status_paused'         => 'Pausada',
    'option_enrollment_active'     => 'Activa',
    'option_enrollment_completed'  => 'Completada',
    'option_enrollment_replied'    => 'Respondida',
    'option_enrollment_unenrolled' => 'Cancelada',

    // ─── Status badge labels (table column) ────────────────────────
    'status_draft'                 => 'Borrador',
    'status_active'                => 'Activa',
    'status_paused'                => 'Pausada',

    // ─── Duplicate action copy ─────────────────────────────────────
    'duplicate_copy_suffix'        => '(Copia)',

    // ─── Delay format short tokens (preview) ───────────────────────
    'delay_days_short'             => 'd',
    'delay_hours_short'            => 'h',
];
