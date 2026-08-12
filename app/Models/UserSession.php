<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'last_active_at',
        'revoked_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'revoked_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
        $this->destroyUnderlyingSession();
    }

    protected function destroyUnderlyingSession(): void
    {
        $driver = config('session.driver', 'file');

        if ($driver === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('id', $this->session_id)
                ->delete();
        } elseif ($driver === 'file') {
            $path = config('session.files', storage_path('framework/sessions'));
            $file = $path . DIRECTORY_SEPARATOR . $this->session_id;
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    public static function parseDeviceInfo(string $userAgent): array
    {
        $device = 'desktop';
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
            $device = preg_match('/iPad/i', $userAgent) ? 'tablet' : 'mobile';
        }

        $browser = (string) __('filament/sessions.browser_unknown');
        if (str_contains($userAgent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            $browser = 'Edge';
        }

        $platform = (string) __('filament/sessions.platform_unknown');
        if (str_contains($userAgent, 'Windows')) {
            $platform = 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            $platform = 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $platform = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $platform = 'Android';
        } elseif (str_contains($userAgent, 'iOS')) {
            $platform = 'iOS';
        }

        return compact('device', 'browser', 'platform');
    }
}
