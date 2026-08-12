<?php

declare(strict_types=1);

return [
    'step_type_condition' => 'Condición',
    'step_type_action'    => 'Acción',
    'step_type_delay'     => 'Retraso',

    'action_type_send_email'    => 'Enviar correo al lead',
    'action_type_notify_users'  => 'Enviar notificación interna',
    'action_type_assign_lead'   => 'Asignar lead al usuario',
    'action_type_add_tag'       => 'Añadir etiqueta',
    'action_type_remove_tag'    => 'Eliminar etiqueta',
    'action_type_move_pipeline' => 'Mover a etapa del pipeline',
    'action_type_change_status' => 'Cambiar estado del lead',
    'action_type_send_webhook'  => 'Enviar webhook',
    'action_type_create_task'   => 'Crear tarea / recordatorio',
    'action_type_send_slack'    => 'Enviar notificación de Slack',
    'action_type_send_sms'      => 'Enviar SMS al lead',

    'condition_type_source_is'      => 'La fuente del lead es',
    'condition_type_source_is_not'  => 'La fuente del lead no es',
    'condition_type_has_tag'        => 'El lead tiene la etiqueta',
    'condition_type_not_has_tag'    => 'El lead no tiene la etiqueta',
    'condition_type_field_equals'   => 'El campo del lead es igual a',
    'condition_type_field_contains' => 'El campo del lead contiene',
    'condition_type_field_is_empty' => 'El campo del lead está vacío',
    'condition_type_score_gt'       => 'La puntuación del lead es mayor que',
    'condition_type_score_lt'       => 'La puntuación del lead es menor que',
    'condition_type_assigned_to'    => 'Asignado al usuario',
    'condition_type_unassigned'     => 'Sin asignar',
    'condition_type_time_of_day'    => 'Hora del día',
    'condition_type_day_of_week'    => 'Día de la semana',
];
