<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckNoActivityAutomations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cap on how many stale leads a single automation may dispatch
     * RunAutomation jobs for in one cron tick.  Without this, a
     * tenant with thousands of stale leads would dispatch a
     * RunAutomation-storm that on QUEUE_CONNECTION=sync (the install
     * default) executes inline during the cron call and can OOM PHP
     * or block the cron slot for 30+ minutes.  Any leads beyond the
     * cap are picked up on the next hourly tick naturally.
     */
    private const MAX_LEADS_PER_AUTOMATION = 200;

    public function handle(): void
    {
        // cursor() materializes Automation rows one at a time so a
        // tenant base with hundreds of `no_activity` automations
        // doesn't load the full set into memory at once.
        $automations = Automation::where('enabled', true)
            ->where('trigger_type', 'no_activity')
            ->cursor();

        foreach ($automations as $automation) {
            // Guard each automation in its own try/catch.  Without this,
            // one tenant's malformed trigger_config (unexpected `unit`,
            // missing `value`, a stale relation pointer) would throw and
            // silently abort the hourly run for every tenant that
            // follows in the loop — operators would have no signal that
            // their automations stopped firing.
            try {
                $config = $automation->trigger_config ?? [];
                $unit   = $config['unit'] ?? 'hours';
                $value  = (int) ($config['value'] ?? $config['hours'] ?? 24);

                $cutoff = match ($unit) {
                    'days'  => now()->subDays($value),
                    default => now()->subHours($value),
                };

                $leads = Lead::where('tenant_id', $automation->tenant_id)
                    ->whereNull('deleted_at')
                    ->whereNotIn('status', \App\Enums\LeadStatus::terminalValues())
                    ->where('created_at', '<=', $cutoff)
                    ->whereDoesntHave('activities', function ($q) use ($cutoff) {
                        $q->where('created_at', '>', $cutoff);
                    })
                    ->limit(self::MAX_LEADS_PER_AUTOMATION)
                    ->get();

                foreach ($leads as $lead) {
                    RunAutomation::dispatch($automation->id, $lead->id, 0, null, $lead->tenant_id);
                }
            } catch (\Throwable $e) {
                Log::warning('CheckNoActivityAutomations: automation skipped', [
                    'automation_id' => $automation->id,
                    'tenant_id'     => $automation->tenant_id,
                    'error'         => $e->getMessage(),
                ]);
                // Continue with the next automation.
            }
        }
    }
}
