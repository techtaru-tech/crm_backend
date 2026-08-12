<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Public embedded form (resources/views/forms/show.blade.php)
|------------------------------------------------------------
| Accessed via __('forms_public.<key>').
*/

return [

    // Multi-step navigation
    'back'    => 'Back',
    'next'    => 'Next',

    // Submit button used in standalone & landing form sections
    'submit'  => 'Submit',

    // ─── Embedded form view fallbacks (resources/views/forms/show.blade.php) ──
    'form_logo_alt'        => 'Logo',
    'default_thank_you'    => 'Thank you for submitting!',
    'select_placeholder'   => 'Select...',
    'submitting'           => 'Submitting...',

    // ─── Landing form section labels ───────────────────────────────
    'name_label'    => 'Name',
    'email_label'   => 'Email',
    'phone_label'   => 'Phone',
    'required_mark' => '*',

    // ─── Landing form default success message ──────────────────────
    'landing_default_success' => 'Thanks! We received your submission.',

    // ─── Inline-JS runtime strings (show.blade.php formWizard) ─────
    'js_submission_failed' => 'Submission failed.',
    'js_network_error'     => 'Network error. Please try again.',
];
