<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BrandedEmailRenderer — wrap-template translation strings
|--------------------------------------------------------------------------
|
| Footer + copyright strings used by App\Services\BrandedEmailRenderer
| when wrapping outbound emails (sequence sends, automation sends, test
| sends) in the tenant's brand chrome. Consumed via __('branded_email.<key>').
|
*/

return [

    // ─── Footer ──────────────────────────────────────────────────────
    'footer_sent_by'                  => 'Sent by :app',
    'copyright_all_rights_reserved'   => 'All rights reserved.',
];
