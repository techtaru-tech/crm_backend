<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PublicFormController — submission response strings
|--------------------------------------------------------------------------
|
| Server-side fallbacks for the public form submit endpoint when the
| tenant has not configured a custom thank-you message on the form.
| Consumed via __('public_form.<key>').
|
*/

return [

    // ─── Default thank-you fallbacks ──────────────────────────────────
    'thank_you_short'         => 'Thank you!',
    'thank_you_full'          => 'Thank you for submitting!',
    'api_submitted_default'   => 'Submitted successfully.',
];
