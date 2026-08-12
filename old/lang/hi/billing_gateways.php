<?php

declare(strict_types=1);

return [
    'stripe' => [
        'not_configured'  => 'Stripe कॉन्फ़िगर नहीं है।',
        'checkout_failed' => 'Stripe चेकआउट विफल रहा।',
        'start_failed'    => 'Stripe चेकआउट प्रारंभ नहीं किया जा सका: :error',
        'product_coupon_suffix' => ' — कूपन :code',
    ],
    'razorpay' => [
        'not_configured'        => 'Razorpay कॉन्फ़िगर नहीं है।',
        'subscription_failed'   => 'Razorpay सब्सक्रिप्शन विफल रहा।',
        'order_creation_failed' => 'Razorpay ऑर्डर निर्माण विफल रहा।',
        'error'                 => 'Razorpay त्रुटि: :error',
        'annual_not_supported'  => 'Razorpay पर वार्षिक बिलिंग अभी समर्थित नहीं है। कृपया मासिक बिलिंग चुनें या वार्षिक प्लान सक्षम करने के लिए सहायता से संपर्क करें।',
    ],
    'paystack' => [
        'not_configured'  => 'Paystack कॉन्फ़िगर नहीं है।',
        'checkout_failed' => 'Paystack चेकआउट विफल रहा।',
        'error'           => 'Paystack त्रुटि: :error',
        'annual_not_supported' => 'Paystack पर वार्षिक बिलिंग अभी समर्थित नहीं है। कृपया मासिक बिलिंग चुनें या वार्षिक प्लान सक्षम करने के लिए सहायता से संपर्क करें।',
    ],
    'paypal' => [
        'not_configured'   => 'PayPal कॉन्फ़िगर नहीं है।',
        'no_approval_link' => 'PayPal ने अप्रूवल लिंक नहीं लौटाया।',
        'checkout_failed'  => 'PayPal चेकआउट विफल रहा।',
        'error'            => 'PayPal त्रुटि: :error',
        'auth_failed'      => 'PayPal प्रमाणीकरण विफल रहा: :body',
        'annual_plan_id_missing' => 'इस प्लान के लिए PayPal पर वार्षिक बिलिंग कॉन्फ़िगर नहीं है। वर्कस्पेस मालिक से कहें कि वे PayPal में एक वार्षिक प्लान बनाएँ और meta.paypal_plan_id_yearly के माध्यम से उसकी id मैप करें, या मासिक बिलिंग चुनें।',
    ],
    'manual' => [
        'not_configured'     => 'मैनुअल बैंक ट्रांसफर कॉन्फ़िगर नहीं है।',
        'instructions_intro' => 'कृपया नीचे दी गई राशि स्थानांतरित करें और संदर्भ शामिल करें। हमारी टीम द्वारा भुगतान की पुष्टि होने पर आपका प्लान सक्रिय हो जाएगा।',
        'plan_suffix'        => ':plan प्लान',
        'labels'             => [
            'bank'           => 'बैंक',
            'account_name'   => 'खाता नाम',
            'account_number' => 'खाता संख्या',
            'iban'           => 'IBAN',
            'swift_bic'      => 'SWIFT / BIC',
            'amount'         => 'राशि',
            'reference'      => 'संदर्भ',
        ],
    ],
];
