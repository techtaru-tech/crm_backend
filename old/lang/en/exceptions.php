<?php

return [
    'feature_not_available' => "The ':feature' feature is not included in your current plan.",
    'plan_limit_exceeded'   => "You have reached your plan's :resource limit (:current/:limit).",

    // Defensive guards thrown when the tenant context is missing.
    // Reached only when the SettingsService is called outside a
    // resolved tenant + impersonation scope — should not happen on a
    // healthy install but surfaces via SafeAction's Notification body
    // when it does, so it must be translated.
    'no_current_tenant'     => 'No current tenant resolved.',

    // ConnectorRegistry throws this when a source key (typeform, jotform,
    // calendly, web_form, etc.) doesn't match a registered connector
    // class.  Default-deny here protects against typo'd sources in
    // Filament Actions where the lambda body isn't wrapped in SafeAction.
    'connector_not_registered' => 'No connector registered for source: :source',
];
