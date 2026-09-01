<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\LeadTask;

/**
 * Audit-log writer for follow-ups (spec §13: "Follow-up created/updated").
 *
 * lead_tasks already carries assigned_user_id and timestamps, but nothing
 * recorded WHO changed a follow-up.  When a manager reschedules a rep's
 * site visit or flips one to Missed, the row simply changes and the reason
 * disappears — which is exactly the accountability the funnel needs.
 *
 * Mirrors LeadAuditObserver: separate from the model's own observers so
 * audit policy can change without touching the sync/reminder hot path.
 * Only meaningful fields are logged — `reminder_sent_at` churns on every
 * cron pass and would bury the real edits.
 */
class LeadTaskAuditObserver
{
    /** Columns whose change is worth an audit row. */
    private const TRACKED = ['title', 'description', 'due_at', 'status', 'completed', 'priority', 'assigned_user_id'];

    public function created(LeadTask $task): void
    {
        AuditLog::record(
            'followup.created',
            $task,
            [],
            $task->only(['lead_id', 'title', 'due_at', 'status', 'assigned_user_id']),
            'followup',
        );
    }

    public function updated(LeadTask $task): void
    {
        $changed = array_intersect_key($task->getDirty(), array_flip(self::TRACKED));

        if ($changed === []) {
            return; // reminder bookkeeping only — nothing a human did
        }

        AuditLog::record(
            'followup.updated',
            $task,
            array_intersect_key($task->getOriginal(), $changed),
            $changed,
            'followup',
        );
    }

    public function deleted(LeadTask $task): void
    {
        AuditLog::record(
            'followup.deleted',
            $task,
            $task->only(['lead_id', 'title', 'due_at', 'status']),
            [],
            'followup',
        );
    }
}
