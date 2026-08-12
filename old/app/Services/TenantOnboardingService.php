<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;

/**
 * Single source of truth for tenant-level onboarding state.
 *
 * The onboarding wizard's status flag has historically been read from
 * `tenants.settings['onboarding']` directly across 3+ files
 * (OnboardingWizard, CreateTenant SA action, RedirectToOnboarding
 * middleware) — each with its own ad-hoc array_merge spelling, and
 * each having to know which keys live in the bucket.
 *
 * This service mirrors the TenantSecurityService pattern: a documented
 * KEYS map with defaults, plus typed reader/writer methods.  Three
 * convenience helpers cover the common transitions every caller
 * actually wants: markPending() / markCompleted() / markSkipped().
 */
class TenantOnboardingService
{
    /** Documented keys for `tenants.settings['onboarding']` with defaults. */
    public const KEYS = [
        'pending'         => false,    // wizard hasn't been completed yet
        'created_at'      => null,     // ISO8601 — when SA provisioned the tenant
        'completed_at'    => null,     // ISO8601 — when wizard finished
        'completed_by_id' => null,     // int — user id who completed
        'skipped'         => false,    // wizard was bypassed via "Skip for now"
    ];

    /**
     * Full onboarding state for a tenant with every key resolved.
     *
     * @return array{pending: bool, created_at: ?string, completed_at: ?string, completed_by_id: ?int, skipped: bool}
     */
    public function all(?Tenant $tenant): array
    {
        $stored = (array) ($tenant?->getSetting('onboarding') ?? []);

        $out = [];
        foreach (self::KEYS as $key => $default) {
            $out[$key] = $stored[$key] ?? $default;
        }
        return $out;
    }

    public function isPending(?Tenant $tenant): bool
    {
        return (bool) $this->all($tenant)['pending'];
    }

    public function isCompleted(?Tenant $tenant): bool
    {
        return (string) $this->all($tenant)['completed_at'] !== '';
    }

    /**
     * Mark a tenant as awaiting onboarding.  Called from the SA
     * "Create Workspace" flow so RedirectToOnboarding bounces the
     * fresh admin to the wizard on first login.
     */
    public function markPending(Tenant $tenant): void
    {
        $this->merge($tenant, [
            'pending'    => true,
            'created_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Mark the wizard as completed by a real human walking through it.
     */
    public function markCompleted(Tenant $tenant, int $userId): void
    {
        $this->merge($tenant, [
            'pending'         => false,
            'completed_at'    => now()->toIso8601String(),
            'completed_by_id' => $userId,
        ]);
    }

    /**
     * Mark the wizard as completed via the "Skip for now" link — the
     * tenant is unblocked but the SA dashboard can show "skipped"
     * tenants separately so they can offer a guided re-do later.
     */
    public function markSkipped(Tenant $tenant, int $userId): void
    {
        $this->merge($tenant, [
            'pending'         => false,
            'completed_at'    => now()->toIso8601String(),
            'completed_by_id' => $userId,
            'skipped'         => true,
        ]);
    }

    /**
     * Merge a partial onboarding-state patch onto the tenant's
     * settings JSON column.  Unknown keys are dropped.
     */
    protected function merge(Tenant $tenant, array $patch): void
    {
        $clean = [];
        foreach (self::KEYS as $key => $_default) {
            if (array_key_exists($key, $patch)) {
                $clean[$key] = $patch[$key];
            }
        }

        $settings = (array) ($tenant->settings ?? []);
        $settings['onboarding'] = array_merge(
            (array) ($settings['onboarding'] ?? []),
            $clean,
        );

        $tenant->settings = $settings;
        $tenant->save();
    }
}
