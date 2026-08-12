<?php

declare(strict_types=1);

return [
    'title'                              => 'العلامة التجارية للبريد الإلكتروني',
    'navigation_label'                   => 'العلامة التجارية للبريد',

    // Header section
    'header_section_description'         => 'الشريط الملوّن في الجزء العلوي من كل بريد صادر. تظل تجاوزات اللون الأساسي الخاصة بالمستأجر فعّالة عندما يحدّد المستأجر لونه الخاص في العلامة التجارية — هذه القيم هي الافتراضيات على مستوى السكربت.',
    'header_style_label'                 => 'النمط',
    'header_style_solid'                 => 'لون صلب',
    'header_style_gradient'              => 'تدرّج خطي',
    'header_color_primary_gradient'      => 'لون بداية التدرّج',
    'header_color_primary_solid'         => 'لون الخلفية',
    'header_color_secondary_label'       => 'لون نهاية التدرّج',
    'header_gradient_angle_label'        => 'زاوية التدرّج (بالدرجات)',
    'header_gradient_angle_helper'       => '0 = من الأسفل إلى الأعلى · 90 = من اليسار إلى اليمين · 135 = قطري (افتراضي) · 180 = من الأعلى إلى الأسفل.',

    // Footer section
    'footer_section_description'         => 'الشريط الصغير في أسفل كل بريد. لون عادي بحكم التصميم — التدرّج هنا يتنافس مع كتلة الدعوة إلى الإجراء أعلاه.',
    'footer_color_label'                 => 'لون الخلفية',
    'footer_text_color_label'            => 'لون النص',
    'footer_text_color_helper'           => 'يجب أن يتباين مع الخلفية أعلاه لتسهيل القراءة.',

    // Notifications & actions
    'save_failed_title'                  => 'تعذّر حفظ العلامة التجارية للبريد',
    'save_failed_body'                   => 'التفاصيل في سجل الخادم. السبب الأكثر شيوعًا: لم يتم تشغيل ترحيل الإعدادات بعد — شغّل `php artisan migrate --force` على الخادم.',
    'saved_title'                        => 'تم حفظ العلامة التجارية للبريد',
    'saved_body'                         => 'تنطبق الألوان الجديدة على كل بريد يُرسل من الآن فصاعدًا.',
    'action_save'                        => 'حفظ',

    // ─── Live preview strip ───
    'preview_title'                      => 'معاينة',
    'preview_subtitle'                   => 'انعكاس لتخطيط البريد الصادر — يتحدّث أثناء تغيير المنتقيات.',
    'preview_sample_greeting'            => 'مرحبًا Jane،',
    'preview_sample_body'                => 'نص نموذجي لمحتوى البريد. تستبدل الرسائل الفعلية هذا بمحتواها الخاص.',
    'preview_footer_reason'              => 'استلمت هذا البريد لأنك مستخدم لـ :app.',
];
