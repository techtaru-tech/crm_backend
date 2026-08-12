<?php

namespace App\Jobs;

use App\Models\LeadActivity;
use App\Models\LeadCall;
use App\Services\Voice\TwilioVoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranscribeCall implements ShouldQueue
{
    use Queueable;

    public int $tries   = 2;
    public int $timeout = 300;

    public function __construct(public int $callId) {}

    public function handle(TwilioVoiceService $service): void
    {
        $call = LeadCall::withoutGlobalScope('tenant')->with('tenant')->find($this->callId);
        if (! $call || ! $call->recording_url) {
            return;
        }

        $tenant   = $call->tenant;
        $settings = (array) ($tenant?->getSetting('messaging') ?? []);
        $openAi   = (string) ($settings['openai_api_key'] ?? config('services.openai.api_key') ?? '');

        if ($openAi === '') {
            Log::info('TranscribeCall: skipped, no OpenAI key configured', ['call_id' => $call->id]);
            return;
        }

        $transcription = $service->transcribeRecording($call->recording_url, $openAi, $tenant);
        if (! $transcription) {
            return;
        }

        // i18n: queue workers boot with the app's default locale
        // not the tenant's preferred locale, so wrap the OpenAI summary
        // call in a temporary locale switch.  The tenant's default_locale
        // drives both the prompt translation (next-action label) AND the
        // explicit locale code sent to OpenAI inside the prompt — keeps
        // the AI's response language aligned with the workspace UI.
        $tenantLocale  = (string) ($tenant?->default_locale ?? config('app.locale'));
        $originalLocale = app()->getLocale();
        try {
            app()->setLocale($tenantLocale);
            $summary = $service->summarizeTranscription($transcription, $openAi);
        } finally {
            app()->setLocale($originalLocale);
        }

        $call->update([
            'transcription' => $transcription,
            'ai_summary'    => $summary,
        ]);

        try {
            LeadActivity::create([
                'tenant_id'   => $call->tenant_id,
                'lead_id'     => $call->lead_id,
                'user_id'     => $call->user_id,
                'type'        => 'call_transcribed',
                'description' => 'Call transcribed and summarized by AI.',
                'metadata'    => [
                    'call_id'     => $call->id,
                    'duration'    => $call->duration_seconds,
                    'has_summary' => $summary !== null,
                    'i18n_key'    => 'lead_activities.call_transcribed',
                    'i18n_params' => [],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('TranscribeCall: activity log failed', ['error' => $e->getMessage()]);
        }
    }
}
