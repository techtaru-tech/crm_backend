<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Live-demo banner shown on both Filament panels
|------------------------------------------------------------
| Accessed via __('filament/demo_banner.<key>').
|
| Rendered by app/Providers/AppServiceProvider.php via a global
| FilamentView::registerRenderHook on BODY_START when DemoMode
| is on.  The view lives at
| resources/views/filament/demo-banner.blade.php.
*/

return [

    'banner_message' => '🎬 LIVE DEMO — destructive actions are disabled. This is a public preview of :app.',
    'get_app_link'   => 'Get :app →',

];
