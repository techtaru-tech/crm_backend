<?php

declare(strict_types=1);

return [
    'install_suffix'              => '(من السوق)',
    'template_not_published'      => 'لم يتم نشر القالب',
    'plan_not_included'           => 'لا تتضمّن خطتك الحالية تثبيتات السوق. قم بالترقية لفتح مكتبة قوالب DFY.',
    'monthly_limit_reached'       => 'لقد بلغت حد تثبيتات السوق في خطتك (:used/:limit) لهذا الشهر. قم بالترقية لتثبيت المزيد.',
    'unsupported_template_type'   => 'نوع قالب غير مدعوم: :type',
    'install_failed'              => 'فشل التثبيت: :error',

    // Defensive fallbacks for malformed marketplace template payloads.
    // Fire only when an uploaded template omits the corresponding field
    // (the buyer can always rename in the UI after install).
    'form_default_success_message' => 'شكرًا!',
    'pipeline_stage_default_name'  => 'المرحلة :index',

    // Fallback when the installer tenant has no name set — surfaced in
    // the contributor's "your template was installed" notification body
    // via the `installer` placeholder.
    'installer_tenant_name_fallback' => 'مساحة عمل',
];
