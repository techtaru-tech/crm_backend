<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — MeetingBookingResource translation strings
|--------------------------------------------------------------------------
|
| Labels, filter copy and action copy for the Bookings resource at
| /admin/meeting-bookings.
| Consumed via __('filament/meeting_bookings.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                     => 'الحجوزات',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                   => 'حجز',
    'plural_model_label'            => 'الحجوزات',

    // ─── Table columns ─────────────────────────────────────────────────
    'guest_name'                    => 'اسم الضيف',
    'guest_email'                   => 'بريد الضيف',
    'status'                        => 'الحالة',
    'meeting_type'                  => 'نوع الاجتماع',
    'type'                          => 'النوع',
    'host'                          => 'المضيف',
    'starts'                        => 'يبدأ',
    'lead'                          => 'العميل المحتمل',

    // ─── Filter labels ─────────────────────────────────────────────────
    'filter_label_status'           => 'الحالة',

    // ─── Status filter options ─────────────────────────────────────────
    'status_confirmed'              => 'مؤكَّد',
    'status_cancelled'              => 'ملغى',
    'status_completed'              => 'مكتمل',
    'status_no_show'                => 'لم يحضر',

    // ─── View page: infolist field labels ──────────────────────────────
    'field_guest_name_label'        => 'اسم الضيف',
    'field_guest_email_label'       => 'بريد الضيف',
    'field_guest_phone_label'       => 'هاتف الضيف',
    'field_ends_at_label'           => 'ينتهي',
    'field_timezone_label'          => 'المنطقة الزمنية',
    'field_status_label'            => 'الحالة',
    'field_meeting_url_label'       => 'رابط الاجتماع',
    'field_notes_label'             => 'ملاحظات',
    'field_cancelled_at_label'      => 'تاريخ الإلغاء',
    'field_cancellation_reason_label' => 'سبب الإلغاء',

    // ─── Date filters ──────────────────────────────────────────────────
    'filter_from'                   => 'من',
    'filter_until'                  => 'حتى',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_cancel'                 => 'إلغاء',
    'cancellation_reason'           => 'السبب (اختياري)',
    'action_mark_completed'         => 'تعليم كمكتمل',
    'action_mark_no_show'           => 'تعليم كعدم حضور',
];
