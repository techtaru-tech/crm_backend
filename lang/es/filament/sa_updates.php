<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Updates page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_updates.<key>').
*/

return [
    'title'                            => 'Actualizaciones',
    'navigation_label'                 => 'Actualizaciones',

    // Form section
    'apply_section_description'        => 'Suba un paquete de versión .zip para actualizar esta instalación in situ. Se realiza una copia de seguridad de seguridad primero, a menos que la omita explícitamente.',
    'package_label'                    => 'Paquete de actualización (.zip)',
    'package_helper'                   => 'Soportado: cualquier zip cuyo diseño de nivel superior coincida con la distribución de LeadHub.',
    'skip_backup_label'                => 'Omitir copia de seguridad previa a la actualización',
    'skip_backup_helper'               => 'No recomendado. Déjelo desactivado a menos que acabe de tomar una copia de seguridad manualmente.',

    // Notifications - check
    'check_complete_title'             => 'Comprobación de actualización completada.',
    'check_complete_default_body'      => 'Consulte la pancarta de versión actual/última arriba.',
    'check_failed_title'               => 'La comprobación de actualización falló.',

    // Notifications - apply
    'apply_summary_files_written'      => 'Se escribieron :count archivos',
    'apply_summary_version'            => ' · ahora v:version',
    'apply_summary_backup'             => ' · copia de seguridad: :backup',
    'apply_success_title'              => 'Actualización aplicada con éxito.',
    'apply_failed_title'               => 'La actualización falló.',

    // Header actions
    'action_check'                     => 'Comprobar actualizaciones',
    'action_apply'                     => 'Aplicar paquete subido',
    'action_apply_confirmation'        => 'Esto sobrescribirá los archivos de la aplicación con el contenido del zip subido, ejecutará las migraciones pendientes y limpiará todas las cachés. Primero se toma una copia de seguridad previa a la actualización. ¿Continuar?',

    // Section headings
    'update_history'                   => 'Historial de actualizaciones',

    // ─── Blade view (resources/views/filament/super-admin/pages/updates.blade.php) ──
    'installed_version'                => 'Versión instalada',
    'update_available'                 => 'Actualización disponible',
    'view_changelog'                   => 'Ver registro de cambios',
    'on_latest_version'                => 'Está en la última versión.',
    'history_empty'                    => 'Aún no se ha aplicado ninguna actualización.',
    'col_package'                      => 'Paquete',
    'col_backup'                       => 'Copia de seguridad',
    'col_result'                       => 'Resultado',
    'last_checked'                     => 'Última comprobación :time',
    'col_when'                         => 'Cuándo',
    'col_from_to'                      => 'De → A',

    // ─── History badges ───
    'badge_failed'                     => 'fallida',
    'badge_files_written'              => ':count archivos',
];
