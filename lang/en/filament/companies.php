<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — CompanyResource translation strings
|--------------------------------------------------------------------------
|
| Labels, options, filter copy and action copy for the Companies resource
| at /admin/companies and its LeadsRelationManager (Contacts).
| Consumed via __('filament/companies.<key>').
|
*/

return [

    // ─── Model labels ──────────────────────────────────────────────────
    'model_label'                       => 'Company',
    'plural_model_label'                => 'Companies',

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Companies',

    // ─── Form: company info ────────────────────────────────────────────
    'domain'                            => 'Domain',
    'domain_placeholder'                => 'acme.com',
    'account_owner'                     => 'Account Owner',

    // ─── Industry options ──────────────────────────────────────────────
    'industry_technology'               => 'Technology',
    'industry_finance'                  => 'Finance',
    'industry_healthcare'               => 'Healthcare',
    'industry_retail'                   => 'Retail',
    'industry_manufacturing'            => 'Manufacturing',
    'industry_education'                => 'Education',
    'industry_real_estate'              => 'Real Estate',
    'industry_construction'             => 'Construction',
    'industry_hospitality'              => 'Hospitality',
    'industry_transportation'           => 'Transportation',
    'industry_energy'                   => 'Energy',
    'industry_media'                    => 'Media',
    'industry_nonprofit'                => 'Nonprofit',
    'industry_government'               => 'Government',
    'industry_other'                    => 'Other',

    // ─── Size options ──────────────────────────────────────────────────
    'size_small'                        => 'Small (1-49)',
    'size_medium'                       => 'Medium (50-249)',
    'size_large'                        => 'Large (250-999)',
    'size_enterprise'                   => 'Enterprise (1000+)',
    'size_small_short'                  => 'Small',
    'size_medium_short'                 => 'Medium',
    'size_large_short'                  => 'Large',
    'size_enterprise_short'             => 'Enterprise',

    // ─── Table columns ─────────────────────────────────────────────────
    'contacts'                          => 'Contacts',
    'open_pipeline'                     => 'Open Pipeline',
    'owner'                             => 'Owner',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_status'               => 'Status',

    // ─── LeadsRelationManager (Contacts) ───────────────────────────────
    'relation_title'                    => 'Contacts',
    'lead_status_new'                   => 'New',
    'lead_status_contacted'             => 'Contacted',
    'lead_status_qualified'             => 'Qualified',
    'lead_status_converted'             => 'Converted',
    'lead_status_lost'                  => 'Lost',
    'assigned_to'                       => 'Assigned To',
    'name'                              => 'Name',
    'stage'                             => 'Stage',
    'deal_value'                        => 'Deal Value',
    'action_add_contact'                => 'Add Contact',
    'action_view'                       => 'View',

    // ─── View page: company info section ───────────────────────────────
    'section_company_info'              => 'Company Info',
    'section_deal_summary'              => 'Deal Summary',
    'section_contacts'                  => 'Contacts',
    'section_notes'                     => 'Notes',
    'website'                           => 'Website',
    'phone'                             => 'Phone',
    'industry'                          => 'Industry',
    'size'                              => 'Size',
    'address'                           => 'Address',
    'won_deals'                         => 'Won Deals',

    // ─── View page: tabs ───────────────────────────────────────────────
    'tab_contacts_with_count'           => 'Contacts (:count)',
    'tab_notes'                         => 'Notes',

    // ─── View page: contacts table ─────────────────────────────────────
    'no_contacts'                       => 'No contacts linked to this company yet.',
    'col_name'                          => 'Name',
    'col_email'                         => 'Email',
    'col_status'                        => 'Status',
    'col_deal_value'                    => 'Deal Value',
    'lead_no_name'                      => '(no name)',

    // ─── View page: notes ──────────────────────────────────────────────
    'no_notes'                          => 'No notes yet.',

    // ─── Form field labels (resource) ──────────────────────────────────
    'field_name_label'                  => 'Name',
    'field_industry_label'              => 'Industry',
    'field_size_label'                  => 'Size',
    'field_website_label'               => 'Website',
    'field_phone_label'                 => 'Phone',
    'field_address_label'               => 'Address',
    'field_city_label'                  => 'City',
    'field_country_label'               => 'Country',
    'field_notes_label'                 => 'Notes',

    // ─── Form field labels (LeadsRelationManager) ──────────────────────
    'field_first_name_label'            => 'First Name',
    'field_last_name_label'             => 'Last Name',
    'field_email_label'                 => 'Email',
    'field_lead_phone_label'            => 'Phone',
    'field_source_label'                => 'Source',
    'field_status_label'                => 'Status',

    // ─── Table column labels ───────────────────────────────────────────
    'col_company_name'                  => 'Name',
    'col_domain'                        => 'Domain',
    'col_industry'                      => 'Industry',
    'col_size'                          => 'Size',
    'col_city'                          => 'City',
    'col_country'                       => 'Country',
    'col_created_at'                    => 'Created',

    // ─── Table column labels (LeadsRelationManager) ────────────────────
    'col_lead_email'                    => 'Email',
    'col_lead_phone'                    => 'Phone',
    'col_lead_status'                   => 'Status',
    'col_lead_created_at'               => 'Created',
];
