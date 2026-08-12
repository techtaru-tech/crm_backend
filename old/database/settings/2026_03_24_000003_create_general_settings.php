<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.timezone', config('app.timezone', 'UTC'));
        $this->migrator->add('general.date_format', 'Y-m-d');
        $this->migrator->add('general.language', 'en');
        $this->migrator->add('general.currency', 'USD');
    }
};
