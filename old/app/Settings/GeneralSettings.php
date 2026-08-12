<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $timezone   = 'UTC';
    public string $date_format = 'Y-m-d';
    public string $language   = 'en';
    public string $currency   = 'USD';
    public bool $auto_nightly_backup = false;

    public static function group(): string
    {
        return 'general';
    }
}
