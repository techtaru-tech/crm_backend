<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\AutomationStep;
use App\Models\Lead;
use App\Services\Automations\ActionExecutorFactory;
use App\Services\Automations\AutomationConditionEvaluator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunAutomation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly int $automationId,
        public readonly int $leadId,
        public readonly int $startFromStep = 0,
        public readonly ?int $runId = null,
        // $tenantId is read by AppServiceProvider's Queue::before
        // hook (reflection on the resolved command) to bind
        // `current_tenant` for the duration of the worker run, so
        // BelongsToTenant queries inside handle() / action executors
        // don't fail closed.  Defaults to null for back-compat with
        // existing positional dispatches.
        public readonly ?int $tenantId = null,
    ) {
        $this->onQueue('automations');
    }

    public function handle(ActionExecutorFactory $factory, AutomationConditionEvaluator $evaluator): void
    {
        $automation = Automation::with('steps')->find($this->automationId);
        $lead       = Lead::with('tags')->find($this->leadId);

        if (! $automation || ! $lead || ! $automation->enabled) {
            return;
        }

        $run = $this->runId
            ? AutomationRun::find($this->runId)
            : AutomationRun::create([
                'automation_id' => $automation->id,
                'lead_id'       => $lead->id,
                'started_at'    => now(),
                'status'        => AutomationRun::STATUS_RUNNING,
                'log'           => [],
            ]);

        if (! $run) return;

        $log         = $run->log ?? [];
        $hasFailure  = false;
        $steps       = $automation->steps->where('sort_order', '>=', $this->startFromStep)->values();

        foreach ($steps as $index => $step) {
            $stepLog = [
                'step_id'    => $step->id,
                'type'       => $step->type,
                'sort_order' => $step->sort_order,
                'started_at' => now()->toIso8601String(),
            ];

            try {
                if ($step->type === 'condition') {
                    $passed = $evaluator->evaluate($lead, [$step->config ?? []]);
                    $stepLog['result']  = $passed ? 'passed' : 'skipped';
                    $stepLog['success'] = true;
                    if (! $passed) {
                        $stepLog['message'] = __('automation_runs.log.condition_not_met');
                        $log[] = $stepLog;
                        break;
                    }
                } elseif ($step->type === 'delay') {
                    $minutes = $this->resolveDelayMinutes($step->config ?? []);
                    $stepLog['result']  = __('automation_runs.log.delaying_minutes', ['minutes' => $minutes]);
                    $stepLog['success'] = true;
                    $log[] = $stepLog;

                    $remainingSteps = $steps->slice($index + 1);
                    if ($remainingSteps->isNotEmpty()) {
                        $nextSortOrder = $remainingSteps->first()->sort_order;
                        $run->update(['log' => $log]);
                        RunAutomation::dispatch(
                            $automation->id,
                            $lead->id,
                            $nextSortOrder,
                            $run->id,
                            $lead->tenant_id,
                        )->delay(now()->addMinutes($minutes));
                    } else {
                        $run->update([
                            'finished_at' => now(),
                            'status'      => AutomationRun::STATUS_SUCCESS,
                            'log'         => $log,
                        ]);
                    }
                    return;
                } elseif ($step->type === 'action') {
                    $actionType = $step->config['action_type'] ?? '';
                    $success    = $factory->execute($actionType, $lead, $step->config ?? [], $run);

                    $stepLog['action']  = $actionType;
                    $stepLog['success'] = $success;
                    $stepLog['result']  = $success ? 'ok' : 'failed';

                    if (! $success) {
                        $hasFailure = true;
                    }
                }
            } catch (\Throwable $e) {
                $stepLog['success'] = false;
                $stepLog['error']   = $e->getMessage();
                $hasFailure = true;
                Log::error('Automation step error', ['step' => $step->id, 'error' => $e->getMessage()]);
            }

            $stepLog['finished_at'] = now()->toIso8601String();
            $log[] = $stepLog;
        }

        $status = $hasFailure
            ? (count(array_filter($log, fn($l) => $l['success'] ?? false)) > 0
                ? AutomationRun::STATUS_PARTIAL
                : AutomationRun::STATUS_FAILED)
            : AutomationRun::STATUS_SUCCESS;

        $run->update([
            'finished_at' => now(),
            'status'      => $status,
            'log'         => $log,
        ]);

        DispatchOutboundWebhook::fireForEvent($lead->tenant_id, 'automation.triggered', [
            'automation_id'   => $automation->id,
            'automation_name' => $automation->name,
            'run_id'          => $run->id,
            'lead_id'         => $lead->id,
            'lead_email'      => $lead->email,
            'status'          => $status,
            'triggered_at'    => $run->started_at?->toIso8601String(),
            'finished_at'     => now()->toIso8601String(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('RunAutomation job failed', [
            'automation_id' => $this->automationId,
            'lead_id'       => $this->leadId,
            'run_id'        => $this->runId,
            'error'         => $exception->getMessage(),
        ]);

        if ($this->runId) {
            AutomationRun::where('id', $this->runId)
                ->where('status', AutomationRun::STATUS_RUNNING)
                ->update([
                    'finished_at' => now(),
                    'status'      => AutomationRun::STATUS_FAILED,
                ]);
        }
    }

    private function resolveDelayMinutes(array $config): int
    {
        $amount = (int) ($config['amount'] ?? 1);
        $unit   = $config['unit'] ?? 'minutes';

        return match ($unit) {
            'hours' => $amount * 60,
            'days'  => $amount * 60 * 24,
            default => $amount,
        };
    }
}
