<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SubscriptionStatus enum — translatable case labels
|------------------------------------------------------------
| Accessed via __('enums/subscription_status.<case_value>').
*/

return [
    'trial'             => 'فترة تجريبية',
    'trial_expired'     => 'انتهت الفترة التجريبية',
    'active'            => 'نشط',
    'pending_payment'   => 'بانتظار الدفع',
    'cancelled'         => 'ملغى',
    'expired'           => 'منتهٍ',
    'pending_deletion'  => 'مجدول للحذف',
];
