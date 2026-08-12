<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Backups page — سلاسل لوحة Filament (ar)
|------------------------------------------------------------
| الوصول عبر __('filament/sa_backups.<key>').
*/

return [
    'title'                            => 'النسخ الاحتياطية والاستعادة',
    'navigation_label'                 => 'النسخ الاحتياطية',

    // الإشعارات
    'backup_created_title'             => 'تم إنشاء نسخة احتياطية.',
    'backup_failed_title'              => 'فشل إنشاء النسخة الاحتياطية.',
    'backup_deleted_title'             => 'تم حذف النسخة الاحتياطية.',
    'backup_delete_failed_title'       => 'تعذّر حذف النسخة الاحتياطية.',
    'restore_complete_title'           => 'اكتملت الاستعادة.',
    'restore_complete_body'            => 'تمت استعادة :files ملفًا و :rows من جمل SQL من :backup.',
    'restore_failed_title'             => 'فشلت الاستعادة.',
    'backup_not_found_title'           => 'النسخة الاحتياطية غير موجودة.',
    'backup_healthy_title'             => 'تبدو النسخة الاحتياطية سليمة.',
    'backup_healthy_body'              => ':name — تم رصد :count من جمل SQL.',
    'backup_verify_failed_title'       => 'فشل التحقّق من النسخة الاحتياطية.',

    // إجراءات الرأس
    'action_create'                    => 'إنشاء نسخة احتياطية الآن',

    // نافذة الاستعادة
    'restore_modal_heading'            => 'هل تريد استعادة هذه النسخة الاحتياطية؟',
    'restore_modal_description'        => 'الاستعادة تستبدل كل جدول في قاعدة البيانات وكل ملف مرفوع بمحتويات الأرشيف المحدد. لا يمكن استرجاع الحالة الحالية دون نسخة احتياطية منفصلة — أنشئ نسخة احتياطية جديدة أولًا إن أردت خيار التراجع.',
    'restore_modal_submit'             => 'نعم، استعِد',

    // نافذة الحذف
    'delete_modal_heading'             => 'هل تريد حذف هذه النسخة الاحتياطية؟',
    'delete_modal_description'         => 'سيُحذف الأرشيف من القرص نهائيًا. لن تتأثر نسخك الاحتياطية الأخرى، لكن هذه اللقطة بالذات لا يمكن استرجاعها بعد الحذف.',
    'delete_modal_submit'              => 'حذف',

    // Hero / محتوى الصفحة
    'hero_eyebrow'                     => 'النظام',
    'hero_title'                       => 'النسخ الاحتياطية والاستعادة',
    'hero_sub_html'                    => 'تجمع كل نسخة احتياطية قاعدة بياناتك وملفاتك المرفوعة في ملف zip واحد بطابع زمني داخل <code>storage/app/backups/</code>. استخدم زر «إنشاء نسخة احتياطية الآن» في الرأس قبل العمليات الحرجة، واستعد بنقرة واحدة عند الحاجة إلى التراجع.',
    'empty_no_backups'                 => 'لا توجد نسخ احتياطية بعد. اضغط «إنشاء نسخة احتياطية الآن» لإنشاء أول واحدة.',

    // أعمدة الجدول
    'col_archive'                      => 'الأرشيف',
    'col_created'                      => 'أُنشئت',
    'col_size'                         => 'الحجم',
    'col_actions'                      => 'الإجراءات',

    // أزرار إجراءات الصفوف
    'btn_download'                     => 'تنزيل',
    'btn_verify'                       => 'تحقّق',
    'btn_restore'                      => 'استعادة',
    'btn_delete'                       => 'حذف',

    // شريط التبديل الليلي
    'nightly_status_strong'            => 'النسخ الاحتياطية الليلية: :state.',
    'nightly_state_enabled'            => 'مُفعّلة',
    'nightly_state_disabled'           => 'مُعطّلة',
    'nightly_enabled_description'      => 'تُنشَأ نسخة احتياطية جديدة تلقائيًا كل ليلة في الساعة 02:00 UTC عبر مُشغّل المهام المُجدوَلة.',
    'nightly_disabled_link_text'       => 'الإعدادات ← إعدادات السكربت',
    'nightly_disabled_prefix'          => 'فعّل النسخ الاحتياطية الليلية في ',
    'nightly_disabled_suffix'          => ' للحصول على حماية تلقائية.',
    'nightly_footer_note'              => 'الأزرار أعلاه مخصّصة للنسخ الاحتياطية الفورية وما قبل الترقية.',
];
