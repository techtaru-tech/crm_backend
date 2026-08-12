<?php

declare(strict_types=1);

return [
    'calendar_oauth' => [
        'consent_rejected'       => 'رفض المزود الموافقة: :error',
        'no_authorization_code'  => 'لم يتم استلام رمز التفويض من المزود.',
        'token_exchange_failed'  => 'تعذر تبادل رمز التفويض. تحقق من بيانات اعتماد عميل :provider في إعدادات الخدمات.',
        'connection_save_failed' => 'تعذر حفظ الاتصال. حاول مرة أخرى.',
        'connected_success'      => 'تم ربط تقويم :provider باسم :email.',
    ],

    'recaptcha' => [
        'verification_failed'       => 'فشل التحقق عبر reCAPTCHA. يُرجى تحديث الصفحة والمحاولة مرة أخرى.',
        'verification_failed_short' => 'فشل التحقق عبر reCAPTCHA. يُرجى التحديث والمحاولة مرة أخرى.',
    ],

    'invoice_payment' => [
        'gateway_not_supported' => 'لا تدعم البوابة المحددة دفع الفواتير بعد. يُرجى اختيار بوابة أخرى.',
        'start_failed'          => 'تعذر بدء الدفع. يُرجى تجربة طريقة أخرى.',
    ],

    'password_setup' => [
        'invalid_or_expired' => 'رابط الإعداد هذا غير صالح أو منتهي الصلاحية. استخدم رابط «نسيت كلمة المرور» في صفحة تسجيل الدخول لطلب رابط جديد.',
    ],

    'coupon' => [
        'prefix'       => 'قسيمة: ',
        'invalid_code' => 'رمز غير صالح.',
    ],

    'oauth' => [
        'no_token_url'             => 'لا يوجد رابط رمز لـ :type',
        'token_exchange_failed'    => 'فشل تبادل الرمز (:status): :body',
        'salesforce_invalid_url'   => 'يجب أن يكون instance_url الخاص بـ Salesforce على *.salesforce.com أو *.force.com (تم استلام :host).',
        'salesforce_safety_failed' => 'فشل فحص أمان instance_url الخاص بـ Salesforce: :error',
        'salesforce_token_failed'  => 'فشل تبادل رمز Salesforce (:status): :body',
        'meta_token_failed'        => 'فشل تبادل رمز Meta: :body',
        'meta_no_access_token'     => 'لم يتم إرجاع access_token من Meta',
    ],
];
