<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class KlaviyoConnector extends BaseConnector
{
    private string $baseUrl = 'https://a.klaviyo.com/api';

    private function headers(): array
    {
        return [
            'Authorization' => 'Klaviyo-API-Key ' . ($this->config['api_key'] ?? ''),
            'revision'      => '2023-12-15',
        ];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/lists");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $attributes = $this->applyFieldMapping(
            ['email' => $lead->email, 'first_name' => $lead->first_name, 'last_name' => $lead->last_name, 'phone_number' => $lead->phone],
            $lead,
            $fieldMapping
        );
        $profileRes = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/profiles/", [
                'data' => ['type' => 'profile', 'attributes' => $attributes],
            ]);

        $profileId = $profileRes->json('data.id');
        if ($profileId && ! empty($this->config['list_id'])) {
            Http::withHeaders($this->headers())->timeout(10)
                ->post("{$this->baseUrl}/lists/{$this->config['list_id']}/relationships/profiles/", [
                    'data' => [['type' => 'profile', 'id' => $profileId]],
                ]);
        }

        return ['status_code' => $profileRes->status(), 'body' => $profileRes->json()];
    }
}
