<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Billing public-facing pages (resources/views/billing/*.blade.php)
|------------------------------------------------------------
| Accessed via __('billing.<key>').
*/

return [

    // Manual transfer instructions
    'transfer_instructions'    => 'تعليمات التحويل',

    // ─── Razorpay launch page (resources/views/billing/razorpay-launch.blade.php) ──
    'razorpay_page_title'      => 'جارٍ تشغيل Razorpay…',
    'razorpay_launching_msg'   => 'جارٍ فتح صفحة الدفع في Razorpay…',
    'razorpay_app_description' => 'دفع الاشتراك',

    // ─── Manual-instructions page (resources/views/billing/manual-instructions.blade.php) ──
    'manual_page_title'        => 'تعليمات الدفع',
    'manual_amount_due_label'  => 'المبلغ المستحق',
    'manual_back_to_workspace' => 'العودة إلى مساحة العمل',

    // ─── Gateway labels (App\Billing\Gateways\*::label()) ──
    // Brand-only labels (PayPal, Paystack, Razorpay) are left literal
    // in their gateway classes; only Stripe + Manual contain
    // English words that need localisation.
    'gateway_stripe_label'     => 'Stripe (بطاقة)',
    'gateway_manual_label'     => 'تحويل بنكي (يدوي)',

    // ─── TenantSubscriptionState::label() ──
    // Human-readable badges for the workspace's overall subscription
    // state (combines Tenant.active + subscription_status + trial /
    // sub end-date columns into one derived label).
    'state_suspended'          => 'موقوف',
    'state_trial'              => 'فترة تجريبية',
    'state_trial_expired'      => 'انتهت الفترة التجريبية',
    'state_active'             => 'نشط',
    'state_pending_payment'    => 'بانتظار الدفع',
    'state_cancelled'          => 'ملغى',
    'state_expired'            => 'منتهٍ',
    'state_pending_deletion'   => 'مجدول للحذف',
    'state_unknown'            => 'غير معروف',
];
