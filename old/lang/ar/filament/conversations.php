<?php

declare(strict_types=1);

return [

    // ─── Navigation / title ──────────────────────────────────────────
    'nav_label'                      => 'المحادثات',
    'title'                          => 'المحادثات',

    // ─── Send-message validation ──────────────────────────────────────
    'notif_type_message_first'       => 'اكتب رسالة أولًا.',
    'notif_select_conversation'      => 'اختر محادثة أولًا.',
    'notif_lead_not_found'           => 'العميل المحتمل غير موجود.',

    // ─── Channel not enabled ──────────────────────────────────────────
    'notif_channel_not_enabled'      => ':channel غير مُمكَّن لمساحة العمل هذه.',
    'notif_channel_not_enabled_body' => 'انتقل إلى الإعدادات ← مزوّدو المراسلة لتمكينه.',

    // ─── Success ──────────────────────────────────────────────────────
    'notif_message_queued'           => 'تمت إضافة الرسالة إلى قائمة الانتظار.',

    // ─── Blade view — left panel ──────────────────────────────────────
    'panel_conversations'            => 'المحادثات',
    'empty_no_conversations_p1'      => 'لا توجد محادثات بعد.',
    'empty_no_conversations_p2'      => 'ابدأ واحدة بإرسال رسالة من صفحة عميل محتمل.',
    'out_marker'                     => 'أنت: ',

    // ─── Blade view — right panel ─────────────────────────────────────
    'lead_prefix'                    => 'عميل محتمل رقم ',
    'open_lead'                      => 'فتح العميل المحتمل ←',
    'media_label'                    => '[وسائط]',
    'compose_placeholder'            => 'اكتب رسالة…',
    'compose_via'                    => 'عبر :channel',
    'compose_send'                   => 'إرسال',
    'compose_sending'                => 'جارٍ الإرسال…',
    'empty_thread'                   => 'لا توجد رسائل في هذه المحادثة بعد.',
    'empty_select_msg'               => 'اختر محادثة من اليسار لعرض الرسائل.',
    'empty_select_sub'               => 'أو أرسل رسالة من ملف أي عميل محتمل لبدء سلسلة جديدة.',
    'warning_no_channel'             => 'لا تتوفر قناة مراسلة لهذا العميل المحتمل. مكّن WhatsApp / SMS / Telegram من',
    'warning_no_channel_link'        => 'الإعدادات ← مزوّدو المراسلة',
    'warning_no_channel_suffix'      => 'وتأكد من أن لدى العميل المحتمل رقم هاتف.',
];
