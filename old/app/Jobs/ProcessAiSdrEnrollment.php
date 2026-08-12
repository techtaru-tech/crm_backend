<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AiSdrAgent;
use App\Models\AiSdrEnrollment;
use App\Models\AiSdrMessage;
use App\Models\Lead;
use App\Services\Ai\AiSdrDecision;
use App\Services\Ai\AiSdrDecisionService;
use App\Services\LeadActivityService;
use App\Services\LeadNextActionService;
use App\Support\DemoMode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Advances ONE AI SDR enrollment by at most ONE action.
 *
 * Dispatched per due enrollment by ProcessAiSdrEnrollments (the cron
 * runner). Asks AiSdrDecisionService for a single decision, enforces
 * every guardrail IN CODE (not just the prompt), performs at most one
 * side effect (send email / qualify→handoff / opt-out / stop / wait), and
 * audits the decision to ai_sdr_messages.
 *
 * Guardrails enforced here:
 *   - DEMO_MODE → no-op (never send real mail from the public demo)
 *   - global lead opt-out / STOP keyword → opt out + block, never send
 *   - max_touches hard ceiling → complete, never exceed
 *   - business-hours window → defer (re-arm next_action_at), don't send
 *   - human-reply pause is handled elsewhere (LeadEmail::booted +
 *     PauseAiSdrOnHumanReply); this job re-checks state defensively
 *   - AI errors fail closed to `wait` (in the service)
 */
