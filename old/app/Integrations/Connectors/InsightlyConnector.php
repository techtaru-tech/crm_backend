<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class InsightlyConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.na1.insightly.com/v3.1';

    private function headers(): array
    {
        return ['Authorization' => 'Basic ' . base64_encode($this->config['api_key'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/Contacts?top=1");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $payload = $this->applyFieldMapping([
            'FIRST_NAME'        => $lead->first_name,
            'LAST_NAME'         => $lead->last_name ?: 'Unknown',
            'EMAIL_ADDRESS'     => $lead->email,
            'PHONE'             => $lead->phone,
            'ORGANISATION_NAME' => $lead->company,
            'LEAD_SOURCE'       => $lead->source,
        ], $lead, $fieldMapping);
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/Leads", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
