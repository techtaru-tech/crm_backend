<?php

use Illuminate\Support\Facades\Route;

/**
 * Routes exposed by the HelloWorld example module. Every module
 * loads its own Routes/web.php through its service provider, so
 * keep namespaces scoped and use descriptive route names.
 */
Route::middleware(['web'])->group(function () {
    Route::get('/hello-world', function () {
        return view('helloworld::hello', [
            'name' => config('leadhub.branding.app_name', 'LeadHub'),
        ]);
    })->name('modules.helloworld.hello');
});
