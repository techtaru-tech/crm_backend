<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\LeadImport;
use App\Support\TenantCache;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic tenant-scoped cache invalidator.
 *
 * Flushes every cached value tagged for a model's tenant on save and
 * delete. This is what makes nav badges and dashboard widgets feel
 * "live" on Redis-backed installs - a write happens, the relevant
 * cache evicts, the next render fetches fresh data.
 *
 * Cheap on Redis (one tag-bump per call). NO-OP on file/database
 * cache because TenantCache::flushTenant short-circuits on those.
 *
 * Tenant resolution differs per model:
 *   - Lead and LeadImport carry tenant_id directly.
 *   - AutomationRun carries automation_id; we read the related
 *     automation's tenant_id (loads at most once - the relation is
 *     usually already loaded by the code path that triggered the save).
 */
class TenantCacheFlushObserver
{
    public function saved(Model $model): void
    {
        $this->flushFor($model);
    }

    public function deleted(Model $model): void
    {
        $this->flushFor($model);
    }

    private function flushFor(Model $model): void
    {
        $tenantId = $this->resolveTenantId($model);
        if ($tenantId === null) {
            return;
        }
        TenantCache::flushTenant($tenantId);
    }

    private function resolveTenantId(Model $model): ?int
    {
        if ($model instanceof Lead || $model instanceof LeadImport) {
            return $model->tenant_id !== null ? (int) $model->tenant_id : null;
        }

        if ($model instanceof AutomationRun) {
            // The automation relation is usually already loaded by the
            // code that called save(); if not, this fires one extra
            // SELECT - cheap and fires only on automation lifecycle
            // events (which already do real work).
            $tid = $model->automation?->tenant_id;
            return $tid !== null ? (int) $tid : null;
        }

        return null;
    }
}
