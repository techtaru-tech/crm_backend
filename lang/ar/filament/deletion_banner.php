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

    'title'         => 'مساحة العمل مجدولة للحذف في :when.',
    'body'          => 'سيتم محو كل سجل في هذه المساحة بشكل نهائي في ذلك التاريخ بناءً على طلب المحو وفق المادة 17 من GDPR.',
    'cancel_link'   => 'إلغاء الحذف ←',
    'when_fallback' => 'قريبًا',

];
