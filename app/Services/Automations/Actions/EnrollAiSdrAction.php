<?php

declare(strict_types=1);

namespace App\Services\Automations\Actions;

use App\Models\AiSdrAgent;
use App\Models\AiSdrEnrollment;
use App\Models\AutomationRun;
use App\Models\Lead;

/**
 * Automation action: enroll a lead into an AI SDR agent.
 *
 * Wiring this as an action means ANY trigger (lead_created, tag_added,
 * score_threshold, form_submitted, …) can start autonomous follow-up.
 *
 * Guardrails honoured here so the automation path can never bypass them:
 *   - lead must be reachable on the agent's channel (email / SMS / WhatsApp)
 *   - lead must NOT be globally opted out of AI SDR
 *   - never double-enroll the same lead into the same agent
 *   - agent must exist, be active, and belong to the lead's tenant
 *
 * config:
 *   agent_id  (optional) — specific agent; falls back to the tenant's
 *                          first active agent when omitted.
 */
class EnrollAiSdrAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        // Respect the global opt-out flag regardless of channel.
        if ($lead->ai_sdr_opted_out_at !== null) {
            return false;
        }

        $agent = $this->resolveAgent($lead, $config);
        if (! $agent) {
            return false;
        }

        // The lead must be reachable on the agent's channel (email needs an
        // address; SMS/WhatsApp need a phone) — otherwise enrolling just burns
        // a cron tick before the runner hands it straight off.
        if (! $agent->canReach($lead)) {
            return false;
        }

        // Idempotent: the unique (agent_id, lead_id) index also enforces
        // this at the DB level; checking first keeps the run log clean.
        $exists = AiSdrEnrollment::withoutGlobalScope('tenant')
            ->where('tenant_id', $lead->tenant_id)
            ->where('agent_id', $agent->id)
            ->where('lead_id', $lead->id)
            ->exists();
        if ($exists) {
            return false;
        }

        AiSdrEnrollment::create([
            'tenant_id'      => $lead->tenant_id,
            'agent_id'       => $agent->id,
            'lead_id'        => $lead->id,
            'state'          => AiSdrEnrollment::STATE_ACTIVE,
            'touches_sent'   => 0,
            'enrolled_at'    => now(),
            // Due immediately — the next cron tick makes the first decision.
            'next_action_at' => now(),
        ]);

        return true;
    }

    private function resolveAgent(Lead $lead, array $config): ?AiSdrAgent
    {
        $query = AiSdrAgent::withoutGlobalScope('tenant')
            ->where('tenant_id', $lead->tenant_id)
            ->where('active', true);

        if (! empty($config['agent_id'])) {
            return (clone $query)->whereKey($config['agent_id'])->first();
        }

        return $query->orderBy('id')->first();
    }
}
