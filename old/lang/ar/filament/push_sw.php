<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Web Push SDK banner (resources/views/filament/push-sw.blade.php)
|------------------------------------------------------------
| Accessed via __('filament/push_sw.<key>').
|
| Strings are emitted via a JSON island and consumed by
| public/js/views/filament/push-sw.js when it builds the
| "enable push notifications" toast banner.
*/

return [

    // ─── Toast banner ─────────────────────────────────────────────
    'banner_enable_message' => '🔔 فعّل الإشعارات الفورية لتلقي تنبيهات العملاء المحتملين فورًا.',
    'banner_allow_btn'      => 'سماح',
    // ✕ kept as the literal Unicode close glyph in the view — not English.
];
