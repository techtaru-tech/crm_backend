<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInboundWebhook;
use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use App\Services\ConnectorRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class InboundWebhookController extends Controller
{
    public function __construct(protected ConnectorRegistry $registry) {}

    public function handle(Request $request, string $tenant, string $source, string $token): Response|JsonResponse
    {
        $connection = LeadSourceConnection::where('tenant_id', $tenant)
            ->where('source', $source)
            ->where('webhook_token', $token)
            ->where('active', true)
            ->first();

        if (! $connection) {
            // Fix note: redact the full token in logs.  The token is
            // a Str::random(48) secret — if logs forward to a
            // monitoring service (Sentry, ELK, Loggly), a partial
            // fuzz attack against this endpoint would pollute logs
            // and expose any near-miss tokens.  Log only the first 6
            // chars for triage; an attacker hitting wrong-token must
            // still brute-force the remaining 42 chars.
            $redactedToken = is_string($token) && strlen($token) > 6
                ? substr($token, 0, 6) . '...'
                : '...';
            Log::warning('Webhook: unknown token or inactive connection', [
                'tenant' => $tenant,
                'source' => $source,
                'token'  => $redactedToken,
            ]);
            return response('OK', 200);
        }

        if (! $this->registry->has($source)) {
            Log::warning('Webhook: unregistered source', compact('source'));
            return response('OK', 200);
        }

        $connector = $this->registry->resolve($source);

        if ($request->isMethod('GET')) {
            return $this->handleGetVerification($request, $source, $connection);
        }

        if (! $connector->verifyWebhook($request, $connection)) {
            $this->logWebhook($connection, $request, 'invalid_signature');
            Log::warning('Webhook: signature mismatch', ['source' => $source, 'connection_id' => $connection->id]);
            return response('OK', 200);
        }

        $log = $this->logWebhook($connection, $request, 'pending');

        ProcessInboundWebhook::dispatch($log->id, $connection->id, $connection->tenant_id);

        return response('OK', 200);
    }

    protected function handleGetVerification(
        Request $request,
        string $source,
        LeadSourceConnection $connection
    ): Response|JsonResponse {
        $crcToken = $request->query('crc_token');
        if ($crcToken && $source === 'twitter') {
            return $this->handleTwitterCrc($crcToken, $connection);
        }

        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');
        if ($challenge !== null) {
            return response((string) $challenge, 200);
        }

        return response('OK', 200);
    }

    protected function handleTwitterCrc(string $crcToken, LeadSourceConnection $connection): JsonResponse
    {
        $credentials    = $connection->credentials ?? [];
        $consumerSecret = $credentials['consumer_secret'] ?? null;

        if (! $consumerSecret) {
            Log::warning('Twitter CRC: missing consumer_secret', ['connection_id' => $connection->id]);
            return response()->json(['response_token' => ''], 200);
        }

        $responseToken = 'sha256=' . base64_encode(
            hash_hmac('sha256', $crcToken, $consumerSecret, true)
        );

        return response()->json(['response_token' => $responseToken], 200);
    }

    protected function logWebhook(
        LeadSourceConnection $connection,
        Request $request,
        string $status = 'pending'
    ): WebhookLog {
        return WebhookLog::create([
            'tenant_id'            => $connection->tenant_id,
            'source_connection_id' => $connection->id,
            'source'               => $connection->source,
            'status'               => $status,
            'headers'              => $request->headers->all(),
            'payload'              => $request->getContent() ?: json_encode($request->all()),
            'ip_address'           => $request->ip(),
        ]);
    }
}
