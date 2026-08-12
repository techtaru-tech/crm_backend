<?php

declare(strict_types=1);

namespace App\Listeners\Lead;

use App\Events\LeadAssigned;
use App\Notifications\LeadAssignedNotification;

/**
 * Sends a database+email notification to the user a lead is assigned to.
 *
 * Pre-M-A4 this lived inline in LeadObserver — both at lead-creation
 * time (when assigned_user_id was set on insert) and at lead-update
 * time (when the assignment column changed).  Splitting it out lets:
 *   - the observer focus on Eloquent-lifecycle bookkeeping (audit
 *     log, score recompute, activity feed)
 *   - other consumers (e.g. webhooks, custom integrations) attach to
 *     the same domain event without modifying the observer
 *
 * The LeadAssigned event already implements ShouldBroadcast, so
 * dispatching via event() also fires the realtime socket payload.
 */
class NotifyLeadAssignee
{
    public function handle(LeadAssigned $event): void
    {
        if (! $event->assignedTo) {
            return;
        }

        $event->assignedTo->notify(
            new LeadAssignedNotification($event->lead, $event->assignedBy),
        );
    }
}
