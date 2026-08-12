<?php

declare(strict_types=1);

return [
    'headers' => [
        'id'             => 'ID',
        'first_name'     => 'Nombre',
        'last_name'      => 'Apellido',
        'email'          => 'Correo electrónico',
        'phone'          => 'Teléfono',
        'source'         => 'Fuente',
        'status'         => 'Estado',
        'pipeline_stage' => 'Etapa del pipeline',
        'score'          => 'Puntuación',
        'assigned_to'    => 'Asignado a',
        'tags'           => 'Etiquetas',
        'created_at'     => 'Creado el',
    ],

    'abort_invalid_signature'  => 'Enlace de descarga no válido o caducado.',
    'abort_file_not_found'     => 'Archivo de exportación no encontrado.',
    'abort_tenant_mismatch'    => 'La exportación no pertenece a tu espacio de trabajo.',
];
