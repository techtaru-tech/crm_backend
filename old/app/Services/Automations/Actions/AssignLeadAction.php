<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\User;

class AssignLeadAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $mode = $config['mode'] ?? 'specific';

        if ($mode === 'specific') {
            $userId = (int) ($config['user_id'] ?? 0);
            // Only ever assign a user that belongs to THIS lead's tenant. The
            // step config can arrive from the visual Flow Builder POST, which
            // is not constrained to the tenant-scoped Filament dropdowns, so a
            // crafted request must never assign a foreign tenant's user.
            if ($userId <= 0 || ! $this->belongsToTenant($userId, $lead)) {
                return false;
            }
            $lead->update(['assigned_user_id' => $userId]);
            return true;
        }

        if ($mode === 'round_robin') {
            $userIds = $this->tenantUserIds((array) ($config['user_ids'] ?? []), $lead);
            if (empty($userIds)) {
                return false;
            }

            $lastId  = Lead::where('tenant_id', $lead->tenant_id)
                ->whereIn('assigned_user_id', $userIds)
                ->latest('updated_at')
                ->value('assigned_user_id');
            $lastIdx = $lastId !== null ? array_search((int) $lastId, $userIds, true) : false;
            $idx     = $lastIdx !== false ? ($lastIdx + 1) % count($userIds) : 0;

            $lead->update(['assigned_user_id' => $userIds[$idx]]);
            return true;
        }

        return false;
    }

    /** A user id only counts if it belongs to the lead's tenant. */
    private function belongsToTenant(int $userId, Lead $lead): bool
    {
        return User::query()
            ->where('tenant_id', $lead->tenant_id)
            ->whereKey($userId)
            ->exists();
    }

    /**
     * The subset of the given ids that belong to the lead's tenant, in the
     * tenant's natural order (drops foreign / unknown ids).
     *
     * @param  array<int|string>  $userIds
     * @return list<int>
     */
    private function tenantUserIds(array $userIds, Lead $lead): array
    {
        $userIds = array_filter(array_map('intval', $userIds));
        if (empty($userIds)) {
            return [];
        }

        return User::query()
            ->where('tenant_id', $lead->tenant_id)
            ->whereIn('id', $userIds)
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }
}
