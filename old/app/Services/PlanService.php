<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

/**
 * Central service for querying plan limits, features, and usage.
 * All plan-gating logic in the app should go through this class.
 *
 * Plans are loaded from the `plans` DB table. On fresh installs or
 * environments where the migration hasn't run yet, the service falls
 * back to the legacy `config/plans.php` array so nothing breaks.
 */
class PlanService
{
    private const CACHE_KEY = 'leadhub.plans.cache.v1';
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Get the full plan definition for a tenant, keyed by the plan slug.
     * Returns the legacy-shaped array the rest of the app already consumes.
     */
    public function getPlan(?Tenant $tenant): array
    {
        $planKey = $tenant?->plan ?? $this->defaultKey();
        $plans   = $this->plansMap();

        return $plans[$planKey] ?? $plans[$this->defaultKey()] ?? [];
    }

    /**
     * Get all available plans for display (pricing page, etc).
     */
    public function getAllPlans(bool $publicOnly = false): array
    {
        $plans = $this->plansMap();

        if ($publicOnly) {
            $plans = array_filter($plans, fn ($p) => ($p['is_public'] ?? false) && ($p['is_active'] ?? true));
        }

        uasort($plans, fn ($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));

        return $plans;
    }

    /**
     * Invalidate the in-memory plan cache. Called by the PlanResource
     * on create/update/delete so admin edits take effect immediately.
     */
    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function getLimit(?Tenant $tenant, string $limitKey): int
    {
        if ($tenant) {
            $override = $tenant->getSetting("limits.{$limitKey}");
            if ($override !== null) {
                return (int) $override;
            }
        }

        $plan = $this->getPlan($tenant);
        return (int) ($plan['limits'][$limitKey] ?? 0);
    }

    public function hasFeature(?Tenant $tenant, string $featureKey): bool
    {
        if ($tenant) {
            $override = $tenant->getSetting("features.{$featureKey}");
            if ($override !== null) {
                return (bool) $override;
            }
        }

        $plan = $this->getPlan($tenant);
        return (bool) ($plan['features'][$featureKey] ?? false);
    }

    public function isWithinLimit(?Tenant $tenant, string $limitKey, int $currentCount): bool
    {
        $limit = $this->getLimit($tenant, $limitKey);

        if ($limit === -1) return true;
        if ($limit === 0)  return false;

        return $currentCount < $limit;
    }

    /**
     * Resolve the effective trial length for a plan key, in days.
     *
     * Lookup order (first positive value wins):
     *   1. plan.trial_days        (per-plan, set in PlanResource)
     *   2. BillingSettings.trial_days (global default, SA-editable)
     *   3. config('plans.trial_days') (env / file fallback)
     *   4. 14                      (hardcoded floor)
     *
     * Returns at least 1 so callers can safely `now()->addDays($n)`
     * without producing a same-day trial.  Pass null / empty key to
     * skip the per-plan lookup and use the global fallback chain.
     */
    public function resolveTrialDays(?string $planKey): int
    {
        if ($planKey) {
            try {
                $plan = $this->plansMap()[$planKey] ?? null;
                $perPlan = (int) ($plan['trial_days'] ?? 0);
                if ($perPlan > 0) {
                    return max(1, $perPlan);
                }
            } catch (\Throwable) {
                // fall through to global default
            }
        }

        try {
            $global = (int) (app(\App\Settings\BillingSettings::class)->trial_days ?? 0);
        } catch (\Throwable) {
            $global = 0;
        }

        if ($global > 0) {
            return max(1, $global);
        }

        return max(1, (int) config('plans.trial_days', 14));
    }

    public function canCreate(Tenant $tenant, string $resourceType): array
    {
        $limitKey = "max_{$resourceType}";
        $limit    = $this->getLimit($tenant, $limitKey);
        $current  = $this->getCurrentUsage($tenant, $resourceType);

        if ($limit === -1) {
            return ['allowed' => true, 'limit' => -1, 'current' => $current, 'reason' => ''];
        }

        if ($limit === 0) {
            return [
                'allowed' => false,
                'limit'   => 0,
                'current' => $current,
                'reason'  => __('services/plan.resource_not_included', [
                    'plan'     => $tenant->plan,
                    'resource' => $resourceType,
                ]),
            ];
        }

        if ($current >= $limit) {
            return [
                'allowed' => false,
                'limit'   => $limit,
                'current' => $current,
                'reason'  => __('services/plan.resource_limit_reached', [
                    'resource' => $resourceType,
                    'current'  => $current,
                    'limit'    => $limit,
                ]),
            ];
        }

        return ['allowed' => true, 'limit' => $limit, 'current' => $current, 'reason' => ''];
    }

