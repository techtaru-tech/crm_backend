<?php

namespace App\Jobs;

use App\Integrations\IntegrationRegistry;
use App\Models\Integration;
use App\Models\IntegrationSyncLog;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SyncLeadToIntegration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;
    public int $maxExceptions = 3;

    public function __construct(
        public readonly int $integrationId,
        public readonly int $leadId,
        public readonly string $event = 'lead_created',
        public readonly ?int $syncLogId = null,
        // $tenantId is read by AppServiceProvider's Queue::before
        // hook (reflection on the resolved command) to bind
        // `current_tenant` for the duration of the worker run, so
        // BelongsToTenant queries inside handle() / connector
        // upsert calls don't fail closed.  Defaulted for back-compat
        // with existing positional dispatches.
        public readonly ?int $tenantId = null,
    ) {
        $this->onQueue('integrations');
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(): void
    {
        $integration = Integration::find($this->integrationId);
        $lead        = Lead::with(['tags', 'pipeline', 'pipelineStage'])->find($this->leadId);

        if (! $integration || ! $lead || ! $integration->enabled) {
            return;
        }

        $syncLog = $this->syncLogId
            ? IntegrationSyncLog::find($this->syncLogId)
            : IntegrationSyncLog::create([
                'integration_id' => $integration->id,
                'lead_id'        => $lead->id,
                'event'          => $this->event,
                'status'         => IntegrationSyncLog::STATUS_PENDING,
                'attempts'       => 0,
                'payload'        => $this->leadToPayload($lead),
            ]);

        if (! $syncLog) return;

        $syncLog->increment('attempts');
        $syncLog->update(['status' => IntegrationSyncLog::STATUS_RETRYING, 'retried_at' => now()]);

        try {
            $connector   = IntegrationRegistry::make($integration->type, $integration->getConfig());
            $fieldMapping = $integration->field_mapping ?? [];
            $filterConfig = $integration->filter_config ?? [];

            $response = $connector->pushLead($lead, $fieldMapping, $filterConfig);

            $skipped    = isset($response['skipped']) && $response['skipped'];
            $statusCode = $response['status_code'] ?? null;
            $httpFailed = $statusCode !== null && $statusCode >= 400;

            if ($httpFailed && ! $skipped) {
                $bodyText = is_array($response['body'] ?? null)
                    ? json_encode($response['body'])
                    : (string) ($response['body'] ?? '');
                $translated = __('integration_sync_logs.http_error_prefix', [
                    'status' => (string) $statusCode,
                    'body'   => $bodyText,
                ]);
                $errorMsg = is_string($translated)
                    ? $translated
                    : "HTTP {$statusCode}: {$bodyText}";
                throw new \RuntimeException($errorMsg);
            }

            $syncLog->update([
                'status'        => IntegrationSyncLog::STATUS_SUCCESS,
                'response'      => $response,
                'error_message' => null,
            ]);

            $integration->update(['last_synced_at' => now(), 'status' => Integration::STATUS_CONNECTED]);

        } catch (\Throwable $e) {
            Log::error('IntegrationSync failed', [
                'integration' => $integration->type,
                'lead'        => $this->leadId,
                'error'       => $e->getMessage(),
            ]);

            $isLastAttempt = $syncLog->attempts >= $this->tries;

            $syncLog->update([
                'status'        => $isLastAttempt ? IntegrationSyncLog::STATUS_FAILED : IntegrationSyncLog::STATUS_RETRYING,
                'error_message' => $e->getMessage(),
            ]);

            if ($isLastAttempt) {
                $integration->update(['status' => Integration::STATUS_ERROR]);
                $this->notifyAdmins($integration, $lead, $e->getMessage());
            }

            throw $e;
        }
    }

    private function leadToPayload(Lead $lead): array
    {
        return [
            'id'         => $lead->id,
            'first_name' => $lead->first_name,
            'last_name'  => $lead->last_name,
            'email'      => $lead->email,
            'phone'      => $lead->phone,
            'source'     => $lead->source,
            'status'     => $lead->status?->value,
            'lead_score' => $lead->lead_score,
        ];
    }

    private function notifyAdmins(Integration $integration, Lead $lead, string $error): void
    {
        try {
            $admins = User::where('tenant_id', $integration->tenant_id)
                ->where('role', 'admin')
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\IntegrationSyncFailedNotification($integration, $lead, $error));
            }
        } catch (\Throwable $e) {
            logger()->warning('SyncLeadToIntegration: failed to notify admins', [
                'integration_id' => $integration->id,
                'lead_id'        => $lead->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
