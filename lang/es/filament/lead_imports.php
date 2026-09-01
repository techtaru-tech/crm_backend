<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LeadImportResource translation strings
|--------------------------------------------------------------------------
|
| Column labels for the Lead Imports resource at /admin/lead-imports.
| Consumed via __('filament/lead_imports.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Importaciones',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Importación de clientes potenciales',
    'plural_model_label'                => 'Importaciones de clientes potenciales',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'Estado',
    'created_at'                        => 'Creada',
    'file'                              => 'Archivo',
    'total'                             => 'Total',
    'imported'                          => 'Importados',
    'dupes'                             => 'Duplicados',
    'errors'                            => 'Errores',
    'imported_by'                       => 'Importado por',

    // ─── CreateLeadImport upload step ─────────────────────────────────
    'csv_or_excel'                      => 'Archivo CSV o Excel',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_first_name'                  => 'Nombre',
    'field_last_name'                   => 'Apellido',
    'field_full_name'                   => 'Nombre completo (se divide automáticamente)',
    'field_email'                       => 'Correo electrónico',
    'field_phone'                       => 'Teléfono',
    'field_source'                      => 'Origen',
    'field_status'                      => 'Estado',
    'field_notes'                       => 'Notas',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'No se pudo leer el archivo: ',
    'notif_queued_title'                => 'Importación en cola. Los clientes potenciales se añadirán en breve.',
    'import_csv_excel'                  => 'Importar CSV/Excel',

    // ─── Job errors (persisted to lead_imports.errors JSON) ───────────
    'job_row_cap_exceeded'              => 'La importación supera el límite de :max filas (el archivo tiene :rows filas). Divida el archivo en importaciones más pequeñas.',

    // ─── CreateLeadImport wizard ──────────────────────────────────────
    'step_1_heading'                    => 'Paso 1: Subir archivo',
    'upload_and_preview'                => 'Subir y previsualizar columnas',
    'step_2_heading'                    => 'Paso 2: Previsualizar y asignar columnas',
    'preview_paragraph'                 => 'Detectadas :total filas. La vista previa siguiente muestra las primeras 10. Asigne cada columna del CSV a un campo de cliente potencial (las columnas no asignadas se omiten).',
    'recognized_count'                  => 'Reconocidas: :count',
    'unmapped_count'                    => 'Sin asignar: :count',
    'option_skip'                       => '— Omitir —',
    'reject_reupload'                   => 'Rechazar / Volver a subir',
    'accept_start_import'               => 'Aceptar e iniciar importación',
    'step_3_heading'                    => '¡Importación iniciada!',
    'step_3_body_html'                  => 'Su trabajo de importación se ha puesto en cola. Los clientes potenciales se añadirán en segundo plano. Puede consultar el progreso en la página de <a href=":url" class="text-primary-600 underline">Historial de importaciones</a>.',
    'field_city' => 'Ciudad',
    'field_priority' => 'Prioridad',
    'field_assigned_user' => 'Usuario asignado',
    'field_company' => 'Empresa',
    'field_deal_value' => 'Presupuesto / Valor',
    'field_source_id' => 'ID / Referencia',
    'field_contacted_at' => 'Último contacto',
    'field_next_follow_up' => 'Próximo seguimiento',
];
