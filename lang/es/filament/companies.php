<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — Cadenas de traducción de CompanyResource
|--------------------------------------------------------------------------
|
| Etiquetas, opciones, copia de filtros y copia de acciones para el recurso
| de Empresas en /admin/companies y su LeadsRelationManager (Contactos).
| Consumido mediante __('filament/companies.<key>').
|
*/

return [

    // ─── Etiquetas de modelo ──────────────────────────────────────────
    'model_label'                       => 'Empresa',
    'plural_model_label'                => 'Empresas',

    // ─── Navegación ───────────────────────────────────────────────────
    'nav_label'                         => 'Empresas',

    // ─── Formulario: información de empresa ───────────────────────────
    'domain'                            => 'Dominio',
    'domain_placeholder'                => 'acme.com',
    'account_owner'                     => 'Propietario de la cuenta',

    // ─── Opciones de sector ───────────────────────────────────────────
    'industry_technology'               => 'Tecnología',
    'industry_finance'                  => 'Finanzas',
    'industry_healthcare'               => 'Sanidad',
    'industry_retail'                   => 'Comercio minorista',
    'industry_manufacturing'            => 'Manufactura',
    'industry_education'                => 'Educación',
    'industry_real_estate'              => 'Inmobiliario',
    'industry_construction'             => 'Construcción',
    'industry_hospitality'              => 'Hostelería',
    'industry_transportation'           => 'Transporte',
    'industry_energy'                   => 'Energía',
    'industry_media'                    => 'Medios',
    'industry_nonprofit'                => 'Sin ánimo de lucro',
    'industry_government'               => 'Gobierno',
    'industry_other'                    => 'Otro',

    // ─── Opciones de tamaño ───────────────────────────────────────────
    'size_small'                        => 'Pequeña (1-49)',
    'size_medium'                       => 'Mediana (50-249)',
    'size_large'                        => 'Grande (250-999)',
    'size_enterprise'                   => 'Empresarial (1000+)',
    'size_small_short'                  => 'Pequeña',
    'size_medium_short'                 => 'Mediana',
    'size_large_short'                  => 'Grande',
    'size_enterprise_short'             => 'Empresarial',

    // ─── Columnas de tabla ────────────────────────────────────────────
    'contacts'                          => 'Contactos',
    'open_pipeline'                     => 'Embudo abierto',
    'owner'                             => 'Propietario',

    // ─── Etiquetas de filtros ─────────────────────────────────────────
    'filter_label_status'               => 'Estado',

    // ─── LeadsRelationManager (Contactos) ─────────────────────────────
    'relation_title'                    => 'Contactos',
    'lead_status_new'                   => 'Nuevo',
    'lead_status_contacted'             => 'Contactado',
    'lead_status_qualified'             => 'Cualificado',
    'lead_status_converted'             => 'Convertido',
    'lead_status_lost'                  => 'Perdido',
    'assigned_to'                       => 'Asignado a',
    'name'                              => 'Nombre',
    'stage'                             => 'Etapa',
    'deal_value'                        => 'Valor del trato',
    'action_add_contact'                => 'Añadir contacto',
    'action_view'                       => 'Ver',

    // ─── Página de vista: sección de información de empresa ───────────
    'section_company_info'              => 'Información de la empresa',
    'section_deal_summary'              => 'Resumen del trato',
    'section_contacts'                  => 'Contactos',
    'section_notes'                     => 'Notas',
    'website'                           => 'Sitio web',
    'phone'                             => 'Teléfono',
    'industry'                          => 'Sector',
    'size'                              => 'Tamaño',
    'address'                           => 'Dirección',
    'won_deals'                         => 'Tratos ganados',

    // ─── Página de vista: pestañas ────────────────────────────────────
    'tab_contacts_with_count'           => 'Contactos (:count)',
    'tab_notes'                         => 'Notas',

    // ─── Página de vista: tabla de contactos ──────────────────────────
    'no_contacts'                       => 'Aún no hay contactos vinculados a esta empresa.',
    'col_name'                          => 'Nombre',
    'col_email'                         => 'Correo electrónico',
    'col_status'                        => 'Estado',
    'col_deal_value'                    => 'Valor del trato',
    'lead_no_name'                      => '(sin nombre)',

    // ─── Página de vista: notas ───────────────────────────────────────
    'no_notes'                          => 'Aún no hay notas.',

    // ─── Etiquetas de campos de formulario (recurso) ──────────────────
    'field_name_label'                  => 'Nombre',
    'field_industry_label'              => 'Sector',
    'field_size_label'                  => 'Tamaño',
    'field_website_label'               => 'Sitio web',
    'field_phone_label'                 => 'Teléfono',
    'field_address_label'               => 'Dirección',
    'field_city_label'                  => 'Ciudad',
    'field_country_label'               => 'País',
    'field_notes_label'                 => 'Notas',

    // ─── Etiquetas de campos (LeadsRelationManager) ───────────────────
    'field_first_name_label'            => 'Nombre',
    'field_last_name_label'             => 'Apellido',
    'field_email_label'                 => 'Correo electrónico',
    'field_lead_phone_label'            => 'Teléfono',
    'field_source_label'                => 'Origen',
    'field_status_label'                => 'Estado',

    // ─── Etiquetas de columnas de tabla ───────────────────────────────
    'col_company_name'                  => 'Nombre',
    'col_domain'                        => 'Dominio',
    'col_industry'                      => 'Sector',
    'col_size'                          => 'Tamaño',
    'col_city'                          => 'Ciudad',
    'col_country'                       => 'País',
    'col_created_at'                    => 'Creada',

    // ─── Etiquetas de columnas (LeadsRelationManager) ─────────────────
    'col_lead_email'                    => 'Correo electrónico',
    'col_lead_phone'                    => 'Teléfono',
    'col_lead_status'                   => 'Estado',
    'col_lead_created_at'               => 'Creado',
];
