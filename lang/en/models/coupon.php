<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Coupon model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/coupon.<key>').
*/

return [
    // ─── TYPES ─────────────────────────────────────
    'type_percent'         => 'Percent off',
    'type_fixed'           => 'Fixed amount off',
    'type_trial_extension' => 'Extend trial by N days',
];
