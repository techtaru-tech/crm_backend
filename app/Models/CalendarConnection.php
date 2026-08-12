<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per connected external calendar (Google or Outlook).
 *
 * Tokens are stored encrypted via Laravel's `encrypted` cast — the
 * cast handles encrypt-on-write + decrypt-on-read transparently, so
 * the application code reads/writes plain strings while the DB stores
 * ciphertext.  Rotating APP_KEY would invalidate all rows; reconnects
 * are the recovery path.
 */
class CalendarConnection extends Model
{
    use HasFactory;

    public const PROVIDER_GOOGLE  = 'google';
    public const PROVIDER_OUTLOOK = 'outlook';

    public const STATUS_ACTIVE       = 'active';
    public const STATUS_NEEDS_REAUTH = 'needs_reauth';
    public const STATUS_ERROR        = 'error';
    public const STATUS_DISABLED     = 'disabled';

    public const STATUSES = [
        self::STATUS_ACTIVE       => 'Active',
        self::STATUS_NEEDS_REAUTH => 'Needs reconnect',
        self::STATUS_ERROR        => 'Error',
        self::STATUS_DISABLED     => 'Disabled',
    ];

    /**
     * Translated status labels for display.
     * Keys mirror self::STATUSES.
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE       => __('models/calendar_connection.status_active'),
            self::STATUS_NEEDS_REAUTH => __('models/calendar_connection.status_needs_reauth'),
            self::STATUS_ERROR        => __('models/calendar_connection.status_error'),
            self::STATUS_DISABLED     => __('models/calendar_connection.status_disabled'),
        ];
    }

    protected $fillable = [
        'tenant_id',
        'user_id',
        'provider',
        'external_account_email',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
        'calendar_id',
        'sync_token',
        'last_synced_at',
        'last_sync_error',
        'status',
        'metadata',
    ];

    protected $casts = [
        'access_token'   => 'encrypted',
        'refresh_token'  => 'encrypted',
        'expires_at'     => 'datetime',
        'scopes'         => 'array',
        'last_synced_at' => 'datetime',
        'metadata'       => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * True when access_token is missing, expired, or about to expire
     * within the next 60 seconds.  Sync services check this before
     * each API call and refresh proactively.
     */
    public function isStale(): bool
    {
        if (empty($this->access_token)) return true;
        if (! $this->expires_at) return false;
        return $this->expires_at->lte(Carbon::now()->addSeconds(60));
    }

    public function markError(string $message): void
    {
        $this->forceFill([
            'last_sync_error' => $message,
            'status'          => self::STATUS_ERROR,
        ])->save();
    }

    public function markNeedsReauth(?string $message = null): void
    {
        // PHP default-param values must be constant expressions, so we
        // can't inline __() in the signature. Resolve the translated
        // fallback at call-time so the writer's locale wins.
        $resolved = $message ?? (string) __('services/calendar_sync.refresh_token_revoked');

        $this->forceFill([
            'last_sync_error' => $resolved,
            'status'          => self::STATUS_NEEDS_REAUTH,
        ])->save();
    }

    public function markSyncSuccess(?string $newSyncToken = null): void
    {
        $this->forceFill([
            'sync_token'      => $newSyncToken ?? $this->sync_token,
            'last_synced_at'  => Carbon::now(),
            'last_sync_error' => null,
            'status'          => self::STATUS_ACTIVE,
        ])->save();
    }

    public function scopeActive($q)
    {
        return $q->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForProvider($q, string $provider)
    {
        return $q->where('provider', $provider);
    }
}
