<?php

declare(strict_types=1);

return [

    'log' => [
        'condition_not_met'   => 'No se cumplió la condición; la automatización se detuvo.',
        'delaying_minutes'    => 'Retrasando :minutes minutos',
        'email_send_failed'   => 'Error al enviar el correo: ',
        'unknown_error'       => 'Error desconocido',
    ],

    'defaults' => [
        'task_title'              => 'Tarea de seguimiento',
        'notify_users_message'    => 'Automatización activada en el lead: :lead',
        'slack_message'           => 'Automatización de LeadHub activada para el lead: :lead',
    ],

    'status' => [
        'success' => 'Éxito',
        'failed'  => 'Fallido',
        'running' => 'En ejecución',
        'pending' => 'Pendiente',
        'partial' => 'Parcial',
        'passed'  => 'Superado',
        'skipped' => 'Omitido',
        'ok'      => 'OK',
    ],

];
