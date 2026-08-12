<?php

declare(strict_types=1);

return [
    'updates_blocked' => 'العرض: لا يمكن تطبيق حزم التحديث.',

    // Distinct Backups page guards (one key per call site).
    'backups_create_method_blocked'  => 'العرض: لا يمكن إنشاء النسخ الاحتياطية (سيُفرّغ قاعدة بيانات العرض المباشر وملفاته).',
    'backups_delete_method_blocked'  => 'العرض: لا يمكن حذف أرشيفات النسخ الاحتياطية.',
    'backups_restore_method_blocked' => 'العرض: لا يمكن استعادة النسخ الاحتياطية (ستستبدل قاعدة بيانات العرض المباشر).',
    'backups_create_action_blocked'  => 'العرض: لا يمكن إنشاء النسخ الاحتياطية.',
    'backups_restore_action_blocked' => 'العرض: لا يمكن استعادة النسخ الاحتياطية.',
    'backups_delete_action_blocked'  => 'العرض: لا يمكن حذف النسخ الاحتياطية.',

    'gdpr_anonymize_blocked' => 'العرض: تجهيل GDPR معطَّل.',
    'gdpr_erase_blocked'     => 'العرض: محو GDPR معطَّل.',

    'checkout_blocked'       => 'العرض: لا يمكن بدء عملية دفع فعلية.',
    'impersonation_disabled' => 'العرض: انتحال الهوية معطَّل.',
    'test_email_blocked'     => 'العرض: لا يمكن إرسال رسائل اختبارية إلى مستلمين عشوائيين.',
];
