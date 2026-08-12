<?php

namespace App\Services\Voice;

use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Per-tenant Twilio Voice integration.
 *
 * All credentials are pulled from tenant settings (messaging.twilio_sid /
 * messaging.twilio_token) and voice-specific keys under messaging.voice.*.
 * Uses raw Illuminate\Support\Facades\Http — no SDK dependency.
 */
class TwilioVoiceService
{
    /**
     * Initiate an outbound click-to-call: Twilio rings the user's phone first,
     * then our TwiML endpoint returns <Dial> to bridge them to the lead.
     *
     * Returns the persisted LeadCall row (with external_id set on success).
     */
    public function initiateCall(Lead $lead, User $caller): LeadCall
    {
        $tenant   = $lead->tenant;
        $settings = (array) ($tenant?->getSetting('messaging') ?? []);
        $voice    = (array) ($settings['voice'] ?? []);

        $sid   = (string) ($settings['twilio_sid'] ?? '');
        $token = (string) ($settings['twilio_token'] ?? '');
        $from  = (string) ($voice['voice_from_number'] ?? $settings['twilio_from'] ?? '');
        $to    = (string) ($caller->phone ?? ''); // Twilio dials the user first.

        // Create the LeadCall row up front so we have an ID for webhook URLs.
        $call = LeadCall::create([
            'tenant_id'  => $lead->tenant_id,
            'lead_id'    => $lead->id,
            'user_id'    => $caller->id,
            'direction'  => 'outbound',
            'from_number' => $from ?: '-',
            'to_number'  => (string) ($lead->phone ?? ''),
            'status'     => 'initiated',
            'started_at' => now(),
        ]);

        if (! $sid || ! $token || ! $from || ! $to || ! $lead->phone) {
            $call->update([
                'status' => 'failed',
                'notes'  => (string) __('services/twilio_voice.missing_credentials'),
            ]);
            return $call;
        }

        $webhookToken = (string) ($voice['voice_webhook_token'] ?? '');
        $appUrl       = rtrim((string) config('app.url'), '/');
        $twimlUrl     = $appUrl . '/api/voice/' . $lead->tenant_id . '/twiml/' . $webhookToken;
        $statusUrl    = $appUrl . '/api/voice/' . $lead->tenant_id . '/status/' . $webhookToken;
        $recordingUrl = $appUrl . '/api/voice/' . $lead->tenant_id . '/recording/' . $webhookToken;

        $payload = [
            'From' => $from,
            'To'   => $to, // USER's phone — Twilio bridges to the lead via TwiML.
            'Url'  => $twimlUrl,
            'StatusCallback'       => $statusUrl,
            'StatusCallbackMethod' => 'POST',
            'StatusCallbackEvent'  => ['initiated', 'ringing', 'answered', 'completed'],
        ];

        if (! empty($voice['voice_recording_enabled'])) {
            $payload['Record']                  = 'true';
            $payload['RecordingStatusCallback'] = $recordingUrl;
            $payload['RecordingStatusCallbackEvent'] = ['completed'];
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Calls.json", $payload);

            if ($response->successful()) {
                $call->update([
                    'external_id' => (string) $response->json('sid'),
                    'status'      => (string) ($response->json('status') ?? 'initiated'),
                ]);
            } else {
                $call->update([
                    'status' => 'failed',
                    'notes'  => (string) __('services/twilio_voice.twilio_error', [
                        'body' => Str::limit($response->body(), 240),
                    ]),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('TwilioVoiceService::initiateCall exception', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
            $call->update([
                'status' => 'failed',
                'notes'  => (string) __('services/twilio_voice.exception', [
                    'error' => Str::limit($e->getMessage(), 240),
                ]),
            ]);
        }

        return $call;
    }

    /**
     * Build the TwiML XML Twilio fetches after the user answers their own phone.
     * Dials the lead's number and (optionally) records from answer.
     */
    public function generateTwiML(LeadCall $call, string $callerNumber): string
    {
        $to = htmlspecialchars((string) $call->to_number, ENT_XML1, 'UTF-8');
        $cid = htmlspecialchars($callerNumber, ENT_XML1, 'UTF-8');

        $tenant   = $call->tenant;
        $voice    = (array) (($tenant?->getSetting('messaging') ?? [])['voice'] ?? []);
        $record   = ! empty($voice['voice_recording_enabled']) ? ' record="record-from-answer"' : '';

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Response><Dial' . $record . ' callerId="' . $cid . '"><Number>'
            . $to . '</Number></Dial></Response>';
    }

    /**
     * Download the recording MP3 from Twilio (Basic auth) and submit it to
     * OpenAI Whisper for transcription. Returns null on any failure.
     */
    public function transcribeRecording(string $recordingUrl, string $tenantOpenAiKey, ?Tenant $tenant = null): ?string
    {
        try {
            $sid   = (string) ($tenant?->getSetting('messaging')['twilio_sid'] ?? '');
            $token = (string) ($tenant?->getSetting('messaging')['twilio_token'] ?? '');

            $mp3Url = rtrim($recordingUrl, '/') . '.mp3';
            $audio  = Http::withBasicAuth($sid, $token)->timeout(60)->get($mp3Url);
            if (! $audio->successful()) {
                Log::warning('TwilioVoiceService: recording download failed', ['status' => $audio->status()]);
                return null;
            }

            $response = Http::withToken($tenantOpenAiKey)
                ->timeout(120)
                ->attach('file', $audio->body(), 'recording.mp3', ['Content-Type' => 'audio/mpeg'])
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model'           => 'whisper-1',
                    'response_format' => 'text',
                ]);

            if ($response->successful()) {
                return trim((string) $response->body());
            }

            Log::warning('TwilioVoiceService: whisper failed', ['body' => Str::limit($response->body(), 240)]);
            return null;
        } catch (\Throwable $e) {
            Log::error('TwilioVoiceService::transcribeRecording exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Feed transcription to OpenAI chat completions to produce a short summary
     * + next-action bullet.
     *
     * i18n fix: prompt routed through the translator so the AI
     * responds in the buyer's locale and labels the next-action bullet
     * with the locale-appropriate phrase.  The :locale placeholder
     * passes the active app locale to OpenAI so the model knows which
     * language to answer in; the :nextActionLabel placeholder localises
     * the literal "Next action:" prefix the prompt instructs the model
     * to emit, so it doesn't leak English into a non-EN buyer's UI.
     */
    public function summarizeTranscription(string $transcription, string $tenantOpenAiKey): ?string
    {
        if (trim($transcription) === '') {
            return null;
        }

        $locale          = (string) app()->getLocale();
        $nextActionLabel = (string) __('services/twilio_voice.summary_next_action_label');
        $transcriptLabel = (string) __('services/twilio_voice.summary_transcript_label');
        $systemPrompt    = (string) __('services/twilio_voice.summary_system_prompt', [
            'locale'          => $locale,
            'nextActionLabel' => $nextActionLabel,
        ]);

        try {
            $response = Http::withToken($tenantOpenAiKey)
                ->timeout(60)
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $transcriptLabel . ":\n" . Str::limit($transcription, 8000)],
                    ],
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                return trim((string) $response->json('choices.0.message.content'));
            }

            Log::warning('TwilioVoiceService: summary failed', ['body' => Str::limit($response->body(), 240)]);
            return null;
        } catch (\Throwable $e) {
            Log::error('TwilioVoiceService::summarizeTranscription exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
