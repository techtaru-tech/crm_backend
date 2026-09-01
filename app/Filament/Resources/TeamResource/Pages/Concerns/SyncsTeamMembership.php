<?php

namespace App\Filament\Resources\TeamResource\Pages\Concerns;

use App\Models\Team;

/**
 * `members` and `managers` are two views of one pivot table, not two
 * relations, so Filament cannot save them itself.
 *
 * The form presents them as separate multi-selects because that is how
 * people think about a team; this trait folds them back into a single
 * team_user sync where `is_manager` is the only difference between a row
 * in one list and a row in the other.  Naming someone a manager implies
 * membership, so managers are unioned into the member set rather than
 * being a separate population.
 */
trait SyncsTeamMembership
{
    /** Pull the pseudo-fields out of the payload before the model is written. */
    protected function extractMembership(array $data): array
    {
        $this->membershipData = [
            'members'  => array_map('intval', $data['members'] ?? []),
            'managers' => array_map('intval', $data['managers'] ?? []),
        ];

        unset($data['members'], $data['managers']);

        return $data;
    }

    protected function syncMembership(Team $team): void
    {
        $managers = $this->membershipData['managers'] ?? [];
        $members  = $this->membershipData['members'] ?? [];

        $sync = [];
        foreach (array_unique([...$members, ...$managers]) as $userId) {
            $sync[$userId] = [
                'tenant_id'  => $team->tenant_id,
                'is_manager' => in_array($userId, $managers, true),
            ];
        }

        $team->users()->sync($sync);
    }
}
