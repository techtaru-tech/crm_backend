<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AiSdrEnrollment;
use App\Support\DemoMode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Cron runner for the AI SDR.
 *
 * Scheduled every 15 minutes (routes/console.php). Selects the
 * enrollments that are due this tick (state=active AND next_action_at has
 * arrived) and dispatches one ProcessAiSdrEnrollment per row so each
 * enrollment makes at most ONE AI decision / ONE action.
 *
 * Mirrors ProcessEmailSequences: withoutGlobalScope('tenant') (no auth
 * context in a worker), a bounded batch, and a per-row try/catch so one
 * bad enrollment can never abort the whole tick.
 */
class ProcessAiSdrEnrollments implements ShouldQueue
{
    use Queueable;

    /** Upper bound on enrollments processed per tick. */
    private const BATCH_LIMIT = 500;

    public function handle(): void
    {
        // Whole feature is dormant on the public demo.
        if (DemoMode::isOn()) {
            return;
        }

        $enrollments = AiSdrEnrollment::withoutGlobalScope('tenant')
            ->due()
            ->orderBy('next_action_at')
            ->limit(self::BATCH_LIMIT)
            ->get(['id', 'tenant_id']);

        foreach ($enrollments as $enrollment) {
            try {
                ProcessAiSdrEnrollment::dispatch(
                    (int) $enrollment->id,
                    (int) $enrollment->tenant_id,
                );
            } catch (\Throwable $e) {
                Log::error('ProcessAiSdrEnrollments dispatch failed', [
                    'enrollment_id' => $enrollment->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }
    }
}
