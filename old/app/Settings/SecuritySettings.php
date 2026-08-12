<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SecuritySettings extends Settings
{
    public bool $enforce_2fa        = false;
    public array $ip_whitelist      = [];
    public int $session_lifetime    = 120;
    public int $max_login_attempts  = 5;
    public int $lockout_duration    = 15;

    public static function group(): string
    {
        return 'security';
    }
}
