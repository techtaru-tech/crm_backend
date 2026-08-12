<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Data Privacy page — translation strings
|--------------------------------------------------------------------------
|
| Strings used by the tenant-side Data Privacy page (Filament).  Implements
| GDPR Article 15 (Right of Access) and Article 17 (Right to Erasure) UI.
|
| Buyers can adapt or localise the copy by editing this file or by copying
| it to lang/<locale>/data_privacy.php and translating.
|
| Placeholders use Laravel's :placeholder convention.
|
*/

return [

    'error_no_tenant'             => 'No pudimos identificar su espacio de trabajo. Cierre sesión y vuelva a iniciarla.',

    // --- Right of Access — Data Export ----------------------------------
    'export_title'                => 'Exportar mis datos',
    'export_description'          => 'Crearemos un archivo ZIP con todos los registros que conservamos para este espacio de trabajo — clientes potenciales, formularios, automatizaciones, configuración, todo — en formato JSON. El enlace de descarga es válido durante 48 horas.',

    'export_building_title'       => 'Construyendo su exportación…',
    'export_building_body'        => 'Actualice la página en unos minutos. Los espacios de trabajo grandes pueden tardar un rato.',

    'export_failed_title'         => 'La última exportación falló.',
    'export_failed_body'          => 'Intente solicitar una nueva — si vuelve a fallar, contacte con soporte.',
    'export_failed_btn'           => 'Intentar de nuevo',

    'export_ready_title'          => '✅ Su archivo está listo',
    'export_ready_size'           => 'Tamaño: :size',
    'export_ready_expires'        => 'Expira :when',
    'export_download_btn'         => 'Descargar ZIP',
    'export_rebuild_btn'          => 'Crear una nueva exportación',
    'export_request_btn'          => 'Exportar mis datos',

    // --- Right to Erasure — Workspace Deletion --------------------------
    'deletion_title'              => 'Eliminar mi espacio de trabajo',
    'deletion_description'        => 'Elimina permanentemente este espacio de trabajo y todos los registros que conservamos para él. Programada con 30 días de antelación para que pueda cambiar de opinión — hasta entonces, el espacio de trabajo permanece activo.',

    'deletion_scheduled_title'    => '⏰ Eliminación programada',
    'deletion_scheduled_on'       => 'El :date',
    'deletion_days_left'          => '(:count día restante)|(:count días restantes)',
    'deletion_cancel_btn'         => 'Cancelar eliminación',

    'deletion_request_confirm'    => '¿Está absolutamente seguro? Esto programa la eliminación permanente de todos los registros de este espacio de trabajo. Dispondrá de 30 días para cancelarla.',
    'deletion_request_btn'        => 'Programar eliminación',

    'deletion_not_owner'          => 'Solo el propietario del espacio de trabajo puede programar la eliminación. Contacte con el propietario si este espacio de trabajo debe eliminarse permanentemente.',

    // --- Footer ---------------------------------------------------------
    'gdpr_footer'                 => 'Estos controles implementan los artículos 15 (Derecho de acceso) y 17 (Derecho de supresión) del GDPR. Para información sobre DPA / subencargados, contacte con soporte.',

    // --- README.txt bundled in the GDPR export ZIP ----------------------
    // Rendered by TenantDataExporter::buildReadme() at request time.
    // :app, :workspace, :timestamp are substituted in PHP, not the
    // translator — we keep them in-template for clarity.
    'readme_title'                => ':app — Exportación de datos personales',
    'readme_divider'              => '============================================================',
    'readme_workspace'            => 'Espacio de trabajo: :workspace',
    'readme_generated'            => 'Generado: :timestamp',
    'readme_intro'                => "Este archivo contiene todos los registros que conservamos para su\nespacio de trabajo, exportados en JSON para garantizar la portabilidad\n(importable en cualquier lugar — CRM, hojas de cálculo, scripts propios).",
    'readme_file_map_header'      => 'Mapa de archivos',
    'readme_subdivider'           => '------------------------------------------------------------',
    'readme_row_readme'           => 'README.txt                         Este archivo',
    'readme_row_tenant'           => 'tenant.json                        Perfil del espacio de trabajo, configuración, marca',
    'readme_row_members'          => 'members.json                       Miembros del equipo + roles',
    'readme_row_leads'            => 'leads.json                         Todos los clientes potenciales',
    'readme_row_companies'        => 'companies.json                     Registros de empresas vinculadas',
    'readme_row_activities'       => 'lead_activities.json               Eventos de cronología',
    'readme_row_notes'            => 'lead_notes.json                    Notas asociadas a clientes potenciales',
    'readme_row_tasks'            => 'lead_tasks.json                    Tareas',
    'readme_row_messages'         => 'lead_messages.json                 Mensajes entrantes / salientes',
    'readme_row_emails'           => 'lead_emails.json                   Correos electrónicos',
    'readme_row_calls'            => 'lead_calls.json                    Registros de llamadas',
    'readme_row_pipelines'        => 'pipelines.json                     Embudos',
    'readme_row_pipeline_stages'  => 'pipeline_stages.json               Etapas',
    'readme_row_tags'             => 'tags.json                          Etiquetas',
    'readme_row_custom_fields'    => 'custom_field_definitions.json      Esquema de campos personalizados',
    'readme_row_forms'            => 'forms.json                         Formularios',
    'readme_row_form_submissions' => 'form_submissions.json              Envíos',
    'readme_row_landing_pages'    => 'landing_pages.json                 Páginas de aterrizaje',
    'readme_row_automations'      => 'automations.json                   Automatizaciones',
    'readme_row_email_sequences'  => 'email_sequences.json               Campañas de goteo',
    'readme_row_email_templates'  => 'email_templates.json               Plantillas',
    'readme_row_products'         => 'products.json                      Catálogo',
    'readme_row_quotes'           => 'quotes.json                        Presupuestos',
    'readme_row_invoices'         => 'invoices.json                      Facturas',
    'readme_row_meeting_types'    => 'meeting_types.json                 Tipos de reunión reservables',
    'readme_row_meeting_bookings' => 'meeting_bookings.json              Reuniones reservadas',
    'readme_row_integrations'     => 'integrations.json                  Integraciones conectadas',
    'readme_row_api_keys'         => 'api_keys.json                      Claves de API (secretos ocultos)',
    'readme_notes_header'         => 'Notas',
    'readme_note_iso8601'         => '- Todos los campos de fecha/hora están en formato ISO-8601.',
    'readme_note_redaction'       => "- Los secretos de claves de API y cualquier columna marcada como\n  secret / token / api_key / key están redactados.",
    'readme_note_attachments'     => "- Los archivos adjuntos se referencian solo por su ruta — para descargar\n  los archivos reales, contacte con soporte.",
    'readme_note_snapshot'        => '- Esta exportación es una instantánea en :timestamp.',

];
