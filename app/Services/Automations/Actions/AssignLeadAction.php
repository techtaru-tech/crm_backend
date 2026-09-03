<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\LeadAssignmentHistory;
use App\Models\Team;
use App\Models\User;

class AssignLeadAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $mode = $config['mode'] ?? 'specific';

        // Never take a lead off the person who already owns it.  A
        // lead_created automation runs after the row is written, so a CSV
        // import that mapped an "Assigned To" column, a webhook that carried
        // an owner, or an admin who picked someone on the create form all had
        // their choice silently replaced by the next name in the rotation.
        // A step that genuinely means to reassign says so with overwrite.
        if (! ($config['overwrite'] ?? false) && filled($lead->assigned_user_id)) {
            return true;
        }

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
            $userIds = $this->roundRobinCandidates($config, $lead);
            if (empty($userIds)) {
                return false;
            }

            // Who got the last one, read from the assignment history rather
            // than from leads.updated_at.  updated_at moves whenever anything
            // on the lead changes — a note, a stage, a score recalculation —
            // so the old lookup regularly picked a lead that had merely been
            // touched and handed the next lead straight back to the same rep.
            $lastId = LeadAssignmentHistory::where('tenant_id', $lead->tenant_id)
                ->whereIn('to_user_id', $userIds)
                ->latest('id')
                ->value('to_user_id');

            $lastIdx = $lastId !== null ? array_search((int) $lastId, $userIds, true) : false;
            $idx     = $lastIdx !== false ? ($lastIdx + 1) % count($userIds) : 0;

            $update = ['assigned_user_id' => $userIds[$idx]];

            // Stamp the team too when the step names one.  A member only sees
            // leads on their own assigned_user_id — assigned_team_id alone is
            // visible to the team's MANAGER, not its reps — so a team-only
            // assignment would leave the lead invisible to the person expected
            // to work it.  Setting both keeps the rep's list and the manager's
            // team view in agreement.
            if ($teamId = (int) ($config['team_id'] ?? 0)) {
                if (Team::where('tenant_id', $lead->tenant_id)->whereKey($teamId)->exists()) {
                    $update['assigned_team_id'] = $teamId;
                }
            }

            $lead->update($update);

            return true;
        }

        // Assign to a team rather than a person.  Phase 1 added
        // leads.assigned_team_id and the team-scoped visibility that reads
        // it, but this action only ever set assigned_user_id — so "send new
        // leads to Sales Team A" could not be automated at all.
        if ($mode === 'team') {
            $teamId = (int) ($config['team_id'] ?? 0);

            if ($teamId <= 0 || ! Team::where('tenant_id', $lead->tenant_id)->whereKey($teamId)->exists()) {
                return false;
            }

            $lead->update(['assigned_team_id' => $teamId]);

            return true;
        }

        return false;
    }

    /**
     * Users a round-robin step may hand leads to.
     *
     * An explicit user_ids list wins.  Failing that, fall back to a team's
     * members and then to every user in the tenant, because the shipped
     * "Round-Robin Assignment" automation carries no user_ids at all — enabling
     * it did nothing, silently, with no hint that a list was required.
     *
     * @return list<int>
     */
    private function roundRobinCandidates(array $config, Lead $lead): array
    {
        if ($ids = $this->tenantUserIds((array) ($config['user_ids'] ?? []), $lead)) {
            return $ids;
        }

        if ($teamId = (int) ($config['team_id'] ?? 0)) {
            $team = Team::where('tenant_id', $lead->tenant_id)->find($teamId);

            // A named team is a deliberate boundary.  If it has no members the
            // step fails and says so in the run log — quieter than falling
            // through to "everyone in the workspace", which would start
            // routing that team's leads to admins the moment someone emptied
            // it, with nothing anywhere to explain the change.
            return $team ? $this->tenantUserIds($team->users()->pluck('users.id')->all(), $lead) : [];
        }

        // Prefer the roles that actually carry a lead list.  Handing leads to
        // the workspace owner or a read-only auditor is never what a
        // round-robin step means, and a viewer cannot work the lead at all —
        // every Nth lead would simply park with someone who may not touch it.
        if ($sales = $this->usersWithRoles($lead, (array) config('leadhub.assignable_roles', ['manager', 'member']))) {
            return $sales;
        }

        // Nobody holds a sales role — common in a small workspace where
        // everyone is an admin.  Rotate over them rather than returning an
        // empty list, because an empty list makes the step fail silently,
        // which is the failure this whole fallback exists to avoid.
        return $this->usersWithRoles($lead, null);
    }

    /**
     * Tenant users, optionally restricted to the given roles, always
     * excluding read-only roles and super-admins.
     *
     * @param  array<int, string>|null  $roles  null = any role
     * @return list<int>
     */
    private function usersWithRoles(Lead $lead, ?array $roles): array
    {
        $readOnly = (array) config('leadhub.read_only_roles', ['viewer']);

        return User::query()
            ->where('tenant_id', $lead->tenant_id)
            ->where(fn ($q) => $q->whereNull('is_super_admin')->orWhere('is_super_admin', false))
            ->when($readOnly, fn ($q) => $q->whereDoesntHave('roles', fn ($r) => $r->whereIn('name', $readOnly)))
            ->when($roles, fn ($q) => $q->whereHas('roles', fn ($r) => $r->whereIn('name', $roles)))
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();
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