    public function getCurrentUsage(Tenant $tenant, string $resourceType): int
    {
        return match ($resourceType) {
            'seats'          => $tenant->seat_count ?? 0,
            'leads'          => $this->countScoped($tenant, \App\Models\Lead::class),
            'forms'          => $this->countScoped($tenant, \App\Models\Form::class),
            'pipelines'      => $this->countScoped($tenant, \App\Models\Pipeline::class),
            'automations'    => $this->countScoped($tenant, \App\Models\Automation::class),
            'integrations'   => $this->countScoped($tenant, \App\Models\Integration::class),
            'api_keys'       => $this->countScoped($tenant, \App\Models\ApiKey::class),
            'custom_domains' => $this->countScoped($tenant, \App\Models\TenantDomain::class),
            default          => 0,
        };
    }

    protected function countScoped(Tenant $tenant, string $modelClass): int
    {
        if (! class_exists($modelClass)) {
            return 0;
        }

        try {
            return $modelClass::where('tenant_id', $tenant->id)->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    public function getUsageSummary(Tenant $tenant): array
    {
        $resources = ['seats', 'leads', 'forms', 'pipelines', 'automations', 'integrations', 'api_keys', 'custom_domains'];

        $summary = [];
        foreach ($resources as $resource) {
            $limitKey = "max_{$resource}";
            $limit    = $this->getLimit($tenant, $limitKey);
            $current  = $this->getCurrentUsage($tenant, $resource);

            $summary[$resource] = [
                'current'    => $current,
                'limit'      => $limit,
                'unlimited'  => $limit === -1,
                'disabled'   => $limit === 0,
                'percentage' => $limit > 0 ? min(100, round(($current / $limit) * 100)) : 0,
            ];
        }

        return $summary;
    }

    /*
    |--------------------------------------------------------------------------
    | Source of truth: DB table, with config fallback
    |--------------------------------------------------------------------------
    */

    /**
     * Return [planKey => legacyShapeArray] for the whole catalog, cached.
     */
    protected function plansMap(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $plans = $this->loadFromDatabase();

            if (empty($plans)) {
                return $this->loadFromConfig();
            }

            return $plans;
        });
    }

    protected function loadFromDatabase(): array
    {
        try {
            if (! \Schema::hasTable('plans')) {
                return [];
            }

            return Plan::query()
                ->orderBy('sort_order')
                ->get()
                ->mapWithKeys(function (Plan $p) {
                    $row = $p->toLegacyArray();
                    $row['name']        = $this->translateDisplay($p->key, 'name', $row['name'] ?? null);
                    $row['description'] = $this->translateDisplay($p->key, 'description', $row['description'] ?? null);
                    return [$p->key => $row];
                })
                ->all();
        } catch (\Throwable) {
            // During migrations, early boot, or when DB isn't ready yet.
            return [];
        }
    }

    protected function loadFromConfig(): array
    {
        $plans = config('plans.plans', []);

        // Normalize the config shape to match what the DB path returns.
        foreach ($plans as $key => &$plan) {
            $plan['key']       = $key;
            $plan['currency']  = $plan['currency']  ?? 'USD';
            $plan['is_active'] = $plan['is_active'] ?? true;

            // Pattern B: translator-first-with-fallback on display fields.
            // If lang/<locale>/plans.<key>.<field> is defined, use the
            // translation; otherwise fall back to the literal config value
            // so buyer-added plan keys keep working unchanged.
            $plan['name']        = $this->translateDisplay($key, 'name', $plan['name'] ?? null);
            $plan['description'] = $this->translateDisplay($key, 'description', $plan['description'] ?? null);
        }

        return $plans;
    }

    protected function defaultKey(): string
    {
        return config('plans.default', 'trial');
    }

    /**
     * Translator-first lookup for plan display fields.  Returns the
     * localized string when lang/<locale>/plans.php defines it; otherwise
     * returns the original config / DB value verbatim so PlanResource-
     * created custom plans render whatever the buyer typed.
     *
     * Mirrors the pattern used by App\Support\Currency::label() and
     * KanbanBoard source-label rendering elsewhere in the codebase.
     */
    protected function translateDisplay(?string $planKey, string $field, ?string $fallback): ?string
    {
        if ($planKey === null || $planKey === '') {
            return $fallback;
        }

        $langKey    = "plans.{$planKey}.{$field}";
        $translated = __($langKey);

        if (is_string($translated) && $translated !== $langKey) {
            return $translated;
        }

        return $fallback;
    }
}
