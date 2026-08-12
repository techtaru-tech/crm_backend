<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Automatic tenant-scoping for every model that "belongs to" a tenant.
 *
 * What it does:
 *  1. Adds a global scope that filters queries by the current tenant
 *     (resolved from `app('current_tenant')` or the authenticated user).
 *  2. Auto-stamps `tenant_id` on new records if not already set.
 *  3. Fails **closed** — when no tenant can be resolved and the user is
 *     not a Super Admin, every query returns an empty set rather than
 *     leaking data across tenants.
 *
 * Super Admin pages that need cross-tenant visibility must explicitly
 * call `->withoutGlobalScope('tenant')` on their queries.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            $tenantId = static::resolveTenantId();

            if ($tenantId) {
                $query->where($query->getModel()->getTable() . '.tenant_id', $tenantId);
            } elseif (! static::isSuperAdminContext()) {
                // Fail closed — empty result set, never leak.
                $query->whereRaw('0 = 1');
            }
            // Super Admin context without a specific tenant → unfiltered (cross-tenant view).
        });

        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = static::resolveTenantId();
            }
        });
    }

    /**
     * Resolve the current tenant ID from the request context.
     */
    protected static function resolveTenantId(): ?int
    {
        // 1. Explicit binding (set by ResolveTenant middleware).
        if (app()->bound('current_tenant') && app('current_tenant')) {
            return app('current_tenant')->id;
        }

        // 2. Authenticated user's tenant.
        $user = auth()->user();
        return $user?->tenant_id;
    }

    /**
     * Check whether the current request is running in a Super Admin context.
     */
    protected static function isSuperAdminContext(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
    }

    /**
     * Relationship shortcut — every tenant-owned model belongs to a Tenant.
     */
    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
