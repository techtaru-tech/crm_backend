<?php

/**
 * Display strings for the integration sync-logs Blade view.
 */

return [
    'status' => [
        'success'  => 'نجاح',
        'failed'   => 'فشل',
        'pending'  => 'قيد الانتظار',
        'retrying' => 'إعادة المحاولة',
    ],

    'event' => [
        'lead_created' => 'تم إنشاء عميل محتمل',
        'lead_updated' => 'تم تحديث عميل محتمل',
    ],

    'error_default'     => '(لا توجد تفاصيل خطأ)',
    'http_error_prefix' => 'HTTP :status: :body',
];
