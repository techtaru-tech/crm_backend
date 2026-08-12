<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| DomainSettingsPage strings — accessed via __('filament/domain_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: custom domain configuration (CNAME setup +
| async DNS verification).  Visible to whitelabel/enterprise plans only.
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Custom Domain',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Custom Domain',

    // --- Section description -------------------------------------------
    'section_description' => 'Point your custom domain to this platform. Add a CNAME record pointing to: :host',

    // --- Field labels --------------------------------------------------
    'custom_domain'             => 'Custom Domain',
    'custom_domain_placeholder' => 'leads.yourcompany.com',
    'custom_domain_helper'      => 'Enter your domain without https://. Then add a CNAME record: leads.yourcompany.com → :host',

    // --- Header actions ------------------------------------------------
    'reverify_dns' => 'Re-verify DNS',
    'save_domain'  => 'Save Domain',

    // ─── Blade view — DNS instructions card ────────────────────────────
    'current_domain_label'      => 'Current Domain:',
    'status_verified'           => 'Verified',
    'status_pending'            => 'Pending Verification',
    'dns_record_lede'           => 'Add the following DNS record to your domain:',
    'col_type'                  => 'Type',
    'col_name'                  => 'Name',
    'col_value'                 => 'Value',
    'dns_txt_hint_html'         => 'Or add a TXT record: <code class="ds-code">_leadhub-verify.:domain</code> → <code class="ds-code">:token</code>',
    'dns_propagation_hint'      => 'DNS propagation can take up to 24 hours. Verification runs automatically in the background.',
    'dns_record_type_cname'     => 'CNAME',

];
