<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdminLogin — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_login.<key>').
*/

return [

    'title' => 'Super Admin sign in',

    // ─── Disambiguation banner (resources/views/filament/super-admin/login-disambiguation.blade.php) ──
    'disambig_title'              => '🔐 Super Admin login',
    'disambig_body_html'          => "You're about to access the <strong>Super Admin</strong> panel - for the platform owner who manages every workspace on this LeadHub install. If you're a <strong>workspace admin</strong> or team member, please use the :tenant_login_link instead.",
    'disambig_tenant_login_label' => 'tenant admin login',

];
