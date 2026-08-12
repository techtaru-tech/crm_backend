<?php

declare(strict_types=1);

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Services\Messaging\WhatsAppMessenger;
use Illuminate\Support\Facades\Log;

/**
 * Automation action: send a WhatsApp message via Twilio.
 *
 * Mirrors SendSmsAction's contract (same execute() signature so the
 * automation step factory can swap them transparently) but routes
 * through WhatsAppMessenger which handles E.164 normalization,
 * timeline logging, and graceful credential-missing failure.
 *
 * Config shape:
 *   - message:  template string with merge tags ({{first_name}}, etc.)
 *
 * Returns true on successful send, false on any failure (always
 * logged so the AutomationRun has a paper trail).
 */
class SendWhatsAppAction
{
    public function __construct(protected WhatsAppMessenger $messenger)
    {
    }

    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $body = $this->interpolate($config['message'] ?? '', $lead);
        if (trim($body) === '') {
            Log::info('SendWhatsAppAction: skipped — empty body', [
                'lead_id' => $lead->id,
                'run_id'  => $run->id,
            ]);
            return false;
        }

        $result = $this->messenger->send($lead->tenant, $lead, $body);

        if ($result['sent']) {
            Log::info('SendWhatsAppAction: sent', [
                'lead_id'         => $lead->id,
                'run_id'          => $run->id,
                'twilio_sid'      => $result['message_sid'],
                'lead_message_id' => $result['lead_message_id'],
            ]);
            return true;
        }

        Log::warning('SendWhatsAppAction: failed', [
            'lead_id' => $lead->id,
            'run_id'  => $run->id,
            'error'   => $result['error'],
        ]);
        return false;
    }

    private function interpolate(string $template, Lead $lead): string
    {
        return str_replace(
            ['{{first_name}}', '{{last_name}}', '{{full_name}}', '{{email}}', '{{company}}'],
            [
                $lead->first_name ?? '',
                $lead->last_name  ?? '',
                $lead->full_name  ?? '',
                $lead->email      ?? '',
                $lead->company    ?? '',
            ],
            $template,
        );
    }
}
