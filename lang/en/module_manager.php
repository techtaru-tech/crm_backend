<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ModuleManagerService — exception messages
|--------------------------------------------------------------------------
|
| User-facing exception messages thrown by App\Services\ModuleManagerService.
| These surface to the Super Admin Modules page via $e->getMessage()
| in Filament notifications. Consumed via __('module_manager.<key>').
|
*/

return [

    // ─── nwidart/laravel-modules availability ─────────────────────────
    'error_system_unavailable'                  => 'Module system unavailable — please re-upload the full LeadHub distribution zip and ensure the vendor/ folder is included.',

    // ─── Zip upload + extraction ──────────────────────────────────────
    'error_upload_not_found'                    => 'Upload not found: :path',
    'error_cannot_open_zip'                     => 'Could not open the uploaded zip file.',
    'error_missing_module_json'                 => 'Uploaded archive does not contain a module.json file.',
    'error_module_json_missing_name'            => 'module.json is missing a "name" field.',
    'error_invalid_module_name'                 => 'Invalid module name in module.json.',
];
