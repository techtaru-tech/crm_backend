<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LicenseSettingsPage strings — accessed via __('filament/license_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: LeadHub license key entry & verification.
| Visible to super-admins only.
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'License',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'License',

    // --- Section ------------------------------------------------------
    'section_description' => 'Enter the license key you received after purchasing LeadHub.',

    // --- Field labels --------------------------------------------------
    'license_key'             => 'License Key',
    'license_key_placeholder' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
    'license_key_helper'      => 'Your license key is found in your purchase receipt or your account dashboard.',

    // --- Verification feedback -----------------------------------------
    'verified_with_buyer' => 'Verified! Licensed to: :buyer.',
    'verified_generic'    => 'License verified successfully.',

    // --- Header actions ------------------------------------------------
    'verify_license' => 'Verify License',

    // --- Section headings ----------------------------------------------
    'license_status' => 'License Status',

    // ─── Blade view — status pills ─────────────────────────────────────
    'status_unknown'         => 'Unknown',
    'pill_active'            => 'Active',
    'pill_expired'           => 'Expired',
    'pill_not_activated'     => 'Not Activated',
    'pill_invalid'           => 'Invalid',

    // ─── Blade view — license details grid ─────────────────────────────
    'detail_licensed_to'     => 'Licensed to',
    'detail_license_type'    => 'License type',
    'detail_support_until'   => 'Support until',
    'detail_purchased'       => 'Purchased',
];
