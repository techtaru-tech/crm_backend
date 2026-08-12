<?php

declare(strict_types=1);

return [
    'title'                            => 'Salud del sistema',

    // System info labels
    'label_leadhub_version'            => 'Versión de LeadHub',
    'label_laravel'                    => 'Laravel',
    'label_php'                        => 'PHP',
    'label_environment'                => 'Entorno',
    'label_debug_mode'                 => 'Modo de depuración',
    'label_queue_driver'               => 'Driver de colas',
    'label_cache_driver'               => 'Driver de caché',
    'label_session_driver'             => 'Driver de sesión',
    'label_mail_driver'                => 'Driver de correo',
    'label_database'                   => 'Base de datos',
    'label_timezone'                   => 'Zona horaria',
    'label_billing'                    => 'Facturación',

    // Value strings
    'value_on'                         => 'ACTIVADO',
    'value_off'                        => 'DESACTIVADO',
    'value_enabled'                    => 'Habilitado',
    'value_disabled'                   => 'Deshabilitado',
    'value_not_available'              => 'N/D',

    // Card headings
    'card_system_info'                 => 'Información del sistema',
    'card_disk_usage'                  => 'Uso de disco',

    // ─── Disk-usage stat labels ──
    'stat_label_total'                 => 'Total',
    'stat_label_used'                  => 'Usado',
    'stat_label_free'                  => 'Libre',

    // ─── Disk usage summary (parameterized) ─
    'disk_used_of_total'               => ':used usados de :total',

    // ─── Maintenance actions (shared-hosting friendly) ──
    'card_maintenance'                     => 'Mantenimiento',

    'action_finalize_update'                 => 'Finalizar actualización',
    'action_finalize_update_confirm'         => 'Ejecuta toda la secuencia post-actualización en un solo clic: elimina las cachés bootstrap obsoletas → aplica las migraciones pendientes → limpia todas las cachés de Laravel (config, route, view, cache) → reconstruye las cachés de producción (config, route, view, event, componentes de Filament, iconos blade). Úsalo justo después de reemplazar los archivos de instalación (p. ej. subiste el zip de LeadHub mediante el Administrador de Archivos de cPanel O subiste un zip de versión mediante la página Actualizaciones). Seguro de pulsar en cualquier momento — los pasos ya aplicados se omiten.',
    'notif_finalize_update_success_title'    => 'Actualización finalizada',
    'notif_finalize_update_success_body'     => 'Todos los pasos posteriores a la actualización se completaron. Tu instalación está totalmente sincronizada con el código nuevo.',
    'notif_finalize_update_partial_title'    => 'Actualización finalizada con advertencias',
    'notif_finalize_update_failures_label'   => 'Pasos que no se completaron:',
    'notif_finalize_update_failed_title'     => 'No se pudo finalizar la actualización',

    'action_clear_caches'                  => 'Limpiar todas las cachés',
    'action_clear_caches_confirm'          => 'Ejecuta `php artisan optimize:clear` para vaciar las cachés de config, route, view, event y compiled en un solo paso. Seguro de ejecutar en cualquier momento — normalmente necesario después de desplegar código nuevo o actualizar ajustes en un alojamiento compartido sin acceso SSH.',
    'notif_clear_caches_success_title'     => 'Cachés limpiadas',
    'notif_clear_caches_success_body'      => 'Se vaciaron todas las cachés de Laravel.',
    'notif_clear_caches_failed_title'      => 'No se pudieron limpiar las cachés',

    'action_run_migrations'                  => 'Aplicar migraciones pendientes',
    'action_run_migrations_confirm'          => 'Ejecuta `php artisan migrate --force` para aplicar todas las migraciones de base de datos pendientes. Úsalo justo después de reemplazar los archivos de instalación (p. ej. subiste el zip de LeadHub mediante el Administrador de Archivos de cPanel) para que las nuevas columnas y filas de configuración coincidan con el código nuevo. Seguro de pulsar en cualquier momento — las migraciones ya aplicadas se omiten automáticamente.',
    'notif_run_migrations_success_title'     => 'Migraciones aplicadas',
    'notif_run_migrations_success_body'      => 'Se aplicaron todas las migraciones pendientes. Ahora puedes reintentar la acción que falló antes.',
    'notif_run_migrations_failed_title'      => 'No se pudieron aplicar las migraciones',

    'action_rebuild_caches'                => 'Reconstruir cachés',
    'action_rebuild_caches_confirm'        => 'Limpia y luego reconstruye en secuencia las cachés de config, route y view — acelera las peticiones siguientes en hostings compartidos lentos. Omítelo si aún estás iterando; con limpiar suele bastar.',
    'notif_rebuild_caches_success_title'   => 'Cachés reconstruidas',
    'notif_rebuild_caches_success_body'    => 'Las cachés de config, route y view se limpiaron y reconstruyeron.',
    'notif_rebuild_caches_failed_title'    => 'No se pudieron reconstruir las cachés',

    'action_storage_link'                  => 'Crear enlace de almacenamiento',
    'action_storage_link_confirm'          => 'Crea el enlace simbólico public/storage que Laravel usa para exponer los archivos subidos. Necesario en instalaciones nuevas donde el enlace no se creó durante la configuración (p. ej. open_basedir bloqueó el instalador).',
    'notif_storage_link_success_title'     => 'Enlace de almacenamiento creado',
    'notif_storage_link_already_title'     => 'El enlace de almacenamiento ya existe',
    'notif_storage_link_failed_title'      => 'No se pudo crear el enlace de almacenamiento',

    'action_restart_queue'                 => 'Reiniciar workers de cola',
    'action_restart_queue_confirm'         => 'Indica a los workers en ejecución que reinicien correctamente para recoger el código nuevo. Sin efecto en el driver `sync` — sólo relevante si ejecutas Horizon o `queue:work`.',
    'notif_restart_queue_success_title'    => 'Workers de cola señalados para reiniciar',
    'notif_restart_queue_skipped_title'    => 'El driver de cola es `sync`',
    'notif_restart_queue_skipped_body'     => 'No hay workers en segundo plano en el driver `sync` — nada que reiniciar.',
    'notif_restart_queue_failed_title'     => 'No se pudieron reiniciar los workers de cola',
];
