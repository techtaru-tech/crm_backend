<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Records activation signals for a tenant — the conversion-funnel
 * metric the operator needs to diagnose trial→paid drop-off.
 *
 * Signal taxonomy (kept stable so reports group cleanly):
 *
 *   first_lead_added          first row in `leads` for this tenant
 *   first_form_created        first row in `forms`
 *   first_automation_enabled  first automation flipped is_active=true
 *   first_pipeline_customised first pipeline edited beyond the seed
 *   first_team_invite_sent    first invitation row created
 *   first_email_sent          first transactional email sent
 *
 * Activation = first signal (whichever comes first) sets
 * tenants.activated_at.  Subsequent signals just append to the
 * activation_signals array.
 *
 * Idempotent — re-recording the same signal is a no-op (we check
 * the JSON for existence first).
 */
class ActivationTracker
{
    public const SIGNAL_FIRST_LEAD          = 'first_lead_added';
    public const SIGNAL_FIRST_FORM          = 'first_form_created';
    public const SIGNAL_FIRST_AUTOMATION    = 'first_automation_enabled';
    public const SIGNAL_FIRST_PIPELINE      = 'first_pipeline_customised';
    public const SIGNAL_FIRST_INVITE        = 'first_team_invite_sent';
    public const SIGNAL_FIRST_EMAIL         = 'first_email_sent';

    public const ALL_SIGNALS = [
        self::SIGNAL_FIRST_LEAD       => 'First lead added',
        self::SIGNAL_FIRST_FORM       => 'First form created',
        self::SIGNAL_FIRST_AUTOMATION => 'First automation enabled',
        self::SIGNAL_FIRST_PIPELINE   => 'First pipeline customised',
        self::SIGNAL_FIRST_INVITE     => 'First team invite sent',
        self::SIGNAL_FIRST_EMAIL      => 'First email sent',
    ];

    public function record(?Tenant $tenant, string $signal): void
    {
        if (! $tenant) return;
        if (! array_key_exists($signal, self::ALL_SIGNALS)) return;

        try {
            $signals = $tenant->activation_signals ?? [];

            foreach ($signals as $row) {
                if (($row['signal'] ?? null) === $signal) {
                    return;
                }
            }

            $signals[] = [
                'signal'      => $signal,
                'occurred_at' => Carbon::now()->toIso8601String(),
            ];

            $update = ['activation_signals' => $signals];

            if (! $tenant->activated_at) {
                $update['activated_at'] = Carbon::now();
            }

            $tenant->forceFill($update)->save();
        } catch (\Throwable $e) {
            Log::warning('ActivationTracker failed', [
                'tenant_id' => $tenant->id,
                'signal'    => $signal,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Activation rate over a date range.  Returns 0.0 - 1.0.
     */
    public function activationRate(?Carbon $since = null, ?Carbon $until = null): float
    {
        $since ??= Carbon::now()->subDays(30);
        $until ??= Carbon::now();

        $totalCreated = Tenant::query()
            ->whereBetween('created_at', [$since, $until])
            ->count();

        if ($totalCreated === 0) return 0.0;

        $activated = Tenant::query()
            ->whereBetween('created_at', [$since, $until])
            ->whereNotNull('activated_at')
            ->count();

        return round($activated / $totalCreated, 4);
    }

    /**
     * Median time-to-activation in hours for tenants who DID activate.
     */
    public function medianTimeToActivationHours(?Carbon $since = null, ?Carbon $until = null): ?float
    {
        $since ??= Carbon::now()->subDays(30);
        $until ??= Carbon::now();

        $rows = Tenant::query()
            ->whereBetween('created_at', [$since, $until])
            ->whereNotNull('activated_at')
            ->get(['created_at', 'activated_at']);

        if ($rows->isEmpty()) return null;

        $hours = $rows
            ->map(fn ($t) => (float) $t->created_at->diffInHours($t->activated_at))
            ->sort()
            ->values()
            ->all();

        $count = count($hours);
        $mid = (int) floor($count / 2);
        return $count % 2
            ? $hours[$mid]
            : (($hours[$mid - 1] + $hours[$mid]) / 2);
    }
}
