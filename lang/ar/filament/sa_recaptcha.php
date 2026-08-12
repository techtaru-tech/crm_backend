<?php

declare(strict_types=1);

return [

    // ----- Page title / navigation -----
    'title'                       => 'حماية reCAPTCHA',
    'navigation_label'            => 'reCAPTCHA',

    // ----- Google reCAPTCHA v3 section -----
    'console_intro_prefix'        => 'احصل على مفاتيحك من',
    'console_link_label'          => 'وحدة تحكم إدارة Google reCAPTCHA',
    'console_intro_suffix'        => 'اختر الإصدار v3 عند إنشاء الموقع. أضف :host إلى قائمة النطاقات.',
    'master_switch_label'         => 'المفتاح الرئيسي',
    'master_switch_helper'        => 'فعّل reCAPTCHA أو عطّله دون فقد مفاتيحك. عند إيقافه، تُتجاهل كل وسائل الحماية أدناه.',
    'site_key_label'              => 'مفتاح الموقع',
    'site_key_helper'             => 'مفتاح عام. يُرسل إلى المتصفح.',
    'secret_key_label'            => 'المفتاح السري',
    'secret_key_helper'           => 'مفتاح خاص بالخادم فقط. يُستخدم للتحقق من الرموز المُرسَلة لدى Google.',
    'min_score_label'             => 'الحد الأدنى للنقاط',
    'min_score_helper'            => 'نقاط بين 0.0 (روبوت مؤكد) و1.0 (إنسان مؤكد). تُرفض الإرسالات التي تقل عن هذا الحد. توصي Google بـ 0.5.',

    // ----- Protected surfaces section -----
    'protected_surfaces_description' => 'مفاتيح لكل صفحة. عطّلها بشكل فردي إذا رأيت رفضًا إيجابيًا زائفًا؛ يعلو المفتاح الرئيسي أعلاه على هذه.',
    'guard_register_label'        => 'النموذج العام /register',
    'guard_register_helper'       => 'يحمي نموذج التسجيل الذاتي لمساحة العمل.',
    'guard_admin_login_label'     => 'تسجيل دخول المستأجر /admin',
    'guard_admin_login_helper'    => 'يحمي صفحة تسجيل الدخول الخاصة بالمستأجر.',
    'guard_sa_login_label'        => 'تسجيل دخول المشرف الأعلى',
    'guard_sa_login_helper'       => 'يحمي صفحة تسجيل دخول المشرف الأعلى.',

    // ----- Notifications & actions -----
    'settings_saved'              => 'تم حفظ إعدادات reCAPTCHA',
    'action_save'                 => 'حفظ',

];
