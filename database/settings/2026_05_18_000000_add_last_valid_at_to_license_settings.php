<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Add the license.last_valid_at slot to the settings table.
 *
 * This timestamp records the LAST time the licensing server returned
 * "valid" for the stored licence key.  It drives the EnforceLicense
 * middleware's grace window: if the server later starts returning
 * "invalid" (transient outage, manual revoke, etc.), the admin panels
 * stay accessible for config('leadhub.license.grace_days') days from
 * this value before being blocked.  Demo installs
 * (LEADHUB_DEMO_MODE=true) bypass the whole gate.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('license.last_valid_at', null);
    }
};
