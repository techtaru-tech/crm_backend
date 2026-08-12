<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;

/**
 * Single source of truth for tenant-level notification preferences
 * stored on `tenants.settings['notifications']`.
 *
 * Mirrors the TenantSecurityService / TenantOnboardingService pattern.
 * As of M-A2 there are no production consumers yet — this service is
 * the documented home for future SA-side "set the digest frequency
 * for this tenant's users" or per-tenant channel toggles, so the
 * keys live in one place from day one.
 */
class TenantNotificationService
{
    /** Documented keys for `tenants.settings['notifications']` with defaults. */
    public const KEYS = [
        // realtime | hourly | daily — controls per-user digest cadence
        // when the user hasn't overridden it on their own preferences.
        'digest_frequency'   => 'realtime',
        // List of channels to publish to.  Per-event filtering still
        // happens via the per-user notification_preferences table.
        'channels'           => ['in_app', 'email'],
        // Hour-of-day (0-23, tenant timezone) the daily digest is sent.
        'daily_digest_hour'  => 9,
    ];

    /**
     * Full notification settings for a tenant with every key resolved
     * (tenant override OR documented default).
     *
     * @return array{digest_frequency: string, channels: array<int,string>, daily_digest_hour: int}
     */
    public function all(?Tenant $tenant): array
    {
        $stored = (array) ($tenant?->getSetting('notifications') ?? []);

        return [
            'digest_frequency'  => (string) ($stored['digest_frequency'] ?? self::KEYS['digest_frequency']),
            'channels'          => (array)  ($stored['channels']         ?? self::KEYS['channels']),
            'daily_digest_hour' => (int)    ($stored['daily_digest_hour'] ?? self::KEYS['daily_digest_hour']),
        ];
    }

    public function get(?Tenant $tenant, string $key, mixed $default = null): mixed
    {
        return $this->all($tenant)[$key] ?? $default ?? (self::KEYS[$key] ?? null);
    }

    /**
     * Persist a partial notifications-settings array.  Unknown keys
     * are dropped; values are normalized.
     */
    public function save(Tenant $tenant, array $values): array
    {
        $clean = [];
        foreach (self::KEYS as $key => $_default) {
            if (! array_key_exists($key, $values)) {
                continue;
            }
            $clean[$key] = match ($key) {
                'digest_frequency'  => in_array($values[$key], ['realtime', 'hourly', 'daily'], true)
                    ? $values[$key]
                    : 'realtime',
                'channels'          => array_values(array_filter((array) $values[$key], fn ($v) => filled($v))),
                'daily_digest_hour' => max(0, min(23, (int) $values[$key])),
                default             => $values[$key],
            };
        }

        $settings = (array) ($tenant->settings ?? []);
        $settings['notifications'] = array_merge(
            (array) ($settings['notifications'] ?? []),
            $clean,
        );

        $tenant->settings = $settings;
        $tenant->save();

        return $clean;
    }
}
