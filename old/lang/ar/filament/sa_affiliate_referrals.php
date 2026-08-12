<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin AffiliateReferralResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_affiliate_referrals.<key>').
*/

return [
    'nav_label'            => 'عمولات الإحالة',
    'model_label'          => 'عمولة إحالة',
    'plural_model_label'   => 'عمولات الإحالة',

    // Table columns
    'col_affiliate'        => 'المُحيل',
    'col_referred'         => 'مساحة العمل المُحالة',
    'col_plan'             => 'الخطة',
    'col_commission'       => 'العمولة',
    'col_rate'             => 'النسبة',
    'col_status'           => 'الحالة',
    'col_booked'           => 'تاريخ التسجيل',
    'col_paid_at'          => 'تاريخ الدفع',

    // Filter
    'filter_status'        => 'الحالة',

    // Row actions
    'action_approve'       => 'اعتماد',
    'action_mark_paid'     => 'تحديد كمدفوعة',
    'action_reverse'       => 'إلغاء',

    // Row-action notifications
    'notify_approved'      => 'تم اعتماد العمولة.',
    'notify_paid'          => 'تم تحديد العمولة كمدفوعة.',
    'notify_reversed'      => 'تم إلغاء العمولة.',

    // Bulk actions
    'bulk_approve'         => 'اعتماد المحدّد',
    'bulk_mark_paid'       => 'تحديد المحدّد كمدفوع',
    'notify_bulk_approved' => 'تم اعتماد :count عمولة.',
    'notify_bulk_paid'     => 'تم تحديد :count عمولة كمدفوعة.',

    // Empty state
    'empty_heading'        => 'لا توجد عمولات إحالة بعد',
    'empty_description'    => 'تُسجَّل العمولات تلقائيًا عندما تقوم مساحة عمل مُحالة بإجراء دفعة.',
];
