<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadCallResource — Filament एडमिन स्ट्रिंग्स (hi)
|------------------------------------------------------------
| __('filament/lead_calls.<key>') के माध्यम से उपयोग किया जाता है।
*/

return [

    // ----- नेविगेशन -----
    'nav_label'   => 'कॉल इतिहास',

    // ----- मॉडल लेबल (ब्रेडक्रंब / पेज शीर्षक) -----
    'model_label'        => 'कॉल',
    'plural_model_label' => 'कॉल',

    // ----- Infolist -----
    'lead'        => 'लीड',
    'agent'       => 'एजेंट',
    'from'        => 'से',
    'to'          => 'तक',
    'duration'    => 'अवधि',
    'started'     => 'प्रारंभ',
    'recording'   => 'रिकॉर्डिंग',
    'ai_summary'  => 'AI सारांश',
    'transcription' => 'ट्रांसक्रिप्शन',

    // ----- तालिका -----
    'col_when'      => 'कब',
    'col_direction' => 'दिशा',
    'col_status'    => 'स्थिति',

    // ----- फ़िल्टर -----
    'filter_agent'         => 'एजेंट',
    'filter_label_direction' => 'दिशा',
    'filter_label_status'  => 'स्थिति',

    // ─── चयन विकल्प ────────────────────────────────────────────
    'option_inbound'      => 'इनबाउंड',
    'option_outbound'     => 'आउटबाउंड',
    'option_initiated'    => 'प्रारंभ की गई',
    'option_ringing'      => 'घंटी बज रही है',
    'option_in_progress'  => 'प्रगति पर',
    'option_completed'    => 'पूर्ण',
    'option_busy'         => 'व्यस्त',
    'option_failed'       => 'विफल',
    'option_no_answer'    => 'कोई उत्तर नहीं',
    'option_canceled'     => 'रद्द',

    // ─── Infolist फ़ॉलबैक स्ट्रिंग्स ────────────────────────────────
    'fallback_unknown'       => '(अज्ञात)',
    'fallback_not_available' => '(उपलब्ध नहीं)',

    // ─── दिशा/स्थिति लेबल (Infolist Placeholder सामग्री) ──
    'direction_inbound'   => 'इनबाउंड',
    'direction_outbound'  => 'आउटबाउंड',
    'status_initiated'    => 'प्रारंभ की गई',
    'status_ringing'      => 'घंटी बज रही है',
    'status_in_progress'  => 'प्रगति पर',
    'status_completed'    => 'पूर्ण',
    'status_busy'         => 'व्यस्त',
    'status_failed'       => 'विफल',
    'status_no_answer'    => 'कोई उत्तर नहीं',
    'status_canceled'     => 'रद्द',

    // ─── रिकॉर्डिंग प्लेयर (resources/views/filament/resources/lead-calls/recording-player.blade.php) ──
    'recording_unsupported' => 'आपका ब्राउज़र ऑडियो प्लेबैक का समर्थन नहीं करता।',
    'recording_download'    => 'MP3 डाउनलोड करें',

];
