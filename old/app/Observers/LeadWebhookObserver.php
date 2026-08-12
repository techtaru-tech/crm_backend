<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\DispatchOutboundWebhook;
use App\Models\Lead;

/**
 * Outbound-webhook fan-out for Lead lifecycle events (M-A4).
 *
 * Pre-M-A4 the DispatchOutboundWebhook::fireForEvent() calls were
 * inline in LeadObserver across created/updated/deleted (and the
 * stage-change branch in updating()).  Splitting them out:
 *   - keeps the webhook payload shape next to the dispatcher
 *   - lets webhook concerns (rate-limiting, signature scheme,
 *     payload schema versioning) evolve without touching observer
 *     lifecycle code
 *
 * The `stage_changed` outbound webhook still fires from LeadObserver
 * because it needs the old/new stage objects which Eloquent's
 * `updated` event doesn't carry — that branch lives in the parent
 * observer's `updating()` hook for now.
 */
class LeadWebhookObserver
{
    public function created(Lead $lead): void
    {
        $lead->loadMissing('tags');
        DispatchOutboundWebhook::fireForEvent(
            $lead->tenant_id,
            'lead.created',
            array_merge($lead->toArray(), [
                'tags' => $lead->tags->pluck('name')->toArray(),
            ]),
        );
    }

    public function updated(Lead $lead): void
    {
        $lead->loadMissing('tags');
        DispatchOutboundWebhook::fireForEvent(
            $lead->tenant_id,
            'lead.updated',
            array_merge($lead->toArray(), [
                'tags' => $lead->tags->pluck('name')->toArray(),
            ]),
        );
    }

    public function deleted(Lead $lead): void
    {
        DispatchOutboundWebhook::fireForEvent(
            $lead->tenant_id,
            'lead.deleted',
            ['id' => $lead->id, 'email' => $lead->email],
        );
    }
}
