<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin LocaleResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_locales.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_locales.php.
*/

return [

    // ----- Resource labels -----
    'language'                    => 'Language',
    'languages'                   => 'Languages',

    // ----- Language details form -----
    'code_helper'                 => 'Two-letter ISO code (or locale tag): en, fr, de, el, pt-br, zh-tw. Lower-case.',
    'native_name'                 => 'Native name',
    'native_name_helper'          => 'Shown in the language switcher. Write it in the language itself: Français, Ελληνικά, Deutsch.',
    'native_name_placeholder'     => 'Français',
    'english_name'                => 'English name',
    'english_name_helper'         => 'Shown in this admin list. Optional but useful.',
    'english_name_placeholder'    => 'French',
    'flag_label'                  => 'Flag (emoji)',
    'flag_helper'                 => 'Paste an emoji flag. Leave blank for a generic globe.',
    'sort_order_helper'           => 'Lower = earlier in the switcher.',

    // ----- Behaviour section -----
    'enabled_helper'              => 'Disable to hide the language from the switcher without deleting it. Existing translations are preserved.',
    'rtl_label'                   => 'Right-to-left',
    'rtl_helper'                  => 'Enable for Arabic, Hebrew, Persian, Urdu. Adds dir="rtl" to the <html> tag for this locale.',
    'is_default_label'            => 'Default language',
    'is_default_helper'           => 'New visitors land in this language. Only one locale can be default — saving this one as default clears the flag on the previous.',

    // ----- Table columns -----
    'column_native'               => 'Native',
    'column_english'              => 'English',
    'column_on'                   => 'On',
    'column_rtl'                  => 'RTL',
    'column_default'              => 'Default',
    'column_order'                => 'Order',
    'column_admin_ui'             => 'Admin UI',
    'admin_ui_shipped'            => 'Shipped',
    'admin_ui_installed'          => 'Installed',
    'admin_ui_english_fallback'   => 'English fallback',
    'admin_ui_tooltip'            => 'Whether the admin-panel UI has translated strings for this language. Marketing site translations are managed separately from the Landing / Static Page editors.',

    // ----- Make default action -----
    'make_default'                => 'Make default',
    'make_default_description'    => 'New visitors will land in :name by default.',
    'default_language_set'        => 'Default language set to :name.',

    // ----- Delete action -----
    'delete_description'          => 'Deleting a language removes it from the switcher but PRESERVES any per-locale content you\'ve typed into the Landing Page Editor or Static Pages — re-adding the language later restores it.',

    // ----- List action -----
    'add_language'                => 'Add language',

    // ----- CreateLocale notifications -----
    'created_marketing_ready_title'   => ':name added — marketing site ready',
    'created_marketing_ready_body'    => 'Language switcher + landing / static-page editor tabs are now available for this locale. The admin panel itself will still render in English for users who pick this language, because no `lang/:code/` directory is installed. Ask your service provider to drop a Filament language pack if you need full admin-UI translation.',
    'created_success_title'           => ':name added.',

    // ----- Field labels (form + table) -----
    'code'                            => 'Code',
    'sort_order'                      => 'Sort order',
    'enabled'                         => 'Enabled',
    'flag'                            => 'Flag',

    // ----- Model labels -----
    'model_label'                     => 'Locale',
    'plural_model_label'              => 'Locales',

];
