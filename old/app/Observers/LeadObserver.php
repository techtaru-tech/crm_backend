<?php

namespace App\Observers;

use App\Events\LeadAssigned;
use App\Events\LeadReceived;
use App\Events\LeadStageChanged;
use App\Jobs\DispatchOutboundWebhook;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\LeadReceivedNotification;
use App\Notifications\LeadStageChangedNotification;
use App\Services\ActivationTracker;
use App\Services\Automations\AutomationDispatcher;
use App\Services\IntegrationDispatcher;
use App\Services\LeadActivityService;
use App\Services\LeadScoringService;

class LeadObserver
{
    private array $scoreFields = [
        'email', 'phone', 'source', 'status', 'first_name', 'last_name',
    ];

    public function __construct(
        private LeadScoringService $scoring,
        private LeadActivityService $activity,
        private AutomationDispatcher $automations,
        private IntegrationDispatcher $integrations,
    ) {}

    public function created(Lead $lead): void
    {
        // M-A4: AuditLog write moved to LeadAuditObserver; outbound
        // webhook fan-out moved to LeadWebhookObserver.  This observer
        // now focuses on scoring, activity-feed bookkeeping, dispatch
        // to automations + integrations, realtime broadcast, and the
        // assignee notification path.
        $score = $this->scoring->scoreLead($lead);
        if ($score !== 0) {
            $lead->updateQuietly(['lead_score' => $score]);
        }
        $this->activity->logCreated($lead);
        $this->automations->dispatch('lead_created', $lead);
        $this->integrations->dispatch('lead_created', $lead);

        // Record activation signal — idempotent, only fires the first
        // lead per tenant.  Sets tenants.activated_at + appends to
        // activation_signals JSON.  Drives the SA dashboard's
        // trial→paid funnel diagnosis.
        try {
            app(ActivationTracker::class)->record($lead->tenant, ActivationTracker::SIGNAL_FIRST_LEAD);
        } catch (\Throwable) {
            // Activation tracking failure must never block lead creation.
        }

        broadcast(new LeadReceived($lead));

        if ($lead->assigned_user_id) {
            // User is NOT BelongsToTenant (a SA user spans tenants),
            // so the explicit `where('tenant_id', $lead->tenant_id)`
            // is the actual cross-tenant guard — without it a stray
            // assigned_user_id pointing to another tenant's user would
            // notify the wrong workspace.
            $assignee = User::where('tenant_id', $lead->tenant_id)
                ->whereKey($lead->assigned_user_id)
                ->first();
            if ($assignee) {
                $assignee->notify(new LeadReceivedNotification($lead));

                // M-A4: assignee notification fires through the
                // LeadAssigned event so NotifyLeadAssignee listener
                // handles it (and the realtime broadcast lands via
                // ShouldBroadcast on the same event).  Removing the
                // inline notify here keeps the "who gets notified"
                // policy in one place.
                event(new LeadAssigned($lead, $assignee, null));
            }
        }
    }

    public function updated(Lead $lead): void
    {
        // M-A4: AuditLog + outbound webhook fan-out moved to
        // LeadAuditObserver / LeadWebhookObserver.
        $this->integrations->dispatch('lead_updated', $lead);

        // When a lead becomes "won"/"converted" (either status flips or
        // won_at is populated), auto-unenroll from any active sequence
        // whose stop_on_won=true.
        // H7: post-migration there is no more 'converted' value in
        // leads.status — the normalisation migration coerced every
        // legacy 'converted' row to 'won', and the Filament form
        // options no longer offer 'converted'.  Keep the
        // statusBecameConverted variable wired through for downstream
        // branches that read it, but treat it as never-true going
        // forward.
        $statusBecameWon       = $lead->wasChanged('status') && $lead->status === \App\Enums\LeadStatus::Won;
        $statusBecameConverted = false;
        $wonAtBecameSet        = $lead->wasChanged('won_at')  && $lead->won_at !== null;

        if ($statusBecameWon || $statusBecameConverted || $wonAtBecameSet) {
            // i18n: persist the translated reason so the value stored
            // in email_sequence_enrollments.unenroll_reason matches the
            // active locale at write time.  Matches the
            // unenroll_reason_manual convention established in .
            $reason = $statusBecameConverted
                ? __('filament/leads.unenroll_reason_converted')
                : __('filament/leads.unenroll_reason_won');

            \App\Models\EmailSequenceEnrollment::withoutGlobalScope('tenant')
                ->where('lead_id', $lead->id)
                ->where('status', 'active')
                ->whereHas('sequence', fn($q) => $q->where('stop_on_won', true))
                ->get()
                ->each(fn($e) => $e->update([
                    'status'          => 'unenrolled',
                    'unenroll_reason' => $reason,
                    'completed_at'    => now(),
                ]));
        }
    }

