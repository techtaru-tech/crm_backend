<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| HelpCenterPage — Filament page strings
|------------------------------------------------------------
| Accessed via __('filament/help_center.<key>').
|
| The tags.* sub-array holds slug → display label pairs used by
| App\Filament\Pages\HelpCenterPage::articles(). Article titles,
| bodies, and category labels remain under
| lang/<locale>/help_center_articles.php (legacy file).
|
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/help_center.php.
*/

return [

    // ----- Article tags (snake_case slug => display label) -----
    'tags' => [
        // Getting started
        'leads'         => 'العملاء المحتملون',
        'first_lead'    => 'أول عميل محتمل',
        'onboarding'    => 'الإعداد',
        'import'        => 'استيراد',
        'csv'           => 'csv',
        'migration'     => 'ترحيل',
        'branding'      => 'العلامة التجارية',
        'white_label'   => 'علامة بيضاء',
        'logo'          => 'الشعار',

        // Forms
        'forms'         => 'النماذج',
        'embed'         => 'تضمين',
        'website'       => 'الموقع',
        'utm'           => 'utm',
        'prefill'       => 'تعبئة مسبقة',

        // Pipelines
        'pipeline'      => 'خط الأنابيب',
        'stages'        => 'المراحل',
        'forecast'      => 'توقعات',
        'teams'         => 'الفِرَق',

        // Automations
        'automation'    => 'أتمتة',
        'workflow'      => 'سير العمل',
        'manual'        => 'يدوي',
        'testing'       => 'اختبار',

        // Email
        'smtp'          => 'smtp',
        'email'         => 'البريد الإلكتروني',
        'configuration' => 'إعداد',
        'deliverability' => 'إمكانية التسليم',
        'spam'          => 'بريد مزعج',

        // Billing
        'billing'       => 'الفوترة',
        'card'          => 'بطاقة',
        'payment'       => 'الدفع',
        'invoices'      => 'الفواتير',
        'receipts'      => 'الإيصالات',
        'cancellation'  => 'الإلغاء',

        // Privacy
        'gdpr'          => 'gdpr',
        'export'        => 'تصدير',
        'data'          => 'البيانات',
        'deletion'      => 'الحذف',

        // Team
        'team'          => 'الفريق',
        'invite'        => 'دعوة',
        'members'       => 'الأعضاء',
        'roles'         => 'الأدوار',
        'permissions'   => 'الأذونات',
    ],
];
