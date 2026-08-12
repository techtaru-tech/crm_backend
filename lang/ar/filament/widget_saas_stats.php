<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — SaasStatsOverview (Super Admin) translation strings
|--------------------------------------------------------------------------
|
| Stat labels, descriptions and trend copy for the Super Admin dashboard
| overview widget.
| Consumed via __('filament/widget_saas_stats.<key>').
|
*/

return [

    'total_tenants'              => 'إجمالي مساحات العمل',
    'total_tenants_description'  => 'جميع المستأجرين المسجَّلين',
    'active_subs'                => 'الاشتراكات النشطة',
    'active_subs_description'    => 'مدفوعة وسارية',
    'mrr'                        => 'MRR',
    'mrr_description'            => 'الإيرادات الشهرية المتكررة',
    'signups_month'              => 'التسجيلات هذا الشهر',

    // ─── Trend description fragments ─────────────────────────────────
    'vs_last_month'              => 'الشهر الماضي',
    'no_data_for_comparison'     => 'لا توجد بيانات للمقارنة',
    'new_no_prior_data'          => 'جديد — لا توجد بيانات سابقة لـ :label',
    'trend_vs'                   => ':sign:pct% مقابل :label',
];
