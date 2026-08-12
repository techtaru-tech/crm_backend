<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class DripConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return 'https://api.getdrip.com/v2/' . ($this->config['account_id'] ?? '');
    }

    private function headers(): array
    {
        return ['Authorization' => 'Basic ' . base64_encode($this->config['api_token'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl()}/subscribers");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $subscriber = $this->applyFieldMapping(
            ['email' => $lead->email, 'first_name' => $lead->first_name, 'last_name' => $lead->last_name],
            $lead, $fieldMapping
        );
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl()}/subscribers", ['subscribers' => [$subscriber]]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
