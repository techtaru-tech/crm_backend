<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Automation Templates — display labels
|--------------------------------------------------------------------------
| Mirrors the `name` and `description` fields of config/automation_templates.php.
| Consumed by AutomationTemplateService::templates() and the
| IndustryPackService::installAutomations() lookup, both of which apply
| translator-first-with-fallback to the original config value.
|
| Keys MUST match the `key` field of each entry in
| config/automation_templates.php so unknown / buyer-added templates fall
| through to the literal config value.
*/

return [

    'welcome_new_lead' => [
        'name'        => 'الترحيب بعميل محتمل جديد',
        'description' => 'إرسال بريد ترحيبي عند إنشاء أي عميل محتمل جديد',
    ],

    'hot_lead_alert' => [
        'name'        => 'تنبيه عميل محتمل ساخن',
        'description' => 'تنبيه الفريق عند تجاوز نقاط العميل المحتمل 80',
    ],

    'assign_by_source' => [
        'name'        => 'التوزيع بالتناوب',
        'description' => 'توزيع العملاء المحتملين الجدد بالتساوي على مندوبي المبيعات',
    ],

    'followup_no_activity' => [
        'name'        => 'تذكير متابعة',
        'description' => 'إنشاء مهمة متابعة عند عدم وجود نشاط للعميل المحتمل لمدة 3 أيام',
    ],

    'stage_change_notification' => [
        'name'        => 'إشعار تغيير المرحلة',
        'description' => 'تنبيه المستخدم المعيّن عند انتقال العميل المحتمل إلى مرحلة العرض',
    ],

    'reengage_cold' => [
        'name'        => 'إعادة إشراك العملاء المحتملين الباردين',
        'description' => 'إرسال بريد إلى العملاء المحتملين غير النشطين لمدة 30 يومًا برسالة إعادة إشراك',
    ],

    'vip_tagger' => [
        'name'        => 'وسم تلقائي لعملاء VIP',
        'description' => 'إضافة وسم VIP تلقائيًا عندما تتجاوز قيمة الصفقة 10 آلاف دولار',
    ],

    'slack_new_lead' => [
        'name'        => 'تنبيه Slack عند عميل محتمل جديد',
        'description' => 'النشر إلى Slack عند استلام عميل محتمل جديد',
    ],

    'sla_escalation_hot_lead' => [
        'name'        => 'SLA: تصعيد سريع للعملاء المحتملين الساخنين',
        'description' => 'إذا كان عميل محتمل بنقاط > 80 ولم يتم التواصل معه خلال ساعتين، يتم تنبيه المدير',
    ],

    'lead_recycling' => [
        'name'        => 'إعادة تدوير العملاء المحتملين',
        'description' => 'إعادة تعيين العملاء المحتملين غير المعالَجين إلى مندوب آخر بعد 30 يومًا',
    ],
];
