<?php

declare(strict_types=1);

return [
    'stripe' => [
        'not_configured'  => 'Stripe غير مُهيّأ.',
        'checkout_failed' => 'فشل الدفع عبر Stripe.',
        'start_failed'    => 'تعذّر بدء عملية الدفع عبر Stripe: :error',
        'product_coupon_suffix' => ' — كوبون :code',
    ],
    'razorpay' => [
        'not_configured'        => 'Razorpay غير مُهيّأ.',
        'subscription_failed'   => 'فشل اشتراك Razorpay.',
        'order_creation_failed' => 'فشل إنشاء طلب Razorpay.',
        'error'                 => 'خطأ في Razorpay: :error',
        'annual_not_supported'  => 'الفوترة السنوية على Razorpay غير مدعومة بعد. يرجى اختيار الفوترة الشهرية أو التواصل مع الدعم لتفعيل خطة سنوية.',
    ],
    'paystack' => [
        'not_configured'  => 'Paystack غير مُهيّأ.',
        'checkout_failed' => 'فشل الدفع عبر Paystack.',
        'error'           => 'خطأ في Paystack: :error',
        'annual_not_supported' => 'الفوترة السنوية على Paystack غير مدعومة بعد. يرجى اختيار الفوترة الشهرية أو التواصل مع الدعم لتفعيل خطة سنوية.',
    ],
    'paypal' => [
        'not_configured'   => 'PayPal غير مُهيّأ.',
        'no_approval_link' => 'لم يُرجع PayPal رابط الموافقة.',
        'checkout_failed'  => 'فشل الدفع عبر PayPal.',
        'error'            => 'خطأ في PayPal: :error',
        'auth_failed'      => 'فشل مصادقة PayPal: :body',
        'annual_plan_id_missing' => 'الفوترة السنوية على PayPal غير مُهيّأة لهذه الخطة. اطلب من مالك مساحة العمل إنشاء خطة سنوية في PayPal وربط معرّفها عبر meta.paypal_plan_id_yearly، أو اختر الفوترة الشهرية.',
    ],
    'manual' => [
        'not_configured'     => 'التحويل البنكي اليدوي غير مُهيّأ.',
        'instructions_intro' => 'يرجى تحويل المبلغ أدناه مع تضمين المرجع. ستُفعَّل خطتك بعد أن يؤكد فريقنا استلام الدفعة.',
        'plan_suffix'        => 'خطة :plan',
        'labels'             => [
            'bank'           => 'البنك',
            'account_name'   => 'اسم الحساب',
            'account_number' => 'رقم الحساب',
            'iban'           => 'IBAN',
            'swift_bic'      => 'SWIFT / BIC',
            'amount'         => 'المبلغ',
            'reference'      => 'المرجع',
        ],
    ],
];
