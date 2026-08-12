<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Password Reset Language Lines (CodeCanyon Item 1)
|--------------------------------------------------------------------------
|
| Project-published copy of Laravel's framework default `passwords.php`.
| Without this file, Laravel falls back to vendor-shipped English
| strings.  These line keys are consumed by Laravel's
| `Illuminate\Auth\Passwords\PasswordBroker` (which Filament's
| Reset/Request password pages call internally) when a reset attempt
| succeeds, fails, gets throttled, or hits an invalid token.
|
| Keys mirror Laravel 12.x — translators only need to update the
| right-hand side strings; do NOT rename keys.
*/

return [

    'reset' => 'Your password has been reset.',
    'sent' => 'We have emailed your password reset link.',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",

];
