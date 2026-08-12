<?php

declare(strict_types=1);

return [
    'title'                          => 'الوحدات',
    'navigation_label'               => 'الوحدات',

    // Install section
    'install_section_description'    => 'ارفع ملف zip يحتوي على البيان module.json. تُنسخ مجلد الوحدة إلى Modules/، ويُعاد توليد محمّل nwidart التلقائي، وتبدأ الوحدة في حالة معطّلة حتى تقوم بتمكينها.',
    'module_zip_label'               => 'ملف zip للوحدة',
    'module_zip_helper'              => 'أي ملف zip يحتوي مستواه الأعلى على ملف module.json (مباشرة أو داخل مجلد تغليف واحد).',

    // Notifications
    'module_installed_title'         => 'تم تثبيت الوحدة بنجاح.',
    'module_installed_body'          => 'تم تثبيت :name. مكّنها من القائمة أدناه.',

    // Header actions
    'action_regenerate'              => 'إعادة توليد التحميل التلقائي',
    'action_install'                 => 'تثبيت الوحدة المرفوعة',
    'action_install_confirmation'    => 'سيُنسخ مجلد الوحدة إلى Modules/. إذا كانت هناك وحدة بالاسم نفسه موجودة، فسيُستبدل. هل تريد المتابعة؟',

    // Hero / page content
    'hero_eyebrow'                   => 'امتدادات HMVC',
    'hero_title'                     => 'الوحدات',
    'unavailable_warning_strong'     => 'نظام الوحدات غير متاح.',
    'installed_section_title'        => 'الوحدات المثبتة',
    'empty_no_modules'               => 'لا توجد وحدات مثبتة. ارفع ملف zip أعلاه للبدء.',

    // Table column headers
    'col_module'                     => 'الوحدة',
    'col_version'                    => 'الإصدار',
    'col_status'                     => 'الحالة',
    'col_actions'                    => 'الإجراءات',

    // Action buttons + confirmations
    'btn_disable'                    => 'تعطيل',
    'btn_enable'                     => 'تمكين',
    'btn_delete'                     => 'حذف',
    'confirm_permanently_delete'     => 'هل تريد حذف الوحدة :name نهائيًا؟ لا يمكن التراجع عن هذا الإجراء.',

    // ─── Hero body + warning + status pills ───
    'hero_subtitle_html'             => 'وسّع LeadHub خلال 30 ثانية. ارفع ملف zip للوحدة، انقر على تمكين، ستصبح الميزة الجديدة فعّالة. بدون composer، بدون توقف، بدون الحاجة إلى مطوّر. تعيش كل وحدة في مجلد مستقل تحت <code class="mod-hero-code">Modules/&lt;Name&gt;</code> مع مساراتها وعمليات الترحيل وطرق العرض والترجمات وموارد الإدارة الخاصة بها.',
    'unavailable_warning_body_html'  => 'لم يتم تحميل بيئة تشغيل الوحدات. عادةً ما يعني هذا أن الرفع كان غير مكتمل &mdash; يرجى إعادة رفع ملف توزيع LeadHub الكامل والتأكد من تضمين مجلد <code class="mod-warn-code">vendor/</code>. إذا استمرت المشكلة، تواصل مع الدعم. تعتمد القائمة أدناه على فحص نظام الملفات بحيث تظل أي وحدات منسوخة يدويًا مرئية.',
    'pill_enabled'                   => 'ممكّن',
    'pill_disabled'                  => 'معطّل',
];
