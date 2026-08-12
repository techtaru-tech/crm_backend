<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * UI language entry — managed from /super-admin/locales.
 *
 * Rather than the old hard-coded config/locales.php, the operator
 * edits a DB table.  App\Providers\LocaleServiceProvider hydrates
 * config('locales.*') from here on every boot (cached 30 min).
 */
class Locale extends Model
{
    protected $fillable = [
        'code',
        'name',
        'english_name',
        'flag',
        'rtl',
        'enabled',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'rtl'        => 'boolean',
        'enabled'    => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const CACHE_KEY = 'locales.registry';
    public const CACHE_TTL = 1800; // 30 min

    public function scopeEnabled(Builder $q): Builder
    {
        return $q->where('enabled', true)->orderBy('sort_order');
    }

    /**
     * Shape: ['en' => ['name' => 'English', 'flag' => '…', 'rtl' => false], ...]
     * Drops straight into config('locales.supported').  Cached so the
     * boot-time DB hit is a one-off per interval.
     */
    public static function registry(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::enabled()
                ->get(['code', 'name', 'flag', 'rtl'])
                ->mapWithKeys(fn ($l) => [$l->code => [
                    'name' => $l->name,
                    'flag' => $l->flag ?: '🌐',
                    'rtl'  => (bool) $l->rtl,
                ]])
                ->all();
        });
    }

    public static function defaultCode(): string
    {
        $default = Cache::remember(self::CACHE_KEY . '.default', self::CACHE_TTL,
            fn () => self::enabled()->where('is_default', true)->value('code')
                ?? self::enabled()->value('code')
        );
        return $default ?: 'en';
    }

    public static function bustCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '.default');
    }

    /**
     * Bust the registry cache on every save/delete so the switcher +
     * editor per-locale tabs reflect changes immediately — no cache
     * clear command required.
     */
    protected static function booted(): void
    {
        $bust = fn () => self::bustCache();
        static::saved($bust);
        static::deleted($bust);
    }
}
