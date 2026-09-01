<?php

declare(strict_types=1);

return [
    'lead_created'     => 'تم إنشاء العميل المحتمل من :source',
    'status_changed'   => 'تغيّرت الحالة من :from إلى :to',
    'stage_moved'      => 'تم النقل من :from إلى :to',
    'email_sent'       => 'تم إرسال بريد: :subject',
    'email_received'   => 'تم استلام بريد: :subject',
    'call_logged'      => 'تم تسجيل مكالمة (:direction، :duration دقيقة، :outcome)',
    'note_added'       => 'تمت إضافة ملاحظة داخلية',
    'tag_applied'      => 'تم تطبيق الوسم: :tag',
    'tag_removed'      => 'تمت إزالة الوسم: :tag',
    'assigned'         => 'تم التعيين إلى :to',
    'score_changed'    => 'تغيّرت النقاط من :from إلى :to',
    'imported'         => 'تم الاستيراد من ملف: :filename',
    'booking_made'     => 'حجز :guest اجتماع «:meeting» في :when',
    'call_transcribed' => 'تم نسخ المكالمة وتلخيصها بالذكاء الاصطناعي.',

    // Meeting activity types (spec §10)
    'meeting_scheduled' => 'تم تحديد اجتماع: :meeting :when',
    'meeting_rescheduled' => 'أُعيدت جدولة الاجتماع: :meeting :when',
    'meeting_cancelled' => 'أُلغي الاجتماع: :meeting',
];
