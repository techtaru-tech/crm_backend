<?php

namespace App\Jobs;

use App\Models\OutboundWebhook;
use App\Models\WebhookDelivery;
use App\Support\UnsafeUrlException;
use App\Support\UrlSafetyGuard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchOutboundWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 5;
    public int $timeout = 30;

    /**
     * Persisted delivery record ID — reused across all retry attempts so we
     * update a single row instead of creating one per attempt.
     */
    public ?int $deliveryId = null;

    public function __construct(
        public readonly int    $tenantId,
        public readonly string $event,
        public readonly array  $payload,
        public readonly ?int   $webhookId = null,
    ) {}

    /**
     * Fire webhooks for all matching subscriptions on an event.
     */
    public static function fireForEvent(int $tenantId, string $event, array $payload): void
    {
        $webhooks = OutboundWebhook::where('tenant_id', $tenantId)
            ->where('enabled', true)
            ->get()
            ->filter(fn($wh) => $wh->listensTo($event) && $wh->matchesFilters($payload));

        foreach ($webhooks as $webhook) {
            static::dispatch($tenantId, $event, $payload, $webhook->id)
                ->onQueue('webhooks');
        }
    }

    public function handle(): void
    {
        if (! $this->webhookId) {
            return;
        }

        $webhook = OutboundWebhook::find($this->webhookId);
        if (! $webhook || ! $webhook->enabled) {
            return;
        }

        $body = json_encode([
            'event'     => $this->event,
            'tenant_id' => $this->tenantId,
            'timestamp' => now()->toIso8601String(),
            'data'      => $this->payload,
        ], JSON_UNESCAPED_UNICODE);

        // Replay-safe Stripe-style signature.  Consumer recomputes
        // hmac_sha256(secret, "{t}.{body}") and verifies the timestamp
        // is within their tolerance window (typically 5 minutes).
        $signed         = $webhook->signWithTimestamp($body);
        $signedHeader   = $signed['signature'];
        $signatureTs    = $signed['timestamp'];

        // On the first attempt, create the delivery record and persist its ID
        // so subsequent retry attempts update it rather than inserting new rows.
        if ($this->deliveryId === null) {
            $delivery = WebhookDelivery::create([
                'webhook_id' => $webhook->id,
                'tenant_id'  => $this->tenantId,
                'event'      => $this->event,
                'payload'    => $this->payload,
                'status'     => WebhookDelivery::STATUS_PENDING,
                'attempts'   => 1,
            ]);
            $this->deliveryId = $delivery->id;
        } else {
            $delivery = WebhookDelivery::findOrFail($this->deliveryId);
        }

        // SSRF guard: block outbound delivery to RFC1918, loopback,
        // link-local, and AWS/GCP/Azure metadata IPs.  Without this a
        // tenant could set the webhook URL to
        // http://169.254.169.254/... and read IMDS credentials, then
        // view them via the "Response" modal in the WebhookDelivery
        // log.  Marks the delivery as failed with a clear message and
        // returns — no retry, the URL would fail again.
        try {
            UrlSafetyGuard::assertSafe($webhook->url);
        } catch (UnsafeUrlException $e) {
            $delivery->update([
                'response_code' => 0,
                'response_body' => (string) __('jobs/dispatch_outbound_webhook.blocked_prefix', [
                    'error' => $e->getMessage(),
                ]),
                'latency_ms'    => 0,
                'status'        => WebhookDelivery::STATUS_FAILED,
                'attempts'      => $this->attempts(),
            ]);
            Log::warning('Outbound webhook blocked by SSRF guard', [
                'webhook_id' => $webhook->id,
                'tenant_id'  => $this->tenantId,
                'url'        => $webhook->url,
                'error'      => $e->getMessage(),
            ]);
            return;
        }

        $startTime = microtime(true);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type'             => 'application/json',
                    'X-LeadHub-Event'          => $this->event,
                    // Replay-safe header: t=<unix>,v1=<hex>.
                    // Consumer recomputes hmac_sha256(secret, "{t}.{body}")
                    // and rejects when |now - t| exceeds tolerance (~5 min).
                    'X-LeadHub-Signature'      => $signedHeader,
                    'X-LeadHub-Timestamp'      => $signatureTs,
                    'X-LeadHub-Delivery'       => (string) $delivery->id,
                    'User-Agent'               => 'LeadHub-Webhooks/1.0',
                ])
                ->send('POST', $webhook->url, ['body' => $body]);

            $latency = (int) ((microtime(true) - $startTime) * 1000);
            $success = $response->successful();

            if ($success) {
                $delivery->update([
                    'response_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 2000),
                    'latency_ms'    => $latency,
                    'status'        => WebhookDelivery::STATUS_SUCCESS,
                    'attempts'      => $this->attempts(),
                ]);
            } else {
                if ($this->attempts() < $this->tries) {
                    $delay = (int) (pow(2, $this->attempts()) * 60);
                    $delivery->update([
                        'response_code'  => $response->status(),
                        'response_body'  => substr($response->body(), 0, 2000),
                        'latency_ms'     => $latency,
                        'status'         => WebhookDelivery::STATUS_RETRYING,
                        'attempts'       => $this->attempts(),
                        'next_retry_at'  => now()->addSeconds($delay),
                    ]);
                    $this->release($delay);
                } else {
                    $delivery->update([
                        'response_code' => $response->status(),
                        'response_body' => substr($response->body(), 0, 2000),
                        'latency_ms'    => $latency,
                        'status'        => WebhookDelivery::STATUS_FAILED,
                        'attempts'      => $this->attempts(),
                    ]);
                }
            }

        } catch (\Throwable $e) {
            $latency = (int) ((microtime(true) - $startTime) * 1000);

            Log::warning('Outbound webhook delivery failed', [
                'webhook_id'  => $webhook->id,
                'delivery_id' => $delivery->id,
                'event'       => $this->event,
                'attempt'     => $this->attempts(),
                'error'       => $e->getMessage(),
            ]);

            if ($this->attempts() < $this->tries) {
                $delay = (int) (pow(2, $this->attempts()) * 60);
                $delivery->update([
                    'response_body' => $e->getMessage(),
                    'latency_ms'    => $latency,
                    'status'        => WebhookDelivery::STATUS_RETRYING,
                    'attempts'      => $this->attempts(),
                    'next_retry_at' => now()->addSeconds($delay),
                ]);
                $this->release($delay);
            } else {
                $delivery->update([
                    'response_body' => $e->getMessage(),
                    'latency_ms'    => $latency,
                    'status'        => WebhookDelivery::STATUS_FAILED,
                    'attempts'      => $this->attempts(),
                ]);
            }
        }
    }
}
