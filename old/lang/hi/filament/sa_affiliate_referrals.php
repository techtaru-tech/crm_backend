<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin AffiliateReferralResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_affiliate_referrals.<key>').
*/

return [
    'nav_label'            => 'एफिलिएट कमीशन',
    'model_label'          => 'एफिलिएट कमीशन',
    'plural_model_label'   => 'एफिलिएट कमीशन',

    // Table columns
    'col_affiliate'        => 'एफिलिएट',
    'col_referred'         => 'रेफ़र किया गया वर्कस्पेस',
    'col_plan'             => 'प्लान',
    'col_commission'       => 'कमीशन',
    'col_rate'             => 'दर',
    'col_status'           => 'स्थिति',
    'col_booked'           => 'दर्ज किया गया',
    'col_paid_at'          => 'भुगतान तिथि',

    // Filter
    'filter_status'        => 'स्थिति',

    // Row actions
    'action_approve'       => 'स्वीकृत करें',
    'action_mark_paid'     => 'भुगतान किया गया चिह्नित करें',
    'action_reverse'       => 'वापस लें',

    // Row-action notifications
    'notify_approved'      => 'कमीशन स्वीकृत किया गया।',
    'notify_paid'          => 'कमीशन को भुगतान किया गया चिह्नित किया गया।',
    'notify_reversed'      => 'कमीशन वापस ले लिया गया।',

    // Bulk actions
    'bulk_approve'         => 'चयनित स्वीकृत करें',
    'bulk_mark_paid'       => 'चयनित को भुगतान किया गया चिह्नित करें',
    'notify_bulk_approved' => ':count कमीशन स्वीकृत किए गए।',
    'notify_bulk_paid'     => ':count कमीशन को भुगतान किया गया चिह्नित किया गया।',

    // Empty state
    'empty_heading'        => 'अभी तक कोई एफिलिएट कमीशन नहीं',
    'empty_description'    => 'जब कोई रेफ़र किया गया वर्कस्पेस भुगतान करता है तो कमीशन स्वतः दर्ज हो जाते हैं।',
];
