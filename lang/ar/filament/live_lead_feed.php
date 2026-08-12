<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LiveLeadFeed — Filament tenant widget strings
|------------------------------------------------------------
| Accessed via __('filament/live_lead_feed.<key>').
*/

return [
    // ─── Header ───
    'title'        => 'موجز العملاء المحتملين المباشر',

    // ─── Empty state ───
    'empty'        => 'لا يوجد عملاء محتملون بعد. سيظهرون هنا في الوقت الفعلي عند وصولهم.',

    // ─── Table columns ───
    'col_name'     => 'الاسم',
    'col_email'    => 'البريد الإلكتروني',
    'col_source'   => 'المصدر',
    'col_status'   => 'الحالة',
    'col_received' => 'الاستلام',

    // ─── Row badges & dynamic labels (rendered via Alpine x-text) ───
    'new_tag'      => 'جديد',

    // Realtime row fallbacks: the websocket broadcast (App\Events\LeadReceived)
    // delivers raw status/source slugs and an Echo callback unshifts them into
    // the Alpine array.  The handler resolves slug→label via the i18n dict
    // injected in the view, but when a slug has no entry we fall back to these
    // generic strings so non-English locales never see the English literal.
    'just_now'      => 'الآن',
    'fallback_status_unknown' => 'غير معروف',
    'fallback_source_manual'  => 'إدخال يدوي',
];
