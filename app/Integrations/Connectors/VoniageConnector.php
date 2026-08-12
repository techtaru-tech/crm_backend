<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class VoniageConnector extends BaseConnector
{
    private string $apiUrl = 'https://rest.nexmo.com/sms/json';

    public function testConnection(): bool
    {
        return ! empty($this->config['api_key']) && ! empty($this->config['api_secret']);
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $phone = $lead->phone ?? '';
        if (empty($phone)) return ['skipped' => true, 'reason' => 'no_phone'];

        $template = $this->config['sms_template'] ?? (string) __('integration_messaging_templates.sms.voniage_default');
        $body     = $this->interpolateTemplate($template, $lead);

        $res = Http::timeout(15)->post($this->apiUrl, [
            'api_key'    => $this->config['api_key'] ?? '',
            'api_secret' => $this->config['api_secret'] ?? '',
            'to'         => preg_replace('/\D/', '', $phone),
            'from'       => $this->config['from_number'] ?? 'LeadHub',
            'text'       => $body,
        ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