class ProcessAiSdrEnrollment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 120;

    /** STOP / UNSUBSCRIBE-style keywords that trigger a global opt-out. */
    private const OPT_OUT_KEYWORDS = ['stop', 'unsubscribe', 'remove me', 'opt out', 'opt-out'];

    public function __construct(
        public readonly int $enrollmentId,
        // Read by AppServiceProvider's Queue::before hook (reflection on
        // `tenantId`) to bind current_tenant for the worker run so
        // BelongsToTenant queries inside handle() resolve correctly.
        public readonly ?int $tenantId = null,
    ) {
        $this->onQueue('ai-sdr');
    }

    public function handle(AiSdrDecisionService $decisionService): void
    {
        // Public demo never sends real outreach.
        if (DemoMode::isOn()) {
            return;
        }

        // ── Atomic claim ──────────────────────────────────────────────
        // Exactly one worker may process a given due enrollment. Nulling
        // next_action_at conditionally (still LIVE + still due) means a
        // concurrent tick or a second queue worker gets 0 affected rows and
        // bails here — closing the duplicate-send race. Every branch below
        // either re-arms next_action_at or moves the row to a terminal state,
        // so the claim never strands an enrollment.
        $claimed = AiSdrEnrollment::withoutGlobalScope('tenant')
            ->whereKey($this->enrollmentId)
            ->whereIn('state', AiSdrEnrollment::LIVE_STATES)
            ->whereNotNull('next_action_at')
            ->where('next_action_at', '<=', now())
            ->update(['next_action_at' => null]);

        if ($claimed === 0) {
            return; // lost the race, no longer due, or no longer live
        }

        $enrollment = AiSdrEnrollment::withoutGlobalScope('tenant')->find($this->enrollmentId);
        if (! $enrollment) {
            return;
        }

        $agent = AiSdrAgent::withoutGlobalScope('tenant')->find($enrollment->agent_id);
        $lead  = Lead::withoutGlobalScope('tenant')->find($enrollment->lead_id);

        if (! $agent || ! $lead) {
            $this->complete($enrollment, 'missing_agent_or_lead');
            return;
        }

        if (! $agent->active) {
            // Agent paused by admin — re-arm so it polls and resumes once
            // the agent is reactivated. (The atomic claim above nulled
            // next_action_at, so without this the enrollment would strand.)
            $enrollment->update(['next_action_at' => $this->nextTouchAt($agent)]);
            return;
        }

        // ── Guardrail: global opt-out (set previously or just now) ──
        if ($lead->ai_sdr_opted_out_at !== null) {
            $this->optOut($enrollment, $lead, (string) ($lead->ai_sdr_opt_out_reason ?? 'opted_out'), alreadyFlagged: true);
            return;
        }

        // ── Guardrail: STOP / UNSUBSCRIBE keyword in the latest inbound ──
        if ($this->latestInboundIsOptOut($lead)) {
            $this->optOut($enrollment, $lead, 'stop_keyword');
            return;
        }

        // ── Guardrail: the lead must be reachable on the agent's channel ──
        // (email needs an address; SMS/WhatsApp need a phone). Unreachable →
        // hand to a human rather than silently dropping the lead.
        if (! $agent->canReach($lead)) {
            $this->qualifyAndHandoff($enrollment, $agent, $lead, AiSdrDecision::wait('unreachable'), 'lead_unreachable');
            return;
        }

        // ── Guardrail: max touches hard ceiling ──
        if ($enrollment->touches_sent >= $agent->max_touches) {
            // Exhausted cadence with no reply → qualify for human review
            // so the lead isn't silently dropped.
            $this->qualifyAndHandoff($enrollment, $agent, $lead, AiSdrDecision::wait('max_touches'), 'max_touches_reached');
            return;
        }

        // ── Guardrail: business hours ──
        if ($agent->respect_business_hours && ! $this->isWithinBusinessHours($lead)) {
            // Defer to the next business-hours check; do NOT send.
            $enrollment->update([
                'next_action_at' => now()->addHour(),
            ]);
            return;
        }

        // ── Guardrail: per-tenant daily AI spend ceiling ──
        // Protects the platform owner's (super-admin) OpenAI key from one
        // tenant mass-enrolling and running up unbounded cost. Count the
        // tenant's audited decisions so far today; at/over the cap defer a
        // full day WITHOUT calling the AI (no spend, no send). 0 disables.
        $cap = (int) config('leadhub.ai_sdr.tenant_daily_cap', 200);
        if ($cap > 0 && $this->tenantDecisionsToday($lead) >= $cap) {
            $enrollment->update(['next_action_at' => now()->addDay()]);
            return;
        }

        // ── Ask the AI for exactly one decision (fail-closed inside) ──
        $decision = $decisionService->decide($agent, $enrollment, $lead);

        $this->applyDecision($enrollment, $agent, $lead, $decision);
    }

    private function applyDecision(AiSdrEnrollment $enrollment, AiSdrAgent $agent, Lead $lead, AiSdrDecision $decision): void
    {
        switch ($decision->action) {
            case AiSdrMessage::ACTION_SEND:
                $this->sendTouch($enrollment, $agent, $lead, $decision);
                break;

            case AiSdrMessage::ACTION_QUALIFY:
            case AiSdrMessage::ACTION_HANDOFF:
                $this->qualifyAndHandoff($enrollment, $agent, $lead, $decision, 'ai_decision');
                break;

            case AiSdrMessage::ACTION_STOP:
                $this->audit($enrollment, $lead, $decision);
                $this->complete($enrollment, 'ai_stop', $decision->intentScore);
                break;

            case AiSdrMessage::ACTION_WAIT:
            default:
                $this->audit($enrollment, $lead, $decision);
                // Re-arm for a later tick without sending.
                $enrollment->update([
                    'next_action_at' => $this->nextTouchAt($agent),
                    'intent_score'   => $decision->intentScore ?? $enrollment->intent_score,
                ]);
                break;
        }
    }

    private function sendTouch(AiSdrEnrollment $enrollment, AiSdrAgent $agent, Lead $lead, AiSdrDecision $decision): void
    {
        // Route the touch to the agent's channel. The hardened audit + atomic
        // cadence increment below are channel-agnostic and stay identical.
        $this->dispatchTouch($enrollment, $agent, $lead, $decision);

        $this->audit($enrollment, $lead, $decision);

        // Advance the cadence. After a send the agent waits for the lead
        // (or the next due tick) — state stays in the live set so a reply
        // can still pause it, but next_action_at honours the min spacing.
        // Atomic SQL increment (with the other cadence fields in the same
        // UPDATE) so a retry / concurrent path can never lose a count and let
        // max_touches be exceeded. increment() keeps the in-memory value a real
        // int — a raw `col + 1` expression would break the integer cast.
        $enrollment->increment('touches_sent', 1, [
            'state'          => AiSdrEnrollment::STATE_AWAITING_REPLY,
            'last_touch_at'  => now(),
            'next_action_at' => $this->nextTouchAt($agent),
            'intent_score'   => $decision->intentScore ?? $enrollment->intent_score,
        ]);
    }

    /**
     * Dispatch ONE outbound touch over the agent's channel. Email goes through
     * the canonical SendLeadEmail pipeline (branding/threading/tracking) and is
     * flagged ai_sdr so the human-reply pause never mistakes the agent's own
     * send for activity. SMS/WhatsApp go through SendLeadMessage as a SYSTEM
     * send (user_id null) so, likewise, the agent's own message never trips the
     * LeadMessage::created pause hook.
     */
    private function dispatchTouch(AiSdrEnrollment $enrollment, AiSdrAgent $agent, Lead $lead, AiSdrDecision $decision): void
    {
        if ($agent->usesMessagingChannel()) {
            SendLeadMessage::dispatch(
                (int) $lead->id,
                (string) $agent->channel,
                (string) $decision->body,
                null, // userId — system/agent send, must NOT pause the agent
            );

            return;
        }

        SendLeadEmail::dispatch(
            $lead->id,
            (string) $decision->subject,
            (string) $decision->body,
            (string) $lead->email,
            $this->senderId($agent, $lead),
            [],
            (int) $lead->tenant_id,
            true,                 // aiSdr
            (int) $enrollment->id // aiSdrEnrollmentId
        );
    }

    private function qualifyAndHandoff(AiSdrEnrollment $enrollment, AiSdrAgent $agent, Lead $lead, AiSdrDecision $decision, string $reason): void
    {
        $this->audit($enrollment, $lead, $decision);

        // Build a human-readable handoff summary (AI when available,
        // deterministic fallback otherwise — summarizeLead degrades on
        // its own).
        $summary = '';
        try {
            $summary = app(LeadNextActionService::class)->summarizeLead($lead);
        } catch (\Throwable $e) {
            Log::warning('AI SDR handoff summary failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }

        // Assign to the configured rep (if any) so the lead lands in
        // someone's queue.
        if ($agent->assign_to_user_id) {
            $lead->forceFill(['assigned_user_id' => $agent->assign_to_user_id])->save();
        }

        // Leave a note on the lead so the handoff is visible on the
        // timeline everywhere, not just inside the SDR tables.
        try {
            \App\Models\LeadNote::create([
                'tenant_id' => $lead->tenant_id,
                'lead_id'   => $lead->id,
                'user_id'   => $agent->assign_to_user_id,
                'body'      => trim(__('ai_sdr.handoff.note_prefix') . "\n" . $summary),
            ]);
            app(LeadActivityService::class)->logNoteAdded($lead, __('ai_sdr.handoff.activity'));
        } catch (\Throwable $e) {
            Log::warning('AI SDR handoff note failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }

        $enrollment->update([
            'state'           => AiSdrEnrollment::STATE_HANDED_OFF,
            'handoff_summary' => $summary !== '' ? $summary : null,
            'paused_reason'   => $reason,
            'intent_score'    => $decision->intentScore ?? $enrollment->intent_score,
            'next_action_at'  => null,
            'completed_at'    => now(),
        ]);
    }

    /**
     * Global opt-out: flip the enrollment terminal AND stamp the lead so
     * NO agent can ever re-enroll or touch them again.
     */
    private function optOut(AiSdrEnrollment $enrollment, Lead $lead, string $reason, bool $alreadyFlagged = false): void
    {
        if (! $alreadyFlagged) {
            $lead->forceFill([
                'ai_sdr_opted_out_at'   => now(),
                'ai_sdr_opt_out_reason' => $reason,
            ])->save();
        }

        $this->audit($enrollment, $lead, new AiSdrDecision(
            action: AiSdrMessage::ACTION_STOP,
            reason: 'opt_out:' . $reason,
        ));

        $enrollment->update([
            'state'          => AiSdrEnrollment::STATE_OPTED_OUT,
            'paused_reason'  => $reason,
            'next_action_at' => null,
            'completed_at'   => now(),
        ]);
    }

    private function complete(AiSdrEnrollment $enrollment, string $reason, ?int $intentScore = null): void
    {
        $enrollment->update([
            'state'          => AiSdrEnrollment::STATE_COMPLETED,
            'paused_reason'  => $reason,
            'intent_score'   => $intentScore ?? $enrollment->intent_score,
            'next_action_at' => null,
            'completed_at'   => now(),
        ]);
    }

    private function audit(AiSdrEnrollment $enrollment, Lead $lead, AiSdrDecision $decision): void
    {
        AiSdrMessage::create([
            'tenant_id'         => $lead->tenant_id,
            'enrollment_id'     => $enrollment->id,
            'lead_id'           => $lead->id,
            'action'            => $decision->action,
            'reason'            => $decision->reason,
            'intent_score'      => $decision->intentScore,
            'subject'           => $decision->subject,
            'body'              => $decision->body,
            'model'             => $decision->model,
            'prompt_tokens'     => $decision->promptTokens,
            'completion_tokens' => $decision->completionTokens,
            'cost_usd'          => $decision->costUsd,
            'was_fallback'      => $decision->wasFallback,
        ]);
    }

    /**
     * How many AI SDR decisions this tenant has had audited since midnight.
     * Each audited row corresponds to one runner decision on the shared
     * super-admin key, so it's a faithful (slightly conservative) proxy for
     * today's spend — we'd rather defer one extra touch than overspend.
     */
    private function tenantDecisionsToday(Lead $lead): int
    {
        return AiSdrMessage::withoutGlobalScope('tenant')
            ->where('tenant_id', $lead->tenant_id)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    private function nextTouchAt(AiSdrAgent $agent): Carbon
    {
        $hours = max(1, (int) $agent->min_hours_between_touches);
        return now()->addHours($hours);
    }

    private function senderId(AiSdrAgent $agent, Lead $lead): int
    {
        // Prefer the lead's assigned rep, then the agent's handoff rep,
        // then any user in the tenant. The branded sender resolves from
        // the tenant anyway; this is just the "sent by" attribution.
        $id = $lead->assigned_user_id
            ?? $agent->assign_to_user_id
            ?? \App\Models\User::withoutGlobalScope('tenant')
                ->where('tenant_id', $lead->tenant_id)
                ->orderBy('id')
                ->value('id');

        return (int) $id;
    }

    private function latestInboundIsOptOut(Lead $lead): bool
    {
        // Email channel: the latest inbound email's subject + body.
        $email = \App\Models\LeadEmail::withoutGlobalScope('tenant')
            ->where('tenant_id', $lead->tenant_id)
            ->where('lead_id', $lead->id)
            ->where('direction', 'inbound')
            ->latest('created_at')
            ->first(['subject', 'body_text']);

        if ($email && $this->textIsOptOut((string) ($email->body_text ?? '') . ' ' . (string) ($email->subject ?? ''))) {
            return true;
        }

        // Messaging channels (SMS / WhatsApp / etc.): the latest inbound message
        // body. A STOP on ANY channel is a global opt-out — the agent is
        // multichannel, so the opt-out check must be too.
        $message = \App\Models\LeadMessage::withoutGlobalScope('tenant')
            ->where('tenant_id', $lead->tenant_id)
            ->where('lead_id', $lead->id)
            ->where('direction', 'inbound')
            ->latest('created_at')
            ->first(['body']);

        return $message !== null && $this->textIsOptOut((string) ($message->body ?? ''));
    }

    /** True when the text carries a STOP/UNSUBSCRIBE-style opt-out token. */
    private function textIsOptOut(string $text): bool
    {
        $haystack = mb_strtolower(trim($text));
        if ($haystack === '') {
            return false;
        }

        foreach (self::OPT_OUT_KEYWORDS as $keyword) {
            // Word-boundary match so a casual mention ("the bus stops here",
            // "unstoppable") doesn't trip a global opt-out — only a real STOP /
            // UNSUBSCRIBE token does.
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/u', $haystack) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Business-hours window check. Mirrors AutomationDispatcher's logic
     * (tenant business_hours setting, fail-open when not configured) so
     * the AI SDR and automations agree on "open for outreach".
     */
    private function isWithinBusinessHours(Lead $lead): bool
    {
        $tenant = $lead->tenant;
        if (! $tenant) {
            return true;
        }

        $hours = (array) ($tenant->getSetting('business_hours') ?? []);
        $days  = $hours['days'] ?? null;
        if (! is_array($days) || empty($days)) {
            return true;
        }

        $tz = (string) ($hours['timezone'] ?? 'UTC');
        try {
            $now = Carbon::now($tz);
        } catch (\Throwable) {
            $now = Carbon::now();
        }

        $dow = (int) $now->dayOfWeek; // 0 = Sunday
        $day = $days[$dow] ?? null;
        if (! is_array($day) || empty($day['enabled'])) {
            return false;
        }

        $start = (string) ($day['start'] ?? '00:00');
        $end   = (string) ($day['end']   ?? '23:59');
        $nowHm = $now->format('H:i');

        // Normal window (start <= end): inside when start <= now <= end.
        // Overnight window (start > end, e.g. 22:00–06:00): inside when now is
        // at/after start OR at/before end — a plain BETWEEN never matches it.
        return $start <= $end
            ? ($nowHm >= $start && $nowHm <= $end)
            : ($nowHm >= $start || $nowHm <= $end);
    }
}
