<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class VtigerConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return rtrim($this->config['instance_url'] ?? '', '/') . '/webservice.php';
    }

    private function sessionName(): ?string
    {
        return $this->config['access_token'] ?? $this->config['session_id'] ?? null;
    }

    public function testConnection(): bool
    {
        return ! empty($this->config['instance_url']) && ! empty($this->sessionName());
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $element = $this->applyFieldMapping([
            'firstname'  => $lead->first_name,
            'lastname'   => $lead->last_name ?: 'Unknown',
            'email'      => $lead->email,
            'phone'      => $lead->phone,
            'company'    => $lead->company ?: '-',
            'leadsource' => $lead->source,
        ], $lead, $fieldMapping);

        $res = Http::timeout(15)->post($this->baseUrl(), [
            'operation'   => 'create',
            'sessionName' => $this->sessionName(),
            'elementType' => 'Leads',
            'element'     => json_encode($element),
        ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }

    protected function connectorSlug(): string
    {
        return 'vtiger';
    }

    public function getAvailableFields(): array
    {
        return [
            'firstname'  => $this->fieldLabel('firstname', 'First Name'),
            'lastname'   => $this->fieldLabel('lastname', 'Last Name'),
            'email'      => $this->fieldLabel('email', 'Email'),
            'phone'      => $this->fieldLabel('phone', 'Phone'),
            'company'    => $this->fieldLabel('company', 'Company'),
            'leadsource' => $this->fieldLabel('leadsource', 'Lead Source'),
        ];
    }
}
