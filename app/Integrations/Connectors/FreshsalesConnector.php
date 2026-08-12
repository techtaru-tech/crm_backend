<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class FreshsalesConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return 'https://' . ($this->config['domain'] ?? '') . '.freshsales.io/api';
    }

    private function headers(): array
    {
        return ['Authorization' => 'Token token=' . ($this->config['api_key'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl()}/contacts?per_page=1");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $contact = $this->applyFieldMapping(
            ['first_name' => $lead->first_name, 'last_name' => $lead->last_name, 'email' => $lead->email, 'mobile_number' => $lead->phone],
            $lead, $fieldMapping
        );

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl()}/contacts", ['contact' => $contact]);

        $contactId = $res->json('contact.id');
        $dealId    = null;

        if ($contactId && ($this->config['create_deal'] ?? true)) {
            $dealId = $this->createDeal($lead, $contactId);
        }

        return ['status_code' => $res->status(), 'contact_id' => $contactId, 'deal_id' => $dealId, 'body' => $res->json()];
    }

    private function createDeal(Lead $lead, int $contactId): ?int
    {
        try {
            $dealName = trim("{$lead->first_name} {$lead->last_name}") . ' — ' . ($lead->company ?? 'Lead');

            $payload = [
                'name'              => $dealName,
                'deal_stage_id'     => $this->config['deal_stage_id'] ?? null,
                'deal_pipeline_id'  => $this->config['deal_pipeline_id'] ?? null,
                'contacts'          => [['id' => $contactId]],
                'expected_close'    => now()->addDays(30)->toDateString(),
            ];

            $payload = array_filter($payload, fn($v) => $v !== null);

            $res = Http::withHeaders($this->headers())->timeout(15)
                ->post("{$this->baseUrl()}/deals", ['deal' => $payload]);

            return $res->json('deal.id');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function connectorSlug(): string
    {
        return 'freshsales';
    }

    public function getAvailableFields(): array
    {
        return [
            'first_name'    => $this->fieldLabel('first_name', 'First Name'),
            'last_name'     => $this->fieldLabel('last_name', 'Last Name'),
            'email'         => $this->fieldLabel('email', 'Email'),
            'mobile_number' => $this->fieldLabel('mobile_number', 'Mobile Number'),
            'work_number'   => $this->fieldLabel('work_number', 'Work Number'),
            'job_title'     => $this->fieldLabel('job_title', 'Job Title'),
            'company'       => $this->fieldLabel('company', 'Company'),
            'city'          => $this->fieldLabel('city', 'City'),
            'country'       => $this->fieldLabel('country', 'Country'),
        ];
    }
}
