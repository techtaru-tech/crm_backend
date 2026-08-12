<?php

return [
    'feature_not_available' => 'الميزة «:feature» غير مشمولة في خطتك الحالية.',
    'plan_limit_exceeded'   => 'لقد بلغت حد :resource في خطتك (:current/:limit).',

    // Defensive guards thrown when the tenant context is missing.
    // Reached only when the SettingsService is called outside a
    // resolved tenant + impersonation scope — should not happen on a
    // healthy install but surfaces via SafeAction's Notification body
    // when it does, so it must be translated.
    'no_current_tenant'     => 'لم يتم تحديد مساحة عمل حالية.',

    // ConnectorRegistry throws this when a source key (typeform, jotform,
    // calendly, web_form, etc.) doesn't match a registered connector
    // class.  Default-deny here protects against typo'd sources in
    // Filament Actions where the lambda body isn't wrapped in SafeAction.
    'connector_not_registered' => 'لا يوجد موصِّل مسجَّل للمصدر: :source',
];
