<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Support\UnsafeUrlException;
use App\Support\UrlSafetyGuard;
use Illuminate\Support\Facades\Http;

class SendSlackAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $webhookUrl = $config['webhook_url'] ?? null;
        if (! $webhookUrl) return false;

        // SSRF guard: tenant-supplied URL must not target a private,
        // loopback, or metadata IP.  See app/Support/UrlSafetyGuard.
        try {
            UrlSafetyGuard::assertSafe((string) $webhookUrl);
        } catch (UnsafeUrlException $e) {
            logger()->warning('SendSlackAction blocked by SSRF guard', [
                'lead_id' => $lead->id,
                'run_id'  => $run->id,
                'url'     => $webhookUrl,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }

        $message  = $config['message'] ?? __('automation_runs.defaults.slack_message', ['lead' => $lead->full_name]);
        $template = str_replace(
            ['{{lead.first_name}}', '{{lead.last_name}}', '{{lead.email}}', '{{lead.source}}', '{{lead.status}}'],
            [$lead->first_name, $lead->last_name, $lead->email, $lead->source, $lead->status?->value ?? ''],
            $message
        );

        try {
            Http::timeout(10)->post($webhookUrl, ['text' => $template]);
            return true;
        } catch (\Throwable $e) {
            logger()->warning('SendSlackAction failed', [
                'lead_id' => $lead->id,
                'run_id'  => $run->id,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }
}
