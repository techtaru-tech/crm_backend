<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('license.license_key', env('LEADHUB_LICENSE_KEY'));
        $this->migrator->add('license.license_status', null);
        $this->migrator->add('license.licensed_to', null);
        $this->migrator->add('license.expires_at', null);
        $this->migrator->add('license.last_checked_at', null);
    }
};
