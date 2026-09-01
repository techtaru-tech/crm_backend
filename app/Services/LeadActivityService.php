<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadActivity;

class LeadActivityService
{
    public function log(
        Lead $lead,
        string $type,
        ?string $description = null,
        array $metadata = [],
        ?int $userId = null
    ): LeadActivity {
        return LeadActivity::create([
            'lead_id'     => $lead->id,
            'tenant_id'   => $lead->tenant_id,
            'user_id'     => $userId ?? auth()->id(),
            'type'        => $type,
            'description' => $description,
            'metadata'    => $metadata ?: null,
        ]);
    }

    public function logCreated(Lead $lead): void
    {
        $source = (string) ($lead->source_label ?? '');
        $this->log($lead, 'created', 'Lead created from ' . $source, [
            'i18n_key'    => 'lead_activities.lead_created',
            'i18n_params' => ['source' => $source],
        ]);
    }

    /**
     * The Lead model casts `status` to LeadStatus, so both the observer's
     * getOriginal('status') and $lead->status arrive as enum cases.  Accept
     * either shape: raw values still reach here from importers and legacy
     * rows written before the H7 cast landed.
     */
    public function logStatusChanged(Lead $lead, LeadStatus|string $oldStatus, LeadStatus|string $newStatus): void
    {
        $oldCase = $oldStatus instanceof LeadStatus ? $oldStatus : LeadStatus::tryFrom($oldStatus);
        $newCase = $newStatus instanceof LeadStatus ? $newStatus : LeadStatus::tryFrom($newStatus);

        // metadata.old/new stay machine-readable enum values for reporting;
        // the description and i18n_params carry the human label, matching
        // the write-time translation convention used by logStageMoved().
        $oldValue = $oldCase?->value ?? (string) $oldStatus;
        $newValue = $newCase?->value ?? (string) $newStatus;
        $oldLabel = $oldCase?->label() ?? $oldValue;
        $newLabel = $newCase?->label() ?? $newValue;

        $this->log($lead, 'status_changed', "Status changed from {$oldLabel} to {$newLabel}", [
            'old'         => $oldValue,
            'new'         => $newValue,
            'i18n_key'    => 'lead_activities.status_changed',
            'i18n_params' => ['from' => $oldLabel, 'to' => $newLabel],
        ]);
    }

    public function logStageMoved(Lead $lead, ?string $oldStage, ?string $newStage): void
    {
        $this->log($lead, 'stage_moved', "Moved from {$oldStage} to {$newStage}", [
            'old_stage'   => $oldStage,
            'new_stage'   => $newStage,
            'i18n_key'    => 'lead_activities.stage_moved',
            'i18n_params' => ['from' => (string) $oldStage, 'to' => (string) $newStage],
        ]);
    }

    public function logEmailSent(Lead $lead, string $subject, string $recipient, ?int $userId = null): void
    {
        // Pass userId through to log() so callers from queue context
        // (where there is no auth()->id()) can attribute the activity
        // to the original sender without resorting to loginUsingId().
        $this->log($lead, 'email_sent', "Email sent: {$subject}", [
            'subject'     => $subject,
            'recipient'   => $recipient,
            'i18n_key'    => 'lead_activities.email_sent',
            'i18n_params' => ['subject' => $subject],
        ], $userId);
    }

    /**
     * Log an INBOUND email reply on the lead's timeline. Called from the IMAP
     * connector when a reply lands, so the reply shows in the Activity Timeline
     * and flips the lead's "Waiting on" indicator to us (the Lead model derives
     * waiting_on from the latest email_received / email_sent activity). userId
     * is null — inbound replies have no acting user (queue/cron context).
     */
    public function logEmailReceived(Lead $lead, string $subject, string $fromAddress): void
    {
        $this->log($lead, 'email_received', "Email received: {$subject}", [
            'subject'     => $subject,
            'from'        => $fromAddress,
            'i18n_key'    => 'lead_activities.email_received',
            'i18n_params' => ['subject' => $subject],
        ], null);
    }

    public function logCallLogged(Lead $lead, string $direction, int $duration, string $outcome, ?string $notes): void
    {
        $this->log($lead, 'call_logged', "Call logged ({$direction}, {$duration}min, {$outcome})", [
            'direction'   => $direction,
            'duration'    => $duration,
            'outcome'     => $outcome,
            'notes'       => $notes,
            'i18n_key'    => 'lead_activities.call_logged',
            'i18n_params' => [
                'direction' => $direction,
                'duration'  => $duration,
                'outcome'   => $outcome,
            ],
        ]);
    }

    /**
     * Meeting booked / rescheduled / cancelled (spec §10).
     *
     * Meetings live in their own module (meeting_bookings) and were invisible
     * on the lead timeline, so a rep reading a lead's history saw calls and
     * notes but no sign that a site visit had been booked.  Writing an
     * activity row keeps the timeline the single place to read a lead's story.
     */
    public function logMeeting(Lead $lead, string $event, string $meetingLabel, ?string $when = null, ?int $userId = null): void
    {
        $key = 'lead_activities.meeting_' . $event;

        $this->log($lead, 'meeting_' . $event, trim("Meeting {$event}: {$meetingLabel} {$when}"), [
            'i18n_key'    => $key,
            'i18n_params' => ['meeting' => $meetingLabel, 'when' => (string) $when],
        ], $userId);
    }

    public function logNoteAdded(Lead $lead, string $noteBody): void
    {
        $this->log($lead, 'note_added', 'Internal note added', [
            'i18n_key'    => 'lead_activities.note_added',
            'i18n_params' => [],
        ]);
    }

    public function logTagApplied(Lead $lead, string $tagName): void
    {
        $this->log($lead, 'tag_applied', "Tag applied: {$tagName}", [
            'tag'         => $tagName,
            'i18n_key'    => 'lead_activities.tag_applied',
            'i18n_params' => ['tag' => $tagName],
        ]);
    }

    public function logTagRemoved(Lead $lead, string $tagName): void
    {
        $this->log($lead, 'tag_removed', "Tag removed: {$tagName}", [
            'tag'         => $tagName,
            'i18n_key'    => 'lead_activities.tag_removed',
            'i18n_params' => ['tag' => $tagName],
        ]);
    }

    public function logAssigned(Lead $lead, ?string $toUser): void
    {
        $this->log($lead, 'assigned', "Assigned to {$toUser}", [
            'i18n_key'    => 'lead_activities.assigned',
            'i18n_params' => ['to' => (string) $toUser],
        ]);
    }

    public function logScoreChanged(Lead $lead, int $oldScore, int $newScore): void
    {
        $this->log($lead, 'score_changed', "Score changed from {$oldScore} to {$newScore}", [
            'old'         => $oldScore,
            'new'         => $newScore,
            'i18n_key'    => 'lead_activities.score_changed',
            'i18n_params' => ['from' => $oldScore, 'to' => $newScore],
        ]);
    }

    public function logImported(Lead $lead, string $filename): void
    {
        $this->log($lead, 'imported', "Imported from file: {$filename}", [
            'filename'    => $filename,
            'i18n_key'    => 'lead_activities.imported',
            'i18n_params' => ['filename' => $filename],
        ]);
    }
}
