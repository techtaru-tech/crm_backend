<?php

namespace App\Services;

use App\Models\Tenant;
use App\Settings\SecuritySettings;

/**
 * Single source of truth for security settings in a multi-tenant SaaS.
 *
 * Reads/writes from $tenant->settings['security'] with a documented set
 * of keys. Falls back to the application-global Spatie SecuritySettings
 * defaults when a tenant hasn't overridden a value.
 *
 * Middleware (EnforceSecuritySettings, Enforce2Fa) and the tenant-side
 * Settings page should all go through this service — never read Spatie
 * directly again, or you'll leak one tenant's policy to another.
 */
class TenantSecurityService
{
    /** Documented keys with sensible defaults. */
    public const KEYS = [
        'enforce_2fa'        => false,
        'ip_whitelist'       => [],
        'session_lifetime'   => 120,
        'max_login_attempts' => 5,
        'lockout_duration'   => 15,
    ];

    /**
     * Full settings array for a tenant with every key resolved
     * (tenant override OR app-global default).
     */
    public function all(?Tenant $tenant): array
    {
        $globals = $this->globalDefaults();

        if (! $tenant) {
            return $globals;
        }

        $perTenant = (array) ($tenant->getSetting('security') ?? []);

        $out = [];
        foreach (self::KEYS as $key => $fallback) {
            $out[$key] = $perTenant[$key]
                ?? $globals[$key]
                ?? $fallback;
        }
        return $out;
    }

    public function get(?Tenant $tenant, string $key, mixed $default = null): mixed
    {
        $all = $this->all($tenant);
        return $all[$key] ?? $default ?? (self::KEYS[$key] ?? null);
    }

    /**
     * Persist a security settings array to a tenant's settings column.
     * Unknown keys are dropped; empty values are normalized.
     */
    public function save(Tenant $tenant, array $values): array
    {
        $clean = [];
        foreach (self::KEYS as $key => $default) {
            if (! array_key_exists($key, $values)) {
                continue;
            }
            $clean[$key] = match ($key) {
                'enforce_2fa'        => (bool) $values[$key],
                'ip_whitelist'       => array_values(array_filter((array) $values[$key], fn ($v) => filled($v))),
                'session_lifetime'   => max(15, min(43200, (int) $values[$key])),
                'max_login_attempts' => max(1, min(100, (int) $values[$key])),
                'lockout_duration'   => max(1, min(1440, (int) $values[$key])),
                default              => $values[$key],
            };
        }

        $settings = $tenant->settings ?? [];
        $settings['security'] = array_merge(
            (array) ($settings['security'] ?? []),
            $clean,
        );

        $tenant->settings = $settings;
        $tenant->save();

        return $clean;
    }

    /**
     * Globally-configured defaults from Spatie SecuritySettings.
     * Wrapped in try/catch so we never explode if the table is missing
     * (e.g. during fresh install before migrations run).
     */
    protected function globalDefaults(): array
    {
        try {
            $g = app(SecuritySettings::class);
            return [
                'enforce_2fa'        => (bool) $g->enforce_2fa,
                'ip_whitelist'       => (array) $g->ip_whitelist,
                'session_lifetime'   => (int) $g->session_lifetime,
                'max_login_attempts' => (int) $g->max_login_attempts,
                'lockout_duration'   => (int) $g->lockout_duration,
            ];
        } catch (\Throwable) {
            return self::KEYS;
        }
    }
}
