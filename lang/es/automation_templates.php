<?php

declare(strict_types=1);

return [

    'welcome_new_lead' => [
        'name'        => 'Dar la bienvenida a un nuevo lead',
        'description' => 'Envía un correo de bienvenida cuando se crea un nuevo lead',
    ],

    'hot_lead_alert' => [
        'name'        => 'Alerta de lead caliente',
        'description' => 'Notifica al equipo cuando la puntuación de un lead supera 80',
    ],

    'assign_by_source' => [
        'name'        => 'Asignación por turnos',
        'description' => 'Distribuye los nuevos leads de forma equitativa entre tus comerciales',
    ],

    'followup_no_activity' => [
        'name'        => 'Recordatorio de seguimiento',
        'description' => 'Crea una tarea de seguimiento cuando un lead lleva 3 días sin actividad',
    ],

    'stage_change_notification' => [
        'name'        => 'Notificación de cambio de etapa',
        'description' => 'Notifica al usuario asignado cuando un lead pasa a la etapa Propuesta',
    ],

    'reengage_cold' => [
        'name'        => 'Reactivar leads fríos',
        'description' => 'Envía un correo de reactivación a los leads inactivos durante 30 días',
    ],

    'vip_tagger' => [
        'name'        => 'Auto-etiquetar leads VIP',
        'description' => 'Añade automáticamente la etiqueta VIP cuando el valor del trato supera los 10 000 $',
    ],

    'slack_new_lead' => [
        'name'        => 'Alerta en Slack para nuevo lead',
        'description' => 'Publica en Slack cuando se recibe un nuevo lead',
    ],

    'sla_escalation_hot_lead' => [
        'name'        => 'SLA: escalar leads calientes rápido',
        'description' => 'Si un lead con puntuación > 80 no tiene contacto en 2 horas, notifica al responsable',
    ],

    'lead_recycling' => [
        'name'        => 'Reciclaje de leads',
        'description' => 'Reasigna los leads sin trabajar a otro comercial después de 30 días',
    ],
];
