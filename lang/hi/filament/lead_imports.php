<?php

declare(strict_types=1);

return [

    'nav_label'                         => 'आयात',

    'model_label'                       => 'लीड आयात',
    'plural_model_label'                => 'लीड आयात',

    'status'                            => 'स्थिति',
    'created_at'                        => 'बनाया गया',
    'file'                              => 'फ़ाइल',
    'total'                             => 'कुल',
    'imported'                          => 'आयातित',
    'dupes'                             => 'डुप्लीकेट',
    'errors'                            => 'त्रुटियाँ',
    'imported_by'                       => 'द्वारा आयातित',

    'csv_or_excel'                      => 'CSV या Excel फ़ाइल',

    'field_first_name'                  => 'पहला नाम',
    'field_last_name'                   => 'अंतिम नाम',
    'field_email'                       => 'ईमेल',
    'field_phone'                       => 'फ़ोन',
    'field_source'                      => 'स्रोत',
    'field_status'                      => 'स्थिति',
    'field_notes'                       => 'नोट्स',

    'notif_read_failed_prefix'          => 'फ़ाइल नहीं पढ़ी जा सकी: ',
    'notif_queued_title'                => 'आयात कतारबद्ध! लीड्स शीघ्र ही जोड़ी जाएँगी।',
    'import_csv_excel'                  => 'CSV/Excel आयात करें',

    'job_row_cap_exceeded'              => 'आयात :max पंक्ति सीमा से अधिक है (फ़ाइल में :rows पंक्तियाँ हैं)। कृपया फ़ाइल को छोटे आयातों में विभाजित करें।',

    'step_1_heading'                    => 'चरण 1: फ़ाइल अपलोड करें',
    'upload_and_preview'                => 'अपलोड करें और कॉलम पूर्वावलोकन करें',
    'step_2_heading'                    => 'चरण 2: कॉलम पूर्वावलोकन और मैप करें',
    'preview_paragraph'                 => ':total पंक्तियाँ पाई गईं। नीचे पूर्वावलोकन पहली 10 दिखाता है। प्रत्येक CSV कॉलम को एक लीड फ़ील्ड में मैप करें (बिना मैप किए कॉलम छोड़ दिए जाते हैं)।',
    'recognized_count'                  => 'पहचाने गए: :count',
    'unmapped_count'                    => 'बिना मैप किए: :count',
    'option_skip'                       => '— छोड़ें —',
    'reject_reupload'                   => 'अस्वीकार करें / पुनः अपलोड करें',
    'accept_start_import'               => 'स्वीकार करें और आयात प्रारंभ करें',
    'step_3_heading'                    => 'आयात प्रारंभ!',
    'step_3_body_html'                  => 'आपका आयात कार्य कतारबद्ध हो गया है। लीड्स पृष्ठभूमि में जोड़ी जाएँगी। आप प्रगति के लिए <a href=":url" class="text-primary-600 underline">आयात इतिहास</a> पृष्ठ देख सकते हैं।',
];
