<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| HelloWorld scaffold module
|------------------------------------------------------------
| Strings used by Modules/HelloWorld/Resources/views/hello.blade.php.
| The module ships disabled (module.json `"active": 0`) and is meant
| as a starter scaffold for buyers writing their own modules.
| Translated so the example demonstrates the i18n pattern.
*/

return [

    'page_title'                 => 'Hello from the HelloWorld module',
    'greeting'                   => 'Hello from :name',
    'proof_paragraph'            => 'This page is served from the <strong>HelloWorld</strong> module at :path. You are looking at proof that the HMVC layer is wired correctly end to end.',
    'starting_point_paragraph'   => 'Copy this module as a starting point for your own features — every module can ship routes, views, migrations, translations and Filament resources without ever touching the host app.',

];
