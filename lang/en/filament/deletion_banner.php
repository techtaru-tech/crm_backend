<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Pending-deletion banner (GDPR Article 17 erasure)
|------------------------------------------------------------
| Accessed via __('filament/deletion_banner.<key>').
|
| Rendered by app/Providers/Filament/AdminPanelProvider.php via a
| PAGE_START render hook on every tenant /admin page when the
| workspace owner has scheduled erasure.  The dedicated Blade view
| at resources/views/filament/deletion-banner.blade.php consumes
| these keys.
*/

return [

    'title'         => 'Workspace scheduled for deletion on :when.',
    'body'          => 'Every record in this workspace will be permanently erased on that date under your GDPR Article 17 erasure request.',
    'cancel_link'   => 'Cancel deletion →',
    'when_fallback' => 'soon',

];
