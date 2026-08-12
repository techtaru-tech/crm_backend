<?php

declare(strict_types=1);

return [
    'nav_label'   => 'الإشعارات',
    'notif_saved' => 'تم حفظ تفضيلات الإشعارات.',

    // ----- Section headings -----
    'section_preferences'                => 'تفضيلات الإشعارات',
    'section_browser_push'               => 'إشعارات المتصفح الفورية',

    // ----- Page lede / helper text -----
    'lede'                               => 'اختر كيفية استلام كل نوع من الإشعارات. يسري تردد البريد الإلكتروني على قناة البريد فقط.',

    // ----- Table headers -----
    'th_notification_type'               => 'نوع الإشعار',
    'th_in_app'                          => 'داخل التطبيق',
    'th_email'                           => 'البريد الإلكتروني',
    'th_email_frequency'                 => 'تردد البريد',
    'th_browser_push'                    => 'إشعار المتصفح',

    // ----- Email frequency options -----
    'freq_immediate'                     => 'فوري',
    'freq_hourly'                        => 'موجز كل ساعة',
    'freq_off'                           => 'إيقاف',

    // ----- Save button -----
    'save_preferences'                   => 'حفظ التفضيلات',

    // ----- Push subscription (shared) -----
    'push_lede'                          => 'اسمح لـ :app بإرسال إشعارات فورية حتى عندما يكون التبويب في الخلفية.',
    'push_lede_legacy'                   => 'اسمح لـ :app بإرسال إشعارات المتصفح حتى عندما يكون التبويب في الخلفية.',
    'push_unsupported'                   => 'إشعارات المتصفح الفورية غير مدعومة في هذا المتصفح.',
    'push_subscribing'                   => 'جارٍ الاشتراك...',
    'push_enable_btn'                    => 'تمكين الإشعارات الفورية',
    'push_enabled'                       => 'تم تمكين الإشعارات الفورية',

    // ----- OneSignal status messages -----
    'msg_onesignal_not_loaded'           => 'لم يتم تحميل OneSignal بعد. حاول مرة أخرى.',
    'msg_push_enabled'                   => 'تم تمكين الإشعارات الفورية.',
    'msg_permission_denied'              => 'رفض المتصفح الإذن.',
    'msg_error_prefix'                   => 'خطأ: ',
    'msg_subscribe_failed'               => 'فشل الاشتراك.',
];
