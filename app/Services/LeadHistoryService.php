<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadAssignmentHistory;
use App\Models\LeadStageHistory;
use App\Models\PipelineStage;
use App\Models\User;

/**
 * Writes the first-class stage / assignment history rows (spec §12).
 *
 * Separate from LeadActivityService on purpose: that one produces
 * human-readable timeline sentences, this one produces queryable facts.
 * A timeline entry answers "what happened"; these rows answer "how long did
 * leads sit in Site Visit" and "who owned this lead in June".
 *
 * tenant_id is always taken from the lead rather than the ambient tenant
 * context — these fire from queue workers and webhooks too, where
 * BelongsToTenant has no tenant to stamp and the insert would fail its FK.
 */
class LeadHistoryService
{
    public function recordStageChange(
        Lead $lead,
        ?int $fromStageId,
        ?int $toStageId,
        ?User $actor = null,
    ): ?LeadStageHistory {
        if ($fromStageId === $toStageId) {
            return null;
        }

        return LeadStageHistory::create([
            'tenant_id'       => $lead->tenant_id,
            'lead_id'         => $lead->id,
            'from_stage_id'   => $fromStageId,
            'to_stage_id'     => $toStageId,
            'from_stage_name' => $this->stageName($lead, $fromStageId),
            'to_stage_name'   => $this->stageName($lead, $toStageId),
            'actor_id'        => $this->actorId($actor),
        ]);
    }

    public function recordAssignmentChange(
        Lead $lead,
        ?int $fromUserId,
        ?int $toUserId,
        ?int $fromTeamId,
        ?int $toTeamId,
        ?User $actor = null,
    ): ?LeadAssignmentHistory {
        if ($fromUserId === $toUserId && $fromTeamId === $toTeamId) {
            return null;
        }

        return LeadAssignmentHistory::create([
            'tenant_id'    => $lead->tenant_id,
            'lead_id'      => $lead->id,
            'from_user_id' => $fromUserId,
            'to_user_id'   => $toUserId,
            'from_team_id' => $fromTeamId,
            'to_team_id'   => $toTeamId,
            'actor_id'     => $this->actorId($actor),
        ]);
    }

    /**
     * Snapshot the stage's display name at write time.  Tenant-scoped lookup
     * bypasses the global scope for the same queue-context reason as above,
     * but keeps the explicit tenant_id filter as the actual guard.
     */
    protected function stageName(Lead $lead, ?int $stageId): ?string
    {
        if (! $stageId) {
            return null;
        }

        return PipelineStage::withoutGlobalScopes()
            ->where('tenant_id', $lead->tenant_id)
            ->whereKey($stageId)
            ->value('name');
    }

    protected function actorId(?User $actor): ?int
    {
        if ($actor) {
            return $actor->id;
        }

        return auth()->user() instanceof User ? auth()->id() : null;
    }
}
