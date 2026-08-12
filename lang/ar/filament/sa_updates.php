<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin Updates page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_updates.<key>').
*/

return [
    'title'                            => 'التحديثات',
    'navigation_label'                 => 'التحديثات',

    // Form section
    'apply_section_description'        => 'حمّل حزمة إصدار بصيغة .zip لترقية هذا التثبيت في مكانه. يُؤخذ نسخ احتياطي للسلامة أولًا ما لم تتخطَّه صراحةً.',
    'package_label'                    => 'حزمة التحديث (.zip)',
    'package_helper'                   => 'مدعوم: أي zip يتطابق هيكله العلوي مع توزيع LeadHub.',
    'skip_backup_label'                => 'تخطي النسخ الاحتياطي قبل التحديث',
    'skip_backup_helper'               => 'غير موصى به. اتركه معطَّلًا ما لم تكن قد أخذت نسخة احتياطية يدوية للتو.',

    // Notifications - check
    'check_complete_title'             => 'اكتمل فحص التحديثات.',
    'check_complete_default_body'      => 'راجع شعار الإصدار الحالي/الأحدث أعلاه.',
    'check_failed_title'               => 'فشل فحص التحديثات.',

    // Notifications - apply
    'apply_summary_files_written'      => 'تمت كتابة :count ملف',
    'apply_summary_version'            => ' · الآن الإصدار :version',
    'apply_summary_backup'             => ' · النسخة الاحتياطية: :backup',
    'apply_success_title'              => 'تم تطبيق التحديث بنجاح.',
    'apply_failed_title'               => 'فشل التحديث.',

    // Header actions
    'action_check'                     => 'فحص التحديثات',
    'action_apply'                     => 'تطبيق الحزمة المرفوعة',
    'action_apply_confirmation'        => 'سيؤدي هذا إلى الكتابة فوق ملفات التطبيق بمحتويات zip المرفوعة، وتشغيل عمليات الترحيل المعلقة، ومسح كل ذاكرة تخزين مؤقت. يُؤخذ نسخ احتياطي قبل التحديث أولًا. هل تريد المتابعة؟',

    // Section headings
    'update_history'                   => 'سجل التحديثات',

    // ─── Blade view (resources/views/filament/super-admin/pages/updates.blade.php) ──
    'installed_version'                => 'الإصدار المثبّت',
    'update_available'                 => 'تحديث متاح',
    'view_changelog'                   => 'عرض سجل التغييرات',
    'on_latest_version'                => 'أنت على أحدث إصدار.',
    'history_empty'                    => 'لم يتم تطبيق أي تحديثات بعد.',
    'col_package'                      => 'الحزمة',
    'col_backup'                       => 'النسخة الاحتياطية',
    'col_result'                       => 'النتيجة',
    'last_checked'                     => 'آخر فحص :time',
    'col_when'                         => 'متى',
    'col_from_to'                      => 'من ← إلى',

    // ─── History badges ───
    'badge_failed'                     => 'فشل',
    'badge_files_written'              => ':count ملف',
];
