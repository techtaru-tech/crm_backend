<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SecuritySettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/security_settings.<key>').
*/

return [
    'title'                              => 'إعدادات الأمان',
    'navigation_label'                   => 'الأمان',

    // Auth section
    'auth_section_description'           => 'تنطبق هذه الإعدادات على كل مستخدم في فريقك وتسري فورًا.',
    'enforce_2fa_label'                  => 'فرض المصادقة الثنائية',
    'enforce_2fa_helper_prefix'          => 'عند التفعيل، يُعاد توجيه كل مستخدم في فريقك إلى إعداد رمز QR عند طلبه التالي ولا يمكنه استخدام اللوحة حتى يكمل التسجيل.',
    'enforce_2fa_helper_link'            => 'فعّل المصادقة الثنائية الخاصة بك الآن ←',
    'session_lifetime_label'             => 'مدة الجلسة (دقائق)',
    'minutes_suffix'                     => 'د',

    // Rate limit section
    'max_login_attempts_label'           => 'الحد الأقصى لمحاولات تسجيل الدخول الفاشلة',
    'lockout_duration_label'             => 'مدة القفل (دقائق)',

    // IP whitelist section
    'ip_whitelist_section_description'   => 'اسمح بالوصول إلى لوحة الإدارة من عناوين IP هذه فقط. اترك الحقل فارغًا للسماح للجميع.',
    'ip_whitelist_label'                 => 'عناوين IP المسموح بها',
    'ip_whitelist_placeholder'           => 'مثل: 192.168.1.1',
    'ip_whitelist_helper'                => 'أدخل عناوين IP (IPv4 أو IPv6) أو نطاقات CIDR واضغط Enter لإضافة كل منها.',

    // Actions
    'action_save'                        => 'حفظ الإعدادات',
];
