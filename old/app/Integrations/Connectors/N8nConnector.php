<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class N8nConnector extends BaseConnector
{
    public function testConnection(): bool
    {
        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) return false;
        try {
            $res = Http::timeout(10)->post($url, ['_test' => true]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];
        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.n8n', 'n8n webhook URL not configured.'));
        $res = Http::timeout(15)->post($url, $this->leadToPayload($lead, $fieldMapping));
        return ['status_code' => $res->status(), 'body' => $res->json() ?? $res->body()];
    }
}
