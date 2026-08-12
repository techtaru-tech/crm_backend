<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Public embedded form (resources/views/forms/show.blade.php)
|------------------------------------------------------------
| Accessed via __('forms_public.<key>').
*/

return [

    // Multi-step navigation
    'back'    => 'رجوع',
    'next'    => 'التالي',

    // Submit button used in standalone & landing form sections
    'submit'  => 'إرسال',

    // ─── Embedded form view fallbacks (resources/views/forms/show.blade.php) ──
    'form_logo_alt'        => 'الشعار',
    'default_thank_you'    => 'شكرًا لك على الإرسال!',
    'select_placeholder'   => 'اختر...',
    'submitting'           => 'جارٍ الإرسال...',

    // ─── Landing form section labels ───────────────────────────────
    'name_label'    => 'الاسم',
    'email_label'   => 'البريد الإلكتروني',
    'phone_label'   => 'الهاتف',
    'required_mark' => '*',

    // ─── Landing form default success message ──────────────────────
    'landing_default_success' => 'شكرًا! تلقّينا إرسالك.',

    // ─── Inline-JS runtime strings (show.blade.php formWizard) ─────
    'js_submission_failed' => 'فشل الإرسال.',
    'js_network_error'     => 'خطأ في الشبكة. يُرجى المحاولة مرة أخرى.',
];
