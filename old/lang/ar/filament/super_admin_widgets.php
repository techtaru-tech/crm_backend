<?php

declare(strict_types=1);

return [

    // ─── TenantSignupsChart ─────────────────────────────────────────────
    'tenant_signups' => [
        'heading'        => 'تسجيلات المستأجرين الجدد (آخر ١٢ شهراً)',
        'dataset_label'  => 'التسجيلات',
    ],

    // ─── MrrTrendChart ──────────────────────────────────────────────────
    'mrr_trend' => [
        'heading'        => 'الإيرادات المحصلة (آخر ١٢ شهراً)',
        'dataset_label'  => 'الإيرادات',
    ],

    // ─── LeadSourceMixChart ─────────────────────────────────────────────
    'lead_source_mix' => [
        'heading'        => 'أفضل ٥ مصادر للعملاء المحتملين (جميع المستأجرين)',
        'dataset_label'  => 'العملاء المحتملون',
    ],

    // ─── SubscriptionStatusChart ────────────────────────────────────────
    'subscription_status' => [
        'heading'                => 'توزيع حالة الاشتراكات',
        'status_active'          => 'نشط',
        'status_trial'           => 'تجريبي',
        'status_trial_expired'   => 'انتهت التجربة',
        'status_cancelled'       => 'ملغى',
        'status_expired'         => 'منتهي الصلاحية',
        'status_suspended'       => 'معلق',
        'status_past_due'        => 'متأخر السداد',
    ],

    // ─── SuperAdminSetupChecklist ───────────────────────────────────────
    'setup_checklist' => [
        'item_smtp'       => 'قم بإعداد SMTP حتى تُرسَل الرسائل البريدية فعلياً',
        'item_gateway'    => 'قم بتفعيل بوابة دفع واحدة على الأقل',
        'item_brand'      => 'حدِّد اسم علامتك التجارية وألوانها',
        'item_landing'    => 'حرِّر نص البطل في صفحة الهبوط',
        'item_workspace'  => 'قم بتأهيل أول مساحة عمل لديك',
    ],

];
