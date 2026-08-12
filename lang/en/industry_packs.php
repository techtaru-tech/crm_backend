<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Industry Packs — display labels
|--------------------------------------------------------------------------
| Mirrors the pack-level `name` and `description` fields of each
| config/industry_packs/<slug>.php so the installer browser cards
| (rendered by app/Filament/Pages/IndustryPackInstaller.php and its Blade
| view resources/views/filament/pages/industry-pack-installer.blade.php)
| can display localized text.
|
| Sub-content (pipeline names, stage names, tag names, email-template
| subjects/bodies, form labels) is intentionally NOT translated here —
| once installed, those items are saved as tenant-owned rows that buyers
| edit through the admin panel.  English defaults are kept literal in the
| config files so the install operation never silently writes a foreign
| string into the tenant's DB based on the SuperAdmin's locale.
|
| Keys MUST match the `key` field of each pack file (agency, law_firm,
| medical_clinic, real_estate).  Unknown / buyer-added packs fall through
| to the literal config value via the translator-first-with-fallback
| pattern in IndustryPackService::packs().
*/

return [

    'agency' => [
        'name'        => 'Digital Marketing Agency',
        'description' => 'Sales pipeline, retainer tracking, and proposal-to-close automations tuned for digital marketing & creative agencies.',
    ],

    'law_firm' => [
        'name'        => 'Law Firm',
        'description' => 'Intake and case pipelines, conflict-check tasks, statute-of-limitations alerts, and confidential consult form.',
    ],

    'medical_clinic' => [
        'name'        => 'Medical Clinic',
        'description' => 'Patient intake pipeline, appointment reminders, insurance verification, and a HIPAA-style consultation form.',
    ],

    'real_estate' => [
        'name'        => 'Real Estate',
        'description' => 'Buyer & seller pipelines, property interest fields, viewing automations, and lead-capture forms tuned for realtors.',
    ],
];
