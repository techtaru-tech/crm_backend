<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds the SA-editable trial_days field.  Default (14) matches the
 * previous config('plans.trial_days') default so upgrade-in-place
 * installs keep the same trial length until the operator changes
 * it from the Billing settings page.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('billing.trial_days', 14);
    }

    public function down(): void
    {
        $this->migrator->delete('billing.trial_days');
    }
};
