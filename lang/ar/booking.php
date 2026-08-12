<?php

return [
    'confirm_booking' => 'تأكيد الحجز',
    'error_generic'   => 'حدث خطأ ما. يُرجى المحاولة مرة أخرى.',
    'error_network'   => 'خطأ في الشبكة. يُرجى المحاولة مرة أخرى.',

    // Location-type labels for match() in show.blade.php
    'location_google_meet'  => 'Google Meet',
    'location_zoom'         => 'Zoom',
    'location_phone'        => 'مكالمة هاتفية',
    'location_in_person'    => 'حضوريًا',
    'location_details_below'=> 'التفاصيل أدناه',
    'location_label'        => 'الموقع:',

    // Show page headings + form labels
    'duration_minutes'             => ':minutes دقيقة',
    'select_date_and_time'         => 'اختر تاريخًا ووقتًا',
    'aria_previous_month'          => 'الشهر السابق',
    'aria_next_month'              => 'الشهر التالي',
    'dow_mon'                      => 'الإثنين',
    'dow_tue'                      => 'الثلاثاء',
    'dow_wed'                      => 'الأربعاء',
    'dow_thu'                      => 'الخميس',
    'dow_fri'                      => 'الجمعة',
    'dow_sat'                      => 'السبت',
    'dow_sun'                      => 'الأحد',
    'confirm_your_booking_heading' => 'تأكيد حجزك',
    'your_name_label'              => 'اسمك',
    'email_label'                  => 'البريد الإلكتروني',
    'phone_optional_label'         => 'الهاتف (اختياري)',
    'notes_label'                  => 'هل ترغب في مشاركة أي شيء؟',
    'back'                         => 'رجوع',

    // ─── Confirmation page ────────────────────────────────────
    'cancel'                       => 'إلغاء',
    'confirm_cancel_meeting'       => 'إلغاء هذا الاجتماع؟',

    // ─── Reschedule page ──────────────────────────────────────
    'cancel_existing_booking'      => 'إلغاء الحجز الحالي',
    'confirm_cancel_existing'      => 'إلغاء الحجز الحالي؟',

    // Reschedule view body
    'reschedule_title_prefix'      => 'إعادة جدولة — :name',
    'reschedule_heading'           => 'إعادة جدولة «:name»',
    'reschedule_currently_for'     => 'مجدول حاليًا لـ',
    'reschedule_note'              => 'لإعادة الجدولة، يُرجى إلغاء حجزك الحالي ثم اختيار وقت جديد من صفحة الحجز. (يبقي الأمور بسيطة لك وللمضيف.)',
    'reschedule_pick_new_time'     => 'اختر وقتًا جديدًا',
    'reschedule_back_to_booking'   => 'العودة إلى الحجز',
    'reschedule_reason_value'      => 'إعادة جدولة',

    // ─── JS runtime strings (show.js — read via data-* attributes) ─
    'loading_times'                => 'جارٍ تحميل الأوقات…',
    'no_available_times'           => 'لا توجد أوقات متاحة في هذا اليوم. جرّب تاريخًا آخر.',
    'could_not_load_times'         => 'تعذّر تحميل الأوقات. يُرجى المحاولة مرة أخرى.',
    'at_time_separator'            => ' في ',
    'booking_in_progress'          => 'جارٍ الحجز…',

    // ─── Confirmation page ────────────────────────────────────
    'confirmation_title_confirmed' => 'تم تأكيد الحجز — :name',
    'confirmation_title_cancelled' => 'تم إلغاء الحجز — :name',
    'confirmation_aria_confirmed'  => 'تم تأكيد الحجز',
    'confirmation_aria_cancelled'  => 'تم إلغاء الحجز',
    'confirmation_heading_confirmed' => 'تم تأكيدك',
    'confirmation_heading_cancelled' => 'تم إلغاء هذا الحجز',
    'confirmation_sub_confirmed'   => 'أرسلنا دعوة تقويم إلى :email.',
    'confirmation_sub_cancelled'   => 'لن يُعقد هذا الاجتماع.',
    'meeting_label_row'            => 'الاجتماع',
    'host_label_row'               => 'المضيف',
    'when_label_row'               => 'الموعد',
    'guest_label_row'              => 'الضيف',
    'location_label_row'           => 'الموقع',
    'location_see_details'         => 'عرض التفاصيل',
    'add_to_calendar_ics'          => 'إضافة إلى التقويم (.ics)',
    'reschedule_action'            => 'إعادة جدولة',
    'book_new_time'                => 'احجز وقتًا جديدًا',

    // ─── Booking show page meta ─────────────────────────────────
    'page_title_book_with'         => ':meeting — احجز مع :host',
    'meta_default_schedule_with'   => 'جدولة اجتماع مع :host',
];
