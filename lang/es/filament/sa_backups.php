<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Backups page — cadenas del panel de Filament (es)
|------------------------------------------------------------
| Acceso vía __('filament/sa_backups.<clave>').
*/

return [
    'title'                            => 'Copias de seguridad y restauración',
    'navigation_label'                 => 'Copias de seguridad',

    // Notificaciones
    'backup_created_title'             => 'Copia de seguridad creada.',
    'backup_failed_title'              => 'Error al crear la copia de seguridad.',
    'backup_deleted_title'             => 'Copia de seguridad eliminada.',
    'backup_delete_failed_title'       => 'No se pudo eliminar la copia de seguridad.',
    'restore_complete_title'           => 'Restauración completada.',
    'restore_complete_body'            => 'Se restauraron :files archivos y :rows sentencias SQL desde :backup.',
    'restore_failed_title'             => 'Error al restaurar.',
    'backup_not_found_title'           => 'Copia de seguridad no encontrada.',
    'backup_healthy_title'             => 'La copia de seguridad parece estar correcta.',
    'backup_healthy_body'              => ':name — :count sentencias SQL detectadas.',
    'backup_verify_failed_title'       => 'Falló la verificación de la copia de seguridad.',

    // Acciones de cabecera
    'action_create'                    => 'Crear copia de seguridad ahora',

    // Modal de restauración
    'restore_modal_heading'            => '¿Restaurar esta copia de seguridad?',
    'restore_modal_description'        => 'La restauración sobrescribe todas las tablas de la base de datos y los archivos subidos con el contenido del archivo seleccionado. El estado actual no podrá recuperarse sin otra copia de seguridad: cree una nueva primero si quiere disponer de una opción de retroceso.',
    'restore_modal_submit'             => 'Sí, restaurar',

    // Modal de eliminación
    'delete_modal_heading'             => '¿Eliminar esta copia de seguridad?',
    'delete_modal_description'         => 'El archivo se eliminará permanentemente del disco. Sus demás copias de seguridad no se verán afectadas, pero esta instantánea concreta no podrá recuperarse tras la eliminación.',
    'delete_modal_submit'              => 'Eliminar',

    // Hero / contenido de la página
    'hero_eyebrow'                     => 'Sistema',
    'hero_title'                       => 'Copias de seguridad y restauración',
    'hero_sub_html'                    => 'Cada copia de seguridad agrupa su base de datos y los archivos subidos en un único zip con marca de tiempo bajo <code>storage/app/backups/</code>. Use el botón «Crear copia de seguridad ahora» de la cabecera antes de operaciones de riesgo y restaure con un clic cuando necesite revertir.',
    'empty_no_backups'                 => 'Aún no hay copias de seguridad. Haga clic en «Crear copia de seguridad ahora» para crear la primera.',

    // Columnas de la tabla
    'col_archive'                      => 'Archivo',
    'col_created'                      => 'Creada',
    'col_size'                         => 'Tamaño',
    'col_actions'                      => 'Acciones',

    // Botones de acción por fila
    'btn_download'                     => 'Descargar',
    'btn_verify'                       => 'Verificar',
    'btn_restore'                      => 'Restaurar',
    'btn_delete'                       => 'Eliminar',

    // Banner del interruptor nocturno
    'nightly_status_strong'            => 'Copias de seguridad nocturnas: :state.',
    'nightly_state_enabled'            => 'activadas',
    'nightly_state_disabled'           => 'desactivadas',
    'nightly_enabled_description'      => 'Cada noche a las 02:00 UTC se crea una copia de seguridad automáticamente mediante el ejecutor de tareas programadas.',
    'nightly_disabled_link_text'       => 'Configuración → Ajustes de script',
    'nightly_disabled_prefix'          => 'Active las copias de seguridad nocturnas en ',
    'nightly_disabled_suffix'          => ' para una protección automática.',
    'nightly_footer_note'              => 'Los botones de arriba son para copias de seguridad puntuales y previas a una actualización.',
];
