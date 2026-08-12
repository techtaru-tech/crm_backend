<?php

declare(strict_types=1);

return [
    'stripe' => [
        'not_configured'  => 'Stripe no está configurado.',
        'checkout_failed' => 'El pago con Stripe falló.',
        'start_failed'    => 'No se pudo iniciar el pago con Stripe: :error',
        'product_coupon_suffix' => ' — cupón :code',
    ],
    'razorpay' => [
        'not_configured'        => 'Razorpay no está configurado.',
        'subscription_failed'   => 'La suscripción de Razorpay falló.',
        'order_creation_failed' => 'La creación del pedido en Razorpay falló.',
        'error'                 => 'Error de Razorpay: :error',
        'annual_not_supported'  => 'La facturación anual en Razorpay aún no es compatible. Elija facturación mensual o póngase en contacto con soporte para habilitar un plan anual.',
    ],
    'paystack' => [
        'not_configured'  => 'Paystack no está configurado.',
        'checkout_failed' => 'El pago con Paystack falló.',
        'error'           => 'Error de Paystack: :error',
        'annual_not_supported' => 'La facturación anual en Paystack aún no es compatible. Elija facturación mensual o póngase en contacto con soporte para habilitar un plan anual.',
    ],
    'paypal' => [
        'not_configured'   => 'PayPal no está configurado.',
        'no_approval_link' => 'PayPal no devolvió un enlace de aprobación.',
        'checkout_failed'  => 'El pago con PayPal falló.',
        'error'            => 'Error de PayPal: :error',
        'auth_failed'      => 'La autenticación de PayPal falló: :body',
        'annual_plan_id_missing' => 'La facturación anual en PayPal no está configurada para este plan. Pida al propietario del espacio de trabajo que cree un plan anual en PayPal y asigne su id mediante meta.paypal_plan_id_yearly, o elija facturación mensual.',
    ],
    'manual' => [
        'not_configured'     => 'La transferencia bancaria manual no está configurada.',
        'instructions_intro' => 'Transfiera el importe indicado a continuación e incluya la referencia. Su plan se activará cuando nuestro equipo confirme el pago.',
        'plan_suffix'        => 'plan :plan',
        'labels'             => [
            'bank'           => 'Banco',
            'account_name'   => 'Nombre de la cuenta',
            'account_number' => 'Número de cuenta',
            'iban'           => 'IBAN',
            'swift_bic'      => 'SWIFT / BIC',
            'amount'         => 'Importe',
            'reference'      => 'Referencia',
        ],
    ],
];
