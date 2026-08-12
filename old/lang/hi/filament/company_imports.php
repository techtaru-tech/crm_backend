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
    'nav_label'                         => 'कंपनी आयात',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'कंपनी आयात',
    'plural_model_label'                => 'कंपनी आयात',

    // ─── Table columns ─────────────────────────────────────────────────
    'status'                            => 'स्थिति',
    'created_at'                        => 'बनाया गया',
    'file'                              => 'फ़ाइल',
    'total'                             => 'कुल',
    'imported'                          => 'आयातित',
    'dupes'                             => 'डुप्लीकेट',
    'errors'                            => 'त्रुटियाँ',
    'imported_by'                       => 'द्वारा आयातित',

    // ─── Upload step ──────────────────────────────────────────────────
    'csv_or_excel'                      => 'CSV या Excel फ़ाइल',

    // ─── Field options (auto-mapping targets) ─────────────────────────
    'field_name'                        => 'कंपनी का नाम',
    'field_domain'                      => 'डोमेन',
    'field_industry'                    => 'उद्योग',
    'field_size'                        => 'आकार',
    'field_website'                     => 'वेबसाइट',
    'field_phone'                       => 'फ़ोन',
    'field_address'                     => 'पता',
    'field_city'                        => 'शहर',
    'field_country'                     => 'देश',
    'field_notes'                       => 'नोट्स',

    // ─── Notifications ────────────────────────────────────────────────
    'notif_read_failed_prefix'          => 'फ़ाइल नहीं पढ़ी जा सकी: ',
    'notif_queued_title'                => 'आयात कतारबद्ध! कंपनियाँ शीघ्र ही जोड़ी जाएँगी।',
    'import_csv_excel'                  => 'CSV/Excel आयात करें',

    // ─── Job errors (persisted to company_imports.errors JSON) ────────
    'job_row_cap_exceeded'              => 'आयात :max पंक्ति सीमा से अधिक है (फ़ाइल में :rows पंक्तियाँ हैं)। कृपया फ़ाइल को छोटे आयातों में विभाजित करें।',

    // ─── Wizard ───────────────────────────────────────────────────────
    'step_1_heading'                    => 'चरण 1: फ़ाइल अपलोड करें',
    'upload_and_preview'                => 'अपलोड करें और कॉलम पूर्वावलोकन करें',
    'step_2_heading'                    => 'चरण 2: कॉलम पूर्वावलोकन और मैप करें',
    'preview_paragraph'                 => ':total पंक्तियाँ पाई गईं। नीचे पूर्वावलोकन पहली 10 दिखाता है। प्रत्येक CSV कॉलम को एक कंपनी फ़ील्ड में मैप करें (बिना मैप किए कॉलम छोड़ दिए जाते हैं)।',
    'recognized_count'                  => 'पहचाने गए: :count',
    'unmapped_count'                    => 'बिना मैप किए: :count',
    'option_skip'                       => '— छोड़ें —',
    'reject_reupload'                   => 'अस्वीकार करें / पुनः अपलोड करें',
    'accept_start_import'               => 'स्वीकार करें और आयात प्रारंभ करें',
    'step_3_heading'                    => 'आयात प्रारंभ!',
    'step_3_body_html'                  => 'आपका आयात कार्य कतारबद्ध हो गया है। कंपनियाँ पृष्ठभूमि में जोड़ी जाएँगी। प्रगति के लिए <a href=":url" class="text-primary-600 underline">कंपनी आयात</a> सूची देखें।',
];
