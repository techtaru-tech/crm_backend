<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('updates.latest_version', null);
        $this->migrator->add('updates.changelog_url', null);
        $this->migrator->add('updates.last_checked_at', null);
        $this->migrator->add('updates.update_available', false);
    }
};
