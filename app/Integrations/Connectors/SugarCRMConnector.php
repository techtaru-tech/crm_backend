<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class SugarCRMConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return rtrim($this->config['instance_url'] ?? '', '/') . '/rest/v11_1';
    }

    private function headers(): array
    {
        return ['OAuth-Token' => $this->config['access_token'] ?? ''];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)
                ->get("{$this->baseUrl()}/Leads", ['max_num' => 1]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $payload = $this->applyFieldMapping([
            'first_name'   => $lead->first_name,
            'last_name'    => $lead->last_name ?: 'Unknown',
            'email1'       => $lead->email,
            'phone_work'   => $lead->phone,
            'account_name' => $lead->company,
            'lead_source'  => $lead->source,
        ], $lead, $fieldMapping);
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl()}/Leads", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
