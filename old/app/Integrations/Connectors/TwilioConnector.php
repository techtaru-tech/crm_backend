<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class TwilioConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.twilio.com/2010-04-01';

    private function auth(): array
    {
        return [$this->config['account_sid'] ?? '', $this->config['auth_token'] ?? ''];
    }

    public function testConnection(): bool
    {
        try {
            $sid = $this->config['account_sid'] ?? '';
            $res = Http::withBasicAuth(...$this->auth())->timeout(10)
                ->get("{$this->baseUrl}/Accounts/{$sid}.json");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $phone = $lead->phone ?? '';
        if (empty($phone)) return ['skipped' => true, 'reason' => 'no_phone'];

        $sid      = $this->config['account_sid'] ?? '';
        $template = $this->config['sms_template'] ?? (string) __('integration_messaging_templates.sms.twilio_default');
        $body     = $this->interpolateTemplate($template, $lead);

        $res = Http::withBasicAuth(...$this->auth())->timeout(15)
            ->asForm()
            ->post("{$this->baseUrl}/Accounts/{$sid}/Messages.json", [
                'To'   => $phone,
                'From' => $this->config['from_number'] ?? '',
                'Body' => $body,
            ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
