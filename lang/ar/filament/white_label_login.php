<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| WhiteLabelLogin page — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/white_label_login.<key>').
*/

return [

    // ----- Page heading (tenant-branded login) -----
    // :app is interpolated at the call site with the tenant's
    // resolved app name (white-label brand), or the global
    // leadhub.branding.app_name config value as fallback.
    'sign_in_title' => 'تسجيل الدخول إلى :app',
];
