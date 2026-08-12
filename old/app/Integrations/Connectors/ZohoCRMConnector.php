<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class ZohoCRMConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return 'https://www.zohoapis.' . ($this->config['region'] ?? 'com') . '/crm/v3';
    }

    private function headers(): array
    {
        return ['Authorization' => 'Zoho-oauthtoken ' . ($this->config['access_token'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl()}/Leads?per_page=1");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $data = $this->applyFieldMapping([
            'First_Name'  => $lead->first_name,
            'Last_Name'   => $lead->last_name ?: 'Unknown',
            'Email'       => $lead->email,
            'Phone'       => $lead->phone,
            'Company'     => $lead->company ?: '-',
            'Lead_Source' => $lead->source,
        ], $lead, $fieldMapping);

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl()}/Leads", ['data' => [$data]]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }

    protected function connectorSlug(): string
    {
        return 'zoho_crm';
    }

    public function getAvailableFields(): array
    {
        return [
            'First_Name'  => $this->fieldLabel('first_name', 'First Name'),
            'Last_Name'   => $this->fieldLabel('last_name', 'Last Name'),
            'Email'       => $this->fieldLabel('email', 'Email'),
            'Phone'       => $this->fieldLabel('phone', 'Phone'),
            'Company'     => $this->fieldLabel('company', 'Company'),
            'Lead_Source' => $this->fieldLabel('lead_source', 'Lead Source'),
            'City'        => $this->fieldLabel('city', 'City'),
            'Country'     => $this->fieldLabel('country', 'Country'),
        ];
    }
}
