<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NewLeadMessage;
use App\Models\AiSdrEnrollment;
use App\Models\LeadMessage;
use Illuminate\Support\Facades\Log;

/**
 * Hard-pauses every live AI SDR enrollment for a lead the instant a HUMAN
 * touches the conversation through the messaging channel.
 *
 * NewLeadMessage fires for:
 *   - inbound messages (the LEAD replied by SMS/WhatsApp/etc.) → pause
 *   - outbound messages a REP sent from the Conversations panel
 *     (user_id is set) → pause
 *   - outbound messages sent by automations/system (user_id is null) →
 *     do NOT pause
 *
 * The agent itself is EMAIL-ONLY and never creates LeadMessage rows, so it
 * can never pause itself here. The email side of the same guarantee lives
 * in LeadEmail::booted() (inbound email → pause), which likewise ignores
 * the agent's own outbound sends because only inbound rows trigger it.
 *
 * Not queued: this is a tiny indexed UPDATE and must win any race against
 * the cron runner, so we run it synchronously in-process.
 */
class PauseAiSdrOnHumanReply
{
    public function handle(NewLeadMessage $event): void
    {
        self::pauseForMessage($event->message);
    }

    /**
     * Pause every live AI SDR enrollment for the lead when a HUMAN touches the
     * conversation. Shared by the NewLeadMessage listener AND the
     * LeadMessage::created model hook, so ANY inbound (lead) or rep-authored
     * (outbound + user_id) message pauses the agent — even a channel connector
     * that writes the row without dispatching NewLeadMessage. Idempotent: once
     * paused the row leaves LIVE_STATES, so the second call (the event firing
     * after the model hook on the normal path) updates 0 rows.
     */
    public static function pauseForMessage(LeadMessage $message): void
    {
        $isHuman = $message->direction === 'inbound'
            || ($message->direction === 'outbound' && $message->user_id !== null);

        if (! $isHuman) {
            return;
        }

        try {
            AiSdrEnrollment::withoutGlobalScope('tenant')
                ->where('tenant_id', $message->tenant_id)
                ->where('lead_id', $message->lead_id)
                ->whereIn('state', AiSdrEnrollment::LIVE_STATES)
                ->update([
                    'state'          => AiSdrEnrollment::STATE_PAUSED_HUMAN,
                    'paused_reason'  => $message->direction === 'inbound'
                        ? 'lead_replied_message'
                        : 'rep_replied_message',
                    'next_action_at' => null,
                ]);
        } catch (\Throwable $e) {
            // Never let a pause failure break message delivery / broadcast.
            Log::warning('PauseAiSdrOnHumanReply failed', [
                'lead_id' => $message->lead_id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
