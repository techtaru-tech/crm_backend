<?php

declare(strict_types=1);

return [

    // ----- التنقل -----
    'nav_label' => 'المصادقة 2FA',

    // ----- عنوان الصفحة -----
    'page_title' => 'المصادقة الثنائية',

    // ----- عناوين الأقسام و wire:confirm -----
    'recovery_codes_heading'   => 'رموز الاسترداد',
    'confirm_disable_2fa'      => 'هل أنت متأكد من رغبتك في تعطيل 2FA؟ سيصبح حسابك أقل أماناً.',

    // ----- الحالة المُفعَّلة -----
    'section_2fa_active'           => 'المصادقة الثنائية مُفعَّلة',
    'account_protected'            => 'حسابك محمي بـ 2FA',
    'codes_required_each_login'    => 'رموز المصادقة مطلوبة عند كل تسجيل دخول.',
    'save_codes_safe_place'        => 'احفظ هذه الرموز في مكان آمن. يمكن استخدام كل رمز مرة واحدة فقط.',
    'btn_regenerate_codes'         => 'إعادة توليد رموز الاسترداد',
    'btn_disable_2fa'              => 'تعطيل 2FA',

    // ----- حالة الإعداد (رمز QR) -----
    'section_scan_qr'              => 'امسح رمز QR',
    'scan_with_authenticator'      => 'امسح رمز QR هذا بتطبيق المصادقة لديك (Google Authenticator, Authy, 1Password، إلخ.)',
    'manual_code_label'            => 'الرمز اليدوي:',
    'enter_verification_code'      => 'أدخل رمز التحقق من تطبيق المصادقة',
    'btn_verify_and_enable'        => 'تحقق وفعِّل',

    // ----- الحالة الأولية -----
    'section_enable_2fa'           => 'تفعيل المصادقة الثنائية',
    'initial_state_lede'           => 'أضف طبقة أمان إضافية لحسابك بتفعيل المصادقة الثنائية. ستحتاج إلى تطبيق مصادقة مثل Google Authenticator أو Authy.',
    'btn_set_up_2fa'               => 'إعداد المصادقة الثنائية',
];
