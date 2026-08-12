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
    'usd' => 'US Dollar',
    'eur' => 'Euro',
    'gbp' => 'British Pound',
    'inr' => 'Indian Rupee',
    'ngn' => 'Nigerian Naira',
    'zar' => 'South African Rand',
    'kes' => 'Kenyan Shilling',
    'ghs' => 'Ghanaian Cedi',
    'brl' => 'Brazilian Real',
    'mxn' => 'Mexican Peso',
    'aud' => 'Australian Dollar',
    'cad' => 'Canadian Dollar',
    'aed' => 'UAE Dirham',
    'sar' => 'Saudi Riyal',
    'idr' => 'Indonesian Rupiah',
    'php' => 'Philippine Peso',
    'myr' => 'Malaysian Ringgit',
    'try' => 'Turkish Lira',
    'egp' => 'Egyptian Pound',
    'pkr' => 'Pakistani Rupee',
    'jpy' => 'Japanese Yen',
    'cny' => 'Chinese Yuan',
];
