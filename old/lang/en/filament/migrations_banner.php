<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — pending-migrations banner strings
|--------------------------------------------------------------------------
|
| Shown to Super-Admins on either panel when the DB has unrun migrations.
| Consumed via __('filament/migrations_banner.<key>').
|
*/

return [
    'message' => 'Your database has pending updates. Some new features stay hidden until you run them.',
    'cta'     => 'Run database migrations',
];
