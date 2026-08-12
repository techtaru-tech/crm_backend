<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class UpdateSettings extends Settings
{
    public ?string $latest_version  = null;
    public ?string $changelog_url   = null;
    public ?string $last_checked_at = null;
    public bool $update_available   = false;

    public static function group(): string
    {
        return 'updates';
    }
}
