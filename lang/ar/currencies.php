<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Currency display labels (ISO-4217 code → human-readable name)
|------------------------------------------------------------
| Accessed via __('currencies.<iso_code_lowercase>').
|
| Consumed by app/Support/Currency.php::label() and ::options()
| which feed the Filament Select dropdowns on GeneralSettings,
| Script Settings, plan create forms, etc.
*/

return [
    'usd' => 'دولار أمريكي',
    'eur' => 'يورو',
    'gbp' => 'جنيه إسترليني',
    'inr' => 'روبية هندية',
    'ngn' => 'نايرا نيجيرية',
    'zar' => 'راند جنوب أفريقي',
    'kes' => 'شلن كيني',
    'ghs' => 'سيدي غاني',
    'brl' => 'ريال برازيلي',
    'mxn' => 'بيزو مكسيكي',
    'aud' => 'دولار أسترالي',
    'cad' => 'دولار كندي',
    'aed' => 'درهم إماراتي',
    'sar' => 'ريال سعودي',
    'idr' => 'روبية إندونيسية',
    'php' => 'بيزو فلبيني',
    'myr' => 'رينغيت ماليزي',
    'try' => 'ليرة تركية',
    'egp' => 'جنيه مصري',
    'pkr' => 'روبية باكستانية',
    'jpy' => 'ين ياباني',
    'cny' => 'يوان صيني',
];
