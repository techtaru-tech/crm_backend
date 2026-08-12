<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| RegistrationController — public registration validator/messages
|--------------------------------------------------------------------------
|
| Translation strings used by RegistrationController custom-validator
| closures and validator message bag overrides. Accessed via
| __('controllers/registration.<key>').
|
*/

return [
    'reserved_workspace_url' => '":value" would produce a reserved workspace URL. Please pick a different name.',
    'must_accept_terms'      => 'You must accept the terms to continue.',
    'account_exists'         => 'An account with this email already exists. Try signing in instead.',
];
