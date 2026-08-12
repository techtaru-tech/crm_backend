<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadCallResource — سلاسل لوحة Filament (ar)
|------------------------------------------------------------
| الوصول عبر __('filament/lead_calls.<key>').
*/

return [

    // ----- التنقّل -----
    'nav_label'   => 'سجلّ المكالمات',

    // ----- تسميات النموذج (مسارات التنقّل / عناوين الصفحات) -----
    'model_label'        => 'مكالمة',
    'plural_model_label' => 'المكالمات',

    // ----- Infolist -----
    'lead'        => 'عميل محتمل',
    'agent'       => 'الوكيل',
    'from'        => 'من',
    'to'          => 'إلى',
    'duration'    => 'المدة',
    'started'     => 'بدأت',
    'recording'   => 'التسجيل',
    'ai_summary'  => 'ملخّص بالذكاء الاصطناعي',
    'transcription' => 'النص المكتوب',

    // ----- الجدول -----
    'col_when'      => 'متى',
    'col_direction' => 'الاتجاه',
    'col_status'    => 'الحالة',

    // ----- المرشّحات -----
    'filter_agent'         => 'الوكيل',
    'filter_label_direction' => 'الاتجاه',
    'filter_label_status'  => 'الحالة',

    // ─── خيارات الاختيار ────────────────────────────────────────────
    'option_inbound'      => 'واردة',
    'option_outbound'     => 'صادرة',
    'option_initiated'    => 'بُدئت',
    'option_ringing'      => 'يرنّ',
    'option_in_progress'  => 'قيد التنفيذ',
    'option_completed'    => 'مكتملة',
    'option_busy'         => 'مشغول',
    'option_failed'       => 'فشلت',
    'option_no_answer'    => 'لا يوجد ردّ',
    'option_canceled'     => 'أُلغيت',

    // ─── سلاسل احتياطية للـ Infolist ────────────────────────────────
    'fallback_unknown'       => '(غير معروف)',
    'fallback_not_available' => '(غير متاح)',

    // ─── تسميات الاتجاه/الحالة (محتوى Placeholder في Infolist) ──
    'direction_inbound'   => 'واردة',
    'direction_outbound'  => 'صادرة',
    'status_initiated'    => 'بُدئت',
    'status_ringing'      => 'يرنّ',
    'status_in_progress'  => 'قيد التنفيذ',
    'status_completed'    => 'مكتملة',
    'status_busy'         => 'مشغول',
    'status_failed'       => 'فشلت',
    'status_no_answer'    => 'لا يوجد ردّ',
    'status_canceled'     => 'أُلغيت',

    // ─── مشغّل التسجيل (resources/views/filament/resources/lead-calls/recording-player.blade.php) ──
    'recording_unsupported' => 'متصفّحك لا يدعم تشغيل الصوت.',
    'recording_download'    => 'تنزيل MP3',

];
