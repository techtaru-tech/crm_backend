<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\TranscribeCall;
use App\Models\LeadCall;
use App\Models\Tenant;
use App\Services\Voice\TwilioVoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Twilio → LeadHub voice webhooks.
 *
 * All routes are tenant + token scoped. Token is stored in
 * tenants.settings.messaging.voice.voice_webhook_token.
 */
class VoiceWebhookController extends Controller
{
    /**
     * TwiML fetched by Twilio after the user answers — returns <Dial>
     * to the lead's number.
     */
    public function twiml(string $tenant, string $token, Request $request, TwilioVoiceService $service): SymfonyResponse
    {
        $tenantModel = $this->verify($tenant, $token);
        if (! $tenantModel) {
            return response('Unauthorized', 401);
        }

        $sid = (string) $request->input('CallSid', '');
        $call = LeadCall::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantModel->id)
            ->where('external_id', $sid)
            ->first();

        if (! $call) {
            Log::warning('VoiceWebhook::twiml: CallSid not found', ['sid' => $sid, 'tenant' => $tenantModel->id]);
            return response('<?xml version="1.0" encoding="UTF-8"?><Response><Hangup/></Response>', 200)
                ->header('Content-Type', 'text/xml');
        }

        $voice = (array) (($tenantModel->getSetting('messaging') ?? [])['voice'] ?? []);
        $caller = (string) ($voice['voice_from_number'] ?? '');

        $xml = $service->generateTwiML($call, $caller);
        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Call-status updates from Twilio (initiated/ringing/in-progress/completed/etc).
     */
    public function statusCallback(string $tenant, string $token, Request $request): Response
    {
        $tenantModel = $this->verify($tenant, $token);
        if (! $tenantModel) {
            return response('Unauthorized', 401);
        }

        $sid = (string) $request->input('CallSid', '');
        $call = LeadCall::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantModel->id)
            ->where('external_id', $sid)
            ->first();

        if (! $call) {
            return response('OK', 200);
        }

        $status = (string) $request->input('CallStatus', $call->status);
        $duration = $request->input('CallDuration');
        $recordingUrl = $request->input('RecordingUrl');
        $recordingSid = $request->input('RecordingSid');

        $update = ['status' => $status];
        if ($duration !== null) {
            $update['duration_seconds'] = (int) $duration;
        }
        if ($recordingUrl) {
            $update['recording_url'] = (string) $recordingUrl;
        }
        if ($recordingSid) {
            $update['recording_sid'] = (string) $recordingSid;
        }
        if (in_array($status, ['completed', 'failed', 'busy', 'no-answer', 'canceled'], true)) {
            $update['ended_at'] = now();
        }

        $call->update($update);

        // Kick off transcription when we have a recording + the tenant opted in.
        $voice = (array) (($tenantModel->getSetting('messaging') ?? [])['voice'] ?? []);
        if ($status === 'completed' && $recordingUrl && ! empty($voice['voice_transcription_enabled'])) {
            try {
                TranscribeCall::dispatch($call->id);
            } catch (\Throwable $e) {
                Log::warning('Dispatch TranscribeCall failed', ['error' => $e->getMessage()]);
            }
        }

        return response('OK', 200);
    }

    /**
     * Recording-completed callback — Twilio posts this when the MP3 is ready.
     */
    public function recordingCallback(string $tenant, string $token, Request $request): Response
    {
        $tenantModel = $this->verify($tenant, $token);
        if (! $tenantModel) {
            return response('Unauthorized', 401);
        }

        $sid = (string) $request->input('CallSid', '');
        $call = LeadCall::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantModel->id)
            ->where('external_id', $sid)
            ->first();

        if (! $call) {
            return response('OK', 200);
        }

        $url = (string) $request->input('RecordingUrl', '');
        if ($url) {
            $call->update([
                'recording_url' => $url,
                'recording_sid' => (string) $request->input('RecordingSid', $call->recording_sid),
            ]);

            $voice = (array) (($tenantModel->getSetting('messaging') ?? [])['voice'] ?? []);
            if (! empty($voice['voice_transcription_enabled'])) {
                try {
                    TranscribeCall::dispatch($call->id);
                } catch (\Throwable $e) {
                    Log::warning('Dispatch TranscribeCall failed (recordingCallback)', ['error' => $e->getMessage()]);
                }
            }
        }

        return response('OK', 200);
    }

    /**
     * Resolve + authorize a tenant for the inbound webhook.
     */
    protected function verify(string $tenant, string $token): ?Tenant
    {
        $model = Tenant::query()->find((int) $tenant);
        if (! $model) {
            return null;
        }
        $expected = (string) (($model->getSetting('messaging') ?? [])['voice']['voice_webhook_token'] ?? '');
        if ($expected === '' || ! hash_equals($expected, $token)) {
            return null;
        }
        return $model;
    }
}
