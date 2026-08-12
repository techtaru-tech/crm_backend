<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $to = $lead->phone_normalized ?? $lead->phone;
        if (empty($to)) {
            Log::info('SendSmsAction: skipped — no phone number', ['lead_id' => $lead->id, 'run_id' => $run->id]);
            return false;
        }

        // Pull Twilio credentials from the tenant's `settings` JSON
        // (there is no `TenantSetting` model — messaging settings live
        // under `tenants.settings.messaging.*`). Fall back to script
        // defaults from config/services.php when the tenant hasn't
        // configured their own.
        $tenant   = Tenant::find($lead->tenant_id);
        $messaging = (array) ($tenant?->getSetting('messaging') ?? []);
        $sid      = $messaging['twilio_sid']   ?? config('services.twilio.sid');
        $token    = $messaging['twilio_token'] ?? config('services.twilio.token');
        $from     = $messaging['twilio_from']  ?? config('services.twilio.from');

        if (!$sid || !$token || !$from) {
            Log::info('SendSmsAction: skipped — Twilio not configured', ['lead_id' => $lead->id, 'run_id' => $run->id]);
            return false;
        }

        $body = $this->interpolate($config['message'] ?? '', $lead);
        if (empty(trim($body))) {
            Log::info('SendSmsAction: skipped — empty message body', ['lead_id' => $lead->id, 'run_id' => $run->id]);
            return false;
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => $to,
                    'Body' => $body,
                ]);

            if ($response->successful()) {
                Log::info('SendSmsAction: sent', [
                    'lead_id'  => $lead->id,
                    'run_id'   => $run->id,
                    'twilio_sid' => $response->json('sid'),
                ]);
                return true;
            }

            Log::warning('SendSmsAction: Twilio API error', [
                'lead_id' => $lead->id,
                'run_id'  => $run->id,
                'error'   => $response->json('message', 'Unknown error'),
                'status'  => $response->status(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::warning('SendSmsAction: exception', [
                'lead_id' => $lead->id,
                'run_id'  => $run->id,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
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
            $template
        );
    }
}
