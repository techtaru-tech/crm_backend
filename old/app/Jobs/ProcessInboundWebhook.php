<?php

namespace App\Jobs;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use App\Services\ConnectorRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessInboundWebhook implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public int $webhookLogId,
        public int $connectionId,
        // $tenantId is read by AppServiceProvider's Queue::before
        // hook (reflection on the resolved command) to bind
        // `current_tenant` for the duration of the worker run, so
        // BelongsToTenant queries inside connector handleWebhook()
        // don't fail closed.  Default null keeps existing 2-arg
        // dispatches working until the dispatcher catches up.
        public ?int $tenantId = null,
    ) {
        $this->onQueue('webhooks');
    }

    public function handle(ConnectorRegistry $registry): void
    {
        $log = WebhookLog::find($this->webhookLogId);
        $connection = LeadSourceConnection::find($this->connectionId);

        if (! $log || ! $connection) {
            Log::warning('ProcessInboundWebhook: log or connection not found', [
                'log_id'        => $this->webhookLogId,
                'connection_id' => $this->connectionId,
            ]);
            return;
        }

        try {
            $connector   = $registry->resolve($connection->source);
            $fakeRequest = $this->reconstructRequest($log);
            $leads       = $connector->handleWebhook($fakeRequest, $connection, $log);

            $log->update([
                'status'        => 'success',
                'leads_created' => count($leads),
                'processed_at'  => now(),
            ]);

            $connection->markReceived();
        } catch (Throwable $e) {
            Log::error('Webhook processing failed', [
                'log_id'    => $this->webhookLogId,
                'source'    => $connection->source,
                'error'     => $e->getMessage(),
            ]);

            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at'  => now(),
            ]);

            $connection->markError($e->getMessage());

            throw $e;
        }
    }

    protected function reconstructRequest(WebhookLog $log): Request
    {
        $headers     = $log->headers ?? [];
        $rawPayload  = $log->payload ?? '';
        $contentType = '';

        if (is_array($headers)) {
            $contentType = $headers['content-type'][0]
                ?? $headers['Content-Type'][0]
                ?? $headers['CONTENT_TYPE'][0]
                ?? '';
        }

        $isFormEncoded = str_contains(strtolower($contentType), 'application/x-www-form-urlencoded');
        $isMultipart   = str_contains(strtolower($contentType), 'multipart/form-data');

        if ($isFormEncoded || $isMultipart) {
            $parsedData  = [];
            parse_str($rawPayload, $parsedData);
            $fakeRequest = Request::create('/webhook', 'POST', $parsedData);
        } else {
            $fakeRequest = Request::create(
                '/webhook',
                'POST',
                [],
                [],
                [],
                ['CONTENT_TYPE' => $contentType ?: 'application/json'],
                $rawPayload
            );
        }

        if (is_array($headers)) {
            $fakeRequest->headers->replace($headers);
        }

        return $fakeRequest;
    }

    public function failed(Throwable $exception): void
    {
        $log = WebhookLog::find($this->webhookLogId);
        if ($log) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $exception->getMessage(),
                'processed_at'  => now(),
            ]);
        }
    }
}
