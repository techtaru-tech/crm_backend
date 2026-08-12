<?php

declare(strict_types=1);

return [
    'title'                            => 'صحة النظام',

    // System info labels
    'label_leadhub_version'            => 'إصدار LeadHub',
    'label_laravel'                    => 'Laravel',
    'label_php'                        => 'PHP',
    'label_environment'                => 'البيئة',
    'label_debug_mode'                 => 'وضع التصحيح',
    'label_queue_driver'               => 'مشغّل قوائم الانتظار',
    'label_cache_driver'               => 'مشغّل ذاكرة التخزين المؤقت',
    'label_session_driver'             => 'مشغّل الجلسة',
    'label_mail_driver'                => 'مشغّل البريد',
    'label_database'                   => 'قاعدة البيانات',
    'label_timezone'                   => 'المنطقة الزمنية',
    'label_billing'                    => 'الفوترة',

    // Value strings
    'value_on'                         => 'تشغيل',
    'value_off'                        => 'إيقاف',
    'value_enabled'                    => 'ممكّن',
    'value_disabled'                   => 'معطّل',
    'value_not_available'              => 'غير متاح',

    // Card headings
    'card_system_info'                 => 'معلومات النظام',
    'card_disk_usage'                  => 'استخدام القرص',

    // ─── Disk-usage stat labels ──
    'stat_label_total'                 => 'الإجمالي',
    'stat_label_used'                  => 'المُستخدم',
    'stat_label_free'                  => 'الحر',

    // ─── Disk usage summary (parameterized) ─
    'disk_used_of_total'               => ':used مستخدم من أصل :total',

    // ─── Maintenance actions (shared-hosting friendly) ──
    'card_maintenance'                     => 'الصيانة',

    'action_finalize_update'                 => 'إنهاء التحديث',
    'action_finalize_update_confirm'         => 'يشغّل تسلسل ما بعد التحديث كاملًا بنقرة واحدة: حذف ذاكرات bootstrap القديمة → تطبيق ترحيلات قاعدة البيانات المعلّقة → مسح جميع ذاكرات Laravel المؤقتة (config, route, view, cache) → إعادة بناء ذاكرات الإنتاج (config, route, view, event, مكوّنات Filament, أيقونات blade). استخدمه فور استبدال ملفات التثبيت (مثلًا بعد رفع ملف zip الخاص بـ LeadHub عبر مدير الملفات في cPanel أو بعد رفع ملف إصدار عبر صفحة التحديثات). آمن للنقر في أي وقت — يتم تخطّي الخطوات المطبَّقة سابقًا.',
    'notif_finalize_update_success_title'    => 'تم إنهاء التحديث',
    'notif_finalize_update_success_body'     => 'اكتملت جميع خطوات ما بعد التحديث. تثبيتك الآن متزامن بالكامل مع الكود الجديد.',
    'notif_finalize_update_partial_title'    => 'تم إنهاء التحديث مع تحذيرات',
    'notif_finalize_update_failures_label'   => 'الخطوات التي لم تكتمل:',
    'notif_finalize_update_failed_title'     => 'تعذّر إنهاء التحديث',

    'action_clear_caches'                  => 'مسح جميع ذاكرات التخزين المؤقت',
    'action_clear_caches_confirm'          => 'يشغّل `php artisan optimize:clear` لمسح ذاكرات التخزين المؤقت للإعدادات والمسارات والعروض والأحداث والملفات المُجمَّعة دفعة واحدة. آمن تشغيله في أي وقت — عادةً ما يكون مطلوبًا بعد نشر كود جديد أو تحديث الإعدادات على استضافة مشتركة لا يتوفر فيها SSH.',
    'notif_clear_caches_success_title'     => 'تم مسح ذاكرات التخزين المؤقت',
    'notif_clear_caches_success_body'      => 'تم مسح جميع ذاكرات التخزين المؤقت في Laravel.',
    'notif_clear_caches_failed_title'      => 'تعذّر مسح ذاكرات التخزين المؤقت',

    'action_run_migrations'                  => 'تطبيق ترحيلات قاعدة البيانات المعلّقة',
    'action_run_migrations_confirm'          => 'يشغّل `php artisan migrate --force` لتطبيق جميع ترحيلات قاعدة البيانات المعلّقة. استخدمه فور استبدال ملفات التثبيت (مثلًا بعد رفع ملف zip الخاص بـ LeadHub عبر مدير الملفات في cPanel) حتى تتطابق الأعمدة الجديدة وصفوف الإعدادات مع الكود الجديد. آمن للنقر في أي وقت — يتم تخطّي الترحيلات المطبَّقة سابقًا تلقائيًا.',
    'notif_run_migrations_success_title'     => 'تم تطبيق الترحيلات',
    'notif_run_migrations_success_body'      => 'تم تطبيق جميع ترحيلات قاعدة البيانات المعلّقة. يمكنك الآن إعادة محاولة الإجراء الذي فشل سابقًا.',
    'notif_run_migrations_failed_title'      => 'تعذّر تطبيق الترحيلات',

    'action_rebuild_caches'                => 'إعادة بناء ذاكرات التخزين المؤقت',
    'action_rebuild_caches_confirm'        => 'يمسح ثم يعيد بناء ذاكرات التخزين المؤقت للإعدادات والمسارات والعروض بالتتابع — يُسرّع الطلبات اللاحقة على الاستضافات المشتركة البطيئة. تخطَّه إذا كنت لا تزال في طور التعديل؛ المسح فقط يكفي.',
    'notif_rebuild_caches_success_title'   => 'تمت إعادة بناء ذاكرات التخزين المؤقت',
    'notif_rebuild_caches_success_body'    => 'تم مسح وإعادة بناء ذاكرات التخزين المؤقت للإعدادات والمسارات والعروض.',
    'notif_rebuild_caches_failed_title'    => 'تعذّر إعادة بناء ذاكرات التخزين المؤقت',

    'action_storage_link'                  => 'إنشاء رابط التخزين الرمزي',
    'action_storage_link_confirm'          => 'ينشئ الرابط الرمزي public/storage الذي يستخدمه Laravel لإتاحة الملفات المرفوعة. مطلوب على التثبيتات الجديدة التي لم يُنشأ فيها الرابط أثناء الإعداد (مثلًا منَع open_basedir مُثبّت الإعداد من إنشائه).',
    'notif_storage_link_success_title'     => 'تم إنشاء رابط التخزين الرمزي',
    'notif_storage_link_already_title'     => 'رابط التخزين الرمزي موجود بالفعل',
    'notif_storage_link_failed_title'      => 'تعذّر إنشاء رابط التخزين الرمزي',

    'action_restart_queue'                 => 'إعادة تشغيل عمّال الطوابير',
    'action_restart_queue_confirm'         => 'يُشير إلى عمّال الطوابير قيد التشغيل بإعادة التشغيل برفق لالتقاط الكود الجديد. لا تأثير له على المحرّك `sync` — مناسب فقط إذا كنت تشغّل Horizon أو `queue:work`.',
    'notif_restart_queue_success_title'    => 'تمت الإشارة إلى عمّال الطوابير لإعادة التشغيل',
    'notif_restart_queue_skipped_title'    => 'محرّك الطوابير هو `sync`',
    'notif_restart_queue_skipped_body'     => 'لا تعمل أي عمّال خلفية على المحرّك `sync` — لا شيء لإعادة تشغيله.',
    'notif_restart_queue_failed_title'     => 'تعذّر إعادة تشغيل عمّال الطوابير',
];
