<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — CompanyImportResource translation strings
|--------------------------------------------------------------------------
|
| Column labels + wizard copy for the Company Imports resource at
| /admin/company-imports. Consumed via __('filament/company_imports.<key>').
| Mirrors filament/lead_imports.php with Company-appropriate wording.
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Importaciones de empresas',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Importación de empresas',
    'plural_model_label'                => 'Importaciones de empresas',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'Estado',
    'created_at'                        => 'Creada',
    'file'                              => 'Archivo',
    'total'                             => 'Total',
    'imported'                          => 'Importadas',
    'dupes'                             => 'Duplicadas',
    'errors'                            => 'Errores',
    'imported_by'                       => 'Importado por',

    // ─── Upload step ──────────────────────────────────────────────────
    'csv_or_excel'                      => 'Archivo CSV o Excel',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_name'                        => 'Nombre de la empresa',
    'field_domain'                      => 'Dominio',
    'field_industry'                    => 'Sector',
    'field_size'                        => 'Tamaño',
    'field_website'                     => 'Sitio web',
    'field_phone'                       => 'Teléfono',
    'field_address'                     => 'Dirección',
    'field_city'                        => 'Ciudad',
    'field_country'                     => 'País',
    'field_notes'                       => 'Notas',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'No se pudo leer el archivo: ',
    'notif_queued_title'                => 'Importación en cola. Las empresas se añadirán en breve.',
    'import_csv_excel'                  => 'Importar CSV/Excel',

    // ─── Job errors (persisted to company_imports.errors JSON) ────────
    'job_row_cap_exceeded'              => 'La importación supera el límite de :max filas (el archivo tiene :rows filas). Divida el archivo en importaciones más pequeñas.',

    // ─── Wizard ───────────────────────────────────────────────────────
    'step_1_heading'                    => 'Paso 1: Subir archivo',
    'upload_and_preview'                => 'Subir y previsualizar columnas',
    'step_2_heading'                    => 'Paso 2: Previsualizar y asignar columnas',
    'preview_paragraph'                 => 'Detectadas :total filas. La vista previa siguiente muestra las primeras 10. Asigne cada columna del CSV a un campo de empresa (las columnas no asignadas se omiten).',
    'recognized_count'                  => 'Reconocidas: :count',
    'unmapped_count'                    => 'Sin asignar: :count',
    'option_skip'                       => '— Omitir —',
    'reject_reupload'                   => 'Rechazar / Volver a subir',
    'accept_start_import'               => 'Aceptar e iniciar importación',
    'step_3_heading'                    => '¡Importación iniciada!',
    'step_3_body_html'                  => 'Su trabajo de importación se ha puesto en cola. Las empresas se añadirán en segundo plano. Consulte la lista de <a href=":url" class="text-primary-600 underline">Importaciones de empresas</a> para ver el progreso.',
];