    // M-A4: deleted() entirely moved to LeadAuditObserver +
    // LeadWebhookObserver — nothing else listens for the deleted
    // hook here, so this observer no longer overrides it.

    public function updating(Lead $lead): void
    {
        if ($lead->isDirty('status') && $lead->getOriginal('status')) {
            $this->activity->logStatusChanged(
                $lead,
                $lead->getOriginal('status'),
                $lead->status
            );
        }

        if ($lead->isDirty('pipeline_stage_id')) {
            $oldStageId = $lead->getOriginal('pipeline_stage_id');
            $newStageId = $lead->pipeline_stage_id;
            // PipelineStage carries BelongsToTenant, so the global
            // scope already filters; the explicit tenant_id constraint
            // is defense-in-depth for any future code path that drops
            // the scope (withoutGlobalScope, queue context drift).
            $oldStage   = $oldStageId
                ? PipelineStage::where('tenant_id', $lead->tenant_id)->whereKey($oldStageId)->first()
                : null;
            $newStage   = $newStageId
                ? PipelineStage::where('tenant_id', $lead->tenant_id)->whereKey($newStageId)->first()
                : null;
            $movedBy    = auth()->user() instanceof User ? auth()->user() : null;

            // i18n: the placeholder flows into LeadActivity.metadata as
            // i18n_params['from' / 'to'] and renders verbatim in the
            // activity timeline; translate at write time so non-English
            // locales don't render the literal 'None'.
            $this->activity->logStageMoved(
                $lead,
                $oldStage?->name ?? __('filament/leads.stage_none_placeholder'),
                $newStage?->name ?? __('filament/leads.stage_none_placeholder'),
            );
            $lead->stage_entered_at = now();
            $this->automations->dispatch('lead_stage_changed', $lead, [
                'old_stage_id' => $oldStageId,
                'new_stage_id' => $newStageId,
            ]);

            broadcast(new LeadStageChanged($lead, $oldStage, $newStage, $movedBy))->toOthers();

            $lead->loadMissing('tags');
            DispatchOutboundWebhook::fireForEvent($lead->tenant_id, 'lead.stage_changed', array_merge($lead->toArray(), [
                'tags'           => $lead->tags->pluck('name')->toArray(),
                'old_stage_id'   => $oldStageId,
                'old_stage_name' => $oldStage?->name,
                'new_stage_id'   => $newStageId,
                'new_stage_name' => $newStage?->name,
            ]));

            if ($lead->assigned_user_id) {
                // Tenant-scoped User lookup — see comment in created().
                $assignee = User::where('tenant_id', $lead->tenant_id)
                    ->whereKey($lead->assigned_user_id)
                    ->first();
                if ($assignee) {
                    $assignee->notify(new LeadStageChangedNotification($lead, $oldStage, $newStage));
                }
            }
        }

        if ($lead->isDirty('assigned_user_id')) {
            $newUserId  = $lead->assigned_user_id;
            // Tenant-scoped User lookup — see comment in created().
            $newUser    = $newUserId
                ? User::where('tenant_id', $lead->tenant_id)->whereKey($newUserId)->first()
                : null;
            $assignedBy = auth()->user() instanceof User ? auth()->user() : null;

            // i18n: same pattern as logStageMoved — placeholder is
            // persisted to LeadActivity.metadata and rendered later.
            $this->activity->logAssigned($lead, $newUser?->name ?? __('filament/leads.unassigned_placeholder'));
            $this->automations->dispatch('lead_assigned', $lead);

            if ($newUser) {
                // M-A4: NotifyLeadAssignee listener fires the assignee
                // notification; ShouldBroadcast on the event handles
                // the realtime payload.  Note: this loses the
                // ->toOthers() socket-skip that broadcast() had — the
                // assigning user might receive a duplicate socket
                // event.  Acceptable trade-off for one-place policy;
                // re-add a Broadcast::toOthers() listener if echo
                // becomes user-visible noise.
                event(new LeadAssigned($lead, $newUser, $assignedBy));
            }
        }

        $needsRescore = collect($this->scoreFields)->some(fn($f) => $lead->isDirty($f));
        if ($needsRescore) {
            $oldScore = $lead->lead_score;
            $newScore = $this->scoring->scoreLead($lead);
            if ($oldScore !== $newScore) {
                $lead->lead_score = $newScore;
                $this->activity->logScoreChanged($lead, $oldScore, $newScore);
                $this->automations->dispatch('lead_score_threshold', $lead, [
                    'old_score' => $oldScore,
                ]);
            }
        }
    }
}
