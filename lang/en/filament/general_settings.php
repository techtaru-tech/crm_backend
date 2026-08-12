<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| GeneralSettingsPage strings — accessed via __('filament/general_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: localisation defaults (timezone, date format,
| language, currency, default locale).
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'General',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'General Settings',

    // --- Field labels --------------------------------------------------
    'timezone'                       => 'Timezone',
    'date_format'                    => 'Date Format',
    'language'                       => 'Language',
    'language_helper'                => 'Additional languages will be available in a future release.',
    'workspace_currency'             => 'Workspace currency',
    'workspace_currency_placeholder' => 'Use the system default',
    'workspace_currency_helper'      => 'Used by pricing widgets, dashboards, and money formatting throughout your workspace.',
    'default_locale'                 => 'Default locale',
    'default_locale_placeholder'     => 'Use the system default',

    // --- Invoicing -----------------------------------------------------
    'invoicing_section'              => 'Invoicing',
    'invoicing_section_description'  => 'Defaults applied to new invoices and quotes.',
    'default_tax_rate'               => 'Default tax / VAT rate',
    'default_tax_rate_helper'        => 'Pre-fills the Tax Rate on every new invoice and quote. You can still change it per document. Leave at 0 for no default.',

    // --- Header actions ------------------------------------------------
    'save_settings' => 'Save Settings',

    // ─── Select options — locale dropdown ──────────────────────────
    'option_locale_en'    => 'English',
    'option_locale_en_US' => 'English (United States)',
    'option_locale_en_GB' => 'English (United Kingdom)',
    'option_locale_de'    => 'German',
    'option_locale_de_DE' => 'German (Germany)',
    'option_locale_fr'    => 'French',
    'option_locale_fr_FR' => 'French (France)',
    'option_locale_es'    => 'Spanish',
    'option_locale_es_ES' => 'Spanish (Spain)',
    'option_locale_it_IT' => 'Italian (Italy)',
    'option_locale_pt_BR' => 'Portuguese (Brazil)',
    'option_locale_nl_NL' => 'Dutch (Netherlands)',
    'option_locale_pl_PL' => 'Polish (Poland)',
    'option_locale_tr_TR' => 'Turkish (Turkey)',
    'option_locale_ar'    => 'Arabic',
    'option_locale_ja_JP' => 'Japanese (Japan)',
    'option_locale_zh_CN' => 'Chinese (Simplified)',

];
