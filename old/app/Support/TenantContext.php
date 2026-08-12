<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Tenant;

/**
 * Single source of truth for resolving the current tenant id within
 * an HTTP request, queue worker, or CLI context.
 *
 * Before this helper, the resolution chain
 *
 *     \App\Support\TenantContext::currentId()
 *
 * was repeated literally across ~58 files (Filament pages, jobs,
 * controllers, services).  Any future change to the resolution rules
 * — adding session-based tenant switching, agency parent-tenant
 * resolution, an SA-impersonation override — would have meant 58
 * separate edits, with the certainty of missing one.
 *
 * Migration: replace
 *
 *     $tenantId = \App\Support\TenantContext::currentId();
 *
 * with
 *
 *     $tenantId = TenantContext::currentId();
 *
 * Existing call sites can migrate incrementally; new code MUST use
 * this helper.
 */
final class TenantContext
{
    /**
     * Return the current tenant's id, or null if none can be resolved
     * (unauthenticated request, queue worker without a job-bound
     * tenant, CLI command).  Mirrors what
     * {@see \App\Models\Concerns\BelongsToTenant::resolveTenantId()}
     * does internally so the global scope and explicit filters always
     * agree.
     */
    public static function currentId(): ?int
    {
        $bound = app()->bound('current_tenant') ? app('current_tenant') : null;
        if ($bound instanceof Tenant && $bound->id) {
            return (int) $bound->id;
        }

        $user = auth()->user();
        $tenantId = $user?->tenant_id ?? null;

        return $tenantId !== null ? (int) $tenantId : null;
    }

    /**
     * Return the current Tenant model, or null.  Prefer this when the
     * caller needs the model (for getSetting / hasFeature / etc.) —
     * it avoids a round-trip through currentId() + Tenant::find().
     */
    public static function current(): ?Tenant
    {
        $bound = app()->bound('current_tenant') ? app('current_tenant') : null;
        if ($bound instanceof Tenant) {
            return $bound;
        }

        $tenantId = self::currentId();
        return $tenantId !== null
            ? Tenant::withoutGlobalScope('tenant')->find($tenantId)
            : null;
    }
}
