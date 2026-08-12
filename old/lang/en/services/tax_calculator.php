<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| TaxCalculator — VAT/tax invoice notes
|--------------------------------------------------------------------------
|
| Translation strings used by TaxCalculator when shaping tax-result
| payloads. Accessed via __('services/tax_calculator.<key>').
|
| Note: the reverse-charge note is regulated language under EU VAT
| Directive 2006/112/EC Art. 226(11). Translate carefully — the
| recipient's accountant relies on it to recognise the obligation.
|
*/

return [
    'vat_reverse_charged_note' => 'VAT reverse-charged. The recipient is liable to account for VAT on this supply.',
];
