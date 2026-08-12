<?php

namespace App\Jobs;

use App\Models\EmailSequenceEnrollment;
use App\Models\EmailSequenceStep;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessEmailSequences implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Scheduler runs this every 15 minutes. Only pick up active enrollments
        // whose next_send_at is due. withoutGlobalScope('tenant') because the
        // job runs without an authenticated tenant context.
        $enrollments = EmailSequenceEnrollment::withoutGlobalScope('tenant')
            ->where('status', 'active')
            ->whereNotNull('next_send_at')
            ->where('next_send_at', '<=', now())
            ->with(['sequence.steps', 'lead'])
            ->limit(500)
            ->get();

        // Pre-resolve fallback senders per tenant in a single query.
        // Without this, every enrollment whose lead.assigned_user_id
        // is null fires its own `User::where(...)->value('id')` lookup —
        // at the 500-row batch limit that's up to 500 extra queries
        // per cron tick.
        $tenantIds = $enrollments
            ->pluck('lead.tenant_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $fallbackSenders = $this->resolveFallbackSenders($tenantIds);

        foreach ($enrollments as $enrollment) {
            try {
                $this->processEnrollment($enrollment, $fallbackSenders);
            } catch (\Throwable $e) {
                Log::error('ProcessEmailSequences enrollment failed', [
                    'enrollment_id' => $enrollment->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Build a [tenant_id => first_user_id] map for fallback sender
     * resolution.  One query covers every tenant in the current batch.
     *
     * @param  array<int, int>  $tenantIds
     * @return array<int, int>
     */
    private function resolveFallbackSenders(array $tenantIds): array
    {
        if ($tenantIds === []) {
            return [];
        }

        return \App\Models\User::query()
            ->whereIn('tenant_id', $tenantIds)
            ->orderBy('id')
            ->get(['id', 'tenant_id'])
            ->groupBy('tenant_id')
            ->map(fn ($group) => (int) $group->first()->id)
            ->all();
    }

    /**
     * @param  array<int, int>  $fallbackSenders  tenant_id => user_id
     */
    protected function processEnrollment(EmailSequenceEnrollment $enrollment, array $fallbackSenders = []): void
    {
        $sequence = $enrollment->sequence;
        $lead     = $enrollment->lead;

        if (! $sequence || ! $lead) {
            // i18n: translate at write time so the persisted reason
            // matches the active locale.  See convention.
            $enrollment->update([
                'status'          => 'unenrolled',
                'unenroll_reason' => __('filament/leads.unenroll_reason_missing_data'),
                'completed_at'    => now(),
            ]);
            return;
        }

        if ($sequence->status !== 'active') {
            // Paused or draft — leave it alone and try again later.
            return;
        }

        $steps = $sequence->steps->values();
        $step  = $steps->get($enrollment->current_step);

        if (! $step) {
            // No more steps → completed.
            $enrollment->update([
                'status'       => 'completed',
                'completed_at' => now(),
                'next_send_at' => null,
            ]);
            return;
        }

        // Skip sending if the lead has no email.
        if (! $lead->email) {
            // i18n: translate at write time so the persisted reason
            // matches the active locale.  See convention.
            $enrollment->update([
                'status'          => 'unenrolled',
                'unenroll_reason' => __('filament/leads.unenroll_reason_no_email'),
                'completed_at'    => now(),
            ]);
            return;
        }

        $subject = $this->substitute($step->subject, $lead);
        $body    = $this->substitute($step->body_html, $lead);

        // Use the lead's assigned user as sender; fall back to the
        // pre-resolved tenant fallback (built once per cron tick in
        // handle()) when nobody is assigned.
        $senderId = $lead->assigned_user_id
            ?? ($fallbackSenders[$lead->tenant_id] ?? null);

        if ($senderId) {
            SendLeadEmail::dispatch(
                $lead->id,
                $subject,
                $body,
                $lead->email,
                (int) $senderId,
                [],
                (int) $lead->tenant_id,
            );
        } else {
            Log::warning('ProcessEmailSequences could not find a sender for enrollment', [
                'enrollment_id' => $enrollment->id,
            ]);
        }

        // Advance the cursor.
        $nextIndex = $enrollment->current_step + 1;
        $nextStep  = $steps->get($nextIndex);

        if (! $nextStep) {
            $enrollment->update([
                'current_step' => $nextIndex,
                'status'       => 'completed',
                'completed_at' => now(),
                'next_send_at' => null,
            ]);
            return;
        }

        $nextSendAt = now()
            ->addDays((int) $nextStep->delay_days)
            ->addHours((int) $nextStep->delay_hours);

        $enrollment->update([
            'current_step' => $nextIndex,
            'next_send_at' => $nextSendAt,
        ]);
    }

    protected function substitute(string $template, Lead $lead): string
    {
        $replace = [
            '{first_name}' => (string) ($lead->first_name ?? ''),
            '{last_name}'  => (string) ($lead->last_name ?? ''),
            '{company}'    => (string) ($lead->company ?? ''),
            '{email}'      => (string) ($lead->email ?? ''),
        ];
        return strtr($template, $replace);
    }
}
