<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| RealtimeSettingsPage — سلاسل Filament للمستأجر (ar)
|------------------------------------------------------------
| الوصول عبر __('filament/realtime_settings.<key>').
*/

return [
    'title'                          => 'الوقت الفعلي والبثّ',
    'navigation_label'               => 'الوقت الفعلي',

    // قسم محرّك التشغيل
    'driver_section_description'     => 'اختر مزوّد البث الفوري لتحديثات العملاء المحتملين والإشعارات.',
    'enable_realtime_label'          => 'تفعيل التحديثات الفورية',
    'enable_realtime_helper'         => 'عند التعطيل، تستخدم اللوحة الاستطلاع بدلًا من WebSockets.',
    'driver_label'                   => 'محرّك التشغيل',
    'driver_helper'                  => 'Reverb و Soketi خادمان مستضافان ذاتيًا متوافقان مع بروتوكول Pusher.',
    'driver_option_pusher'           => 'Pusher / Soketi / Reverb (بروتوكول Pusher)',
    'driver_option_null'             => 'مُعطّل (استطلاع فقط)',

    // قسم Pusher
    'pusher_section_description'     => 'قدِّم بيانات الاعتماد لخادم Pusher أو Reverb أو Soketi.',
    'pusher_app_id_label'            => 'App ID',
    'pusher_key_label'               => 'App Key',
    'pusher_secret_label'            => 'App Secret',
    'pusher_cluster_label'           => 'العنقود',
    'pusher_cluster_helper'          => 'اتركه فارغًا لـ Reverb/Soketi المستضافين ذاتيًا.',
    'pusher_host_label'              => 'مضيف مخصّص (Reverb/Soketi)',
    'pusher_host_helper'             => 'تجاوز للخوادم المستضافة ذاتيًا. اتركه فارغًا لاستخدام Pusher المُدار.',
    'pusher_port_label'              => 'المنفذ',
    'pusher_scheme_label'            => 'المخطّط',
    'pusher_scheme_https'            => 'HTTPS (wss)',
    'pusher_scheme_http'             => 'HTTP (ws)',

    // قسم الحالة
    'status_content'                 => 'احفظ الإعدادات لتطبيقها. تُخزَّن بيانات اعتماد البث لكل مستأجر وتُطبَّق وقت التشغيل. أعد تشغيل عامل قائمة الانتظار بعد تغيير محرّك التشغيل.',

    // إجراءات الرأس
    'action_save'                    => 'حفظ الإعدادات',

    // ----- بطاقة محرّك التشغيل (Blade) -----
    'section_broadcasting_driver'       => 'محرّك البثّ',
    'active_driver'                     => 'محرّك التشغيل النشط',
    'connection_status'                 => 'حالة الاتصال',
    'status_connected'                  => 'متّصل',
    'status_error'                      => 'خطأ',
    'status_not_configured'             => 'غير مهيّأ',
    'status_not_tested'                 => 'لم يُختبر',
    'btn_test_connection'               => 'اختبار الاتصال',
    'btn_send_test_notification'        => 'إرسال إشعار اختبار',
    'test_sent_message'                 => 'تم إرسال إشعار الاختبار! تحقّق من جرس الإشعارات لديك.',

    // ----- قسم التهيئة (Blade) -----
    'section_configuration'             => 'التهيئة',
    'config_description'                => 'عيّن متغيّرات البيئة التالية لتمكين البثّ الفوري:',
    'option_a_pusher'                   => 'الخيار أ: Pusher (مُدار)',
    'option_b_reverb'                   => 'الخيار ب: Laravel Reverb (مستضاف ذاتيًا)',
];
