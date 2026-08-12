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
    'message' => 'Su base de datos tiene actualizaciones pendientes. Algunas funciones nuevas permanecen ocultas hasta que las ejecute.',
    'cta'     => 'Ejecutar migraciones de la base de datos',
];
