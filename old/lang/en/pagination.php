<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Pagination Language Lines (CodeCanyon Item 1)
|--------------------------------------------------------------------------
|
| Project-published copy of Laravel's framework default
| `pagination.php`.  Without this file, Laravel's `LengthAwarePaginator`
| renders the "Previous"/"Next" navigation arrows from
| vendor-shipped English strings, regardless of the active locale.
| Filament's table paginator stays in Filament's own locale catalog,
| but any direct use of Laravel's `{{ $paginator->links() }}` in a
| Blade view (portal dashboards, public pages, admin lists that
| haven't been migrated to Filament tables) consults this file.
|
| Keys mirror Laravel 12.x — translators only need to update the
| right-hand side strings; do NOT rename keys.
*/

return [

    'previous' => '&laquo; Previous',
    'next' => 'Next &raquo;',

];
