<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Support\UnsafeUrlException;
use App\Support\UrlSafetyGuard;
use Illuminate\Support\Facades\Http;

class SendWebhookAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $url = $config['url'] ?? null;
        if (! $url) return false;

        // SSRF guard: tenant-supplied URL must not target a private,
        // loopback, or metadata IP.  See app/Support/UrlSafetyGuard.
        try {
            UrlSafetyGuard::assertSafe((string) $url);
        } catch (UnsafeUrlException $e) {
            logger()->warning('SendWebhookAction blocked by SSRF guard', [
                'url'    => $url,
                'run_id' => $run->id,
                'error'  => $e->getMessage(),
            ]);
            return false;
        }

        try {
            $payload = [
                'event'     => 'automation.triggered',
                'lead'      => [
                    'id'         => $lead->id,
                    'email'      => $lead->email,
                    'first_name' => $lead->first_name,
                    'last_name'  => $lead->last_name,
                    'phone'      => $lead->phone,
                    'source'     => $lead->source,
                    'status'     => $lead->status?->value,
                ],
                'automation_run_id' => $run->id,
                'timestamp'         => now()->toIso8601String(),
            ];

            $headers = [];
            if (! empty($config['secret'])) {
                $headers['X-LeadHub-Signature'] = hash_hmac('sha256', json_encode($payload), $config['secret']);
            }

            Http::withHeaders($headers)->timeout(10)->post($url, $payload);
            return true;
        } catch (\Throwable $e) {
            logger()->warning('SendWebhookAction failed', [
                'url'    => $url,
                'run_id' => $run->id,
                'error'  => $e->getMessage(),
            ]);
            return false;
        }
    }
}
