<?php

declare(strict_types=1);

return [
    'lead_created'     => ':source से लीड बनाई गई',
    'status_changed'   => 'स्थिति :from से :to में बदली',
    'stage_moved'      => ':from से :to में स्थानांतरित',
    'email_sent'       => 'ईमेल भेजा गया: :subject',
    'email_received'   => 'ईमेल प्राप्त हुआ: :subject',
    'call_logged'      => 'कॉल लॉग की गई (:direction, :duration मिनट, :outcome)',
    'note_added'       => 'आंतरिक नोट जोड़ा गया',
    'tag_applied'      => 'टैग लागू: :tag',
    'tag_removed'      => 'टैग हटाया गया: :tag',
    'assigned'         => ':to को असाइन किया गया',
    'score_changed'    => 'स्कोर :from से :to में बदला',
    'imported'         => 'फ़ाइल से आयात किया गया: :filename',
    'booking_made'     => ':guest ने ":meeting" को :when पर बुक किया',
    'call_transcribed' => 'कॉल का AI द्वारा प्रतिलेखन और सारांश किया गया।',

    // Meeting activity types (spec §10)
    'meeting_scheduled' => 'मीटिंग तय हुई: :meeting :when',
    'meeting_rescheduled' => 'मीटिंग पुनर्निर्धारित: :meeting :when',
    'meeting_cancelled' => 'मीटिंग रद्द: :meeting',
];
