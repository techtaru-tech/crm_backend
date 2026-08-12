<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| QueueSettingsPage — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/queue_settings.<key>').
*/

return [
    'title'                => 'حالة الطابور والعمّال',
    'navigation_label'     => 'الطابور والعمّال',

    // Section headings
    'queue_configuration'  => 'إعدادات الطابور',

    // ─── Blade view — queue config grid ────────────────────────────────
    'connection'           => 'الاتصال',
    'driver'               => 'المشغّل',

    // ─── Blade view — Horizon ─────────────────────────────────────────
    'horizon_lede'         => 'Laravel Horizon مثبّت. راقب الطوابير في الوقت الفعلي:',
    'horizon_open_dashboard' => 'فتح لوحة Horizon',

    // ─── Blade view — operator notice banner ──────────────────────────
    'operator_notice_title' => 'تُدار المهام الخلفية بواسطة مُشغِّل الخدمة',
    'operator_notice_body' => 'تُعالَج الأتمتة وتسليم البريد والتقارير المجدولة والتذكيرات بواسطة عامل خلفي مُهيَّأ على مستوى الخادم. إذا بدت المهام متأخرة أو عالقة، تواصل مع مزود الخدمة الخاص بك — فهم يديرون جدول cron والبنية التحتية للعمّال نيابة عنك.',
];
