<?php

/**
 * Central registry of UI locales shipped by LeadHub. The Super Admin
 * can toggle the global default via the Script Settings page, and
 * any visitor can flip their session language with /locale/{code}.
 *
 * Add new entries here — no code changes required elsewhere — as
 * long as matching lang/<code>/*.php files exist.
 */
return [

    /*
    |--------------------------------------------------------------
    | Supported UI languages
    |--------------------------------------------------------------
    |
    | 'name' : native label shown in switchers
    | 'flag' : emoji flag used in the locale switcher dropdown
    | 'rtl'  : true for right-to-left scripts (adds dir="rtl")
    |
    */

    'supported' => [
        'en' => ['name' => 'English',  'flag' => '🇬🇧', 'rtl' => false],
        'ar' => ['name' => 'العربية',  'flag' => '🇸🇦', 'rtl' => true],
        'es' => ['name' => 'Español',  'flag' => '🇪🇸', 'rtl' => false],
        'hi' => ['name' => 'हिन्दी',    'flag' => '🇮🇳', 'rtl' => false],
    ],

    'default' => 'en',

];
