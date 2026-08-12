<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class PipedriveConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.pipedrive.com/v1';

    private function apiKey(): string
    {
        return $this->config['api_key'] ?? '';
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::timeout(10)->get("{$this->baseUrl}/persons", ['api_token' => $this->apiKey(), 'limit' => 1]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $personData = $this->applyFieldMapping([
            'name'  => trim("{$lead->first_name} {$lead->last_name}"),
            'email' => [['value' => $lead->email, 'primary' => true]],
            'phone' => $lead->phone ? [['value' => $lead->phone, 'primary' => true]] : [],
        ], $lead, $fieldMapping);
        $personRes = Http::timeout(15)->post("{$this->baseUrl}/persons?api_token={$this->apiKey()}", $personData);

        $personId = $personRes->json('data.id');
        $result   = ['person' => $personRes->json()];

        if ($personId && ($this->config['create_deal'] ?? true)) {
            // Deal title is visible to the operator inside Pipedrive, so
            // we route the "Lead: <name>" prefix through __() to respect
            // the operator's active script locale instead of leaking
            // English regardless of UI language.
            $dealRes = Http::timeout(15)->post("{$this->baseUrl}/leads?api_token={$this->apiKey()}", [
                'title'     => (string) __('integration_connectors.pipedrive.deal_title', [
                    'name' => trim("{$lead->first_name} {$lead->last_name}"),
                ]),
                'person_id' => $personId,
            ]);
            $result['deal'] = $dealRes->json();
        }

        return $result;
    }

    protected function connectorSlug(): string
    {
        return 'pipedrive';
    }

    public function getAvailableFields(): array
    {
        return [
            'name'     => $this->fieldLabel('name', 'Name'),
            'email'    => $this->fieldLabel('email', 'Email'),
            'phone'    => $this->fieldLabel('phone', 'Phone'),
            'org_name' => $this->fieldLabel('org_name', 'Organization'),
        ];
    }
}
