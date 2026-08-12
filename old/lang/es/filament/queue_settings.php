<?php

declare(strict_types=1);

return [
    'title'                => 'Estado de la cola y trabajadores',
    'navigation_label'     => 'Cola y trabajadores',

    // Encabezados de sección
    'queue_configuration'  => 'Configuración de la cola',

    // ─── Vista Blade — cuadrícula de configuración ────────────────────
    'connection'           => 'Conexión',
    'driver'               => 'Driver',

    // ─── Vista Blade — Horizon ────────────────────────────────────────
    'horizon_lede'         => 'Laravel Horizon está instalado. Monitoree las colas en tiempo real:',
    'horizon_open_dashboard' => 'Abrir panel de Horizon',

    // ─── Vista Blade — aviso del operador ─────────────────────────────
    'operator_notice_title' => 'Los trabajos en segundo plano son gestionados por el operador del servicio',
    'operator_notice_body' => 'Las automatizaciones, el envío de correos, los informes programados y los recordatorios son procesados por un trabajador en segundo plano configurado a nivel de servidor. Si los trabajos parecen retrasados o atascados, contacte con su proveedor de servicio — ellos gestionan la programación cron y la infraestructura del trabajador en su nombre.',
];
