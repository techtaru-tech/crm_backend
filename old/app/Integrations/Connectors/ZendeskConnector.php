<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class ZendeskConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return 'https://' . ($this->config['subdomain'] ?? '') . '.zendesk.com/api/v2';
    }

    private function auth(): array
    {
        $token = $this->config['api_token_zendesk'] ?? $this->config['api_token'] ?? '';
        return [($this->config['email'] ?? '') . '/token', $token];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withBasicAuth(...$this->auth())->timeout(10)->get("{$this->baseUrl()}/users/me");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $createTicket = (bool) ($this->config['create_ticket'] ?? false);

        if ($createTicket) {
            $subjectTemplate = $this->config['ticket_subject_template'] ?? null;
            $subject = $subjectTemplate
                ? $this->interpolateTemplate($subjectTemplate, $lead)
                : "New Lead: {$lead->first_name} {$lead->last_name}";

            $body = "Email: {$lead->email}\nPhone: {$lead->phone}\nCompany: {$lead->company}\nSource: {$lead->source}";

            $res = Http::withBasicAuth(...$this->auth())->timeout(15)
                ->post("{$this->baseUrl()}/tickets", [
                    'ticket' => [
                        'subject' => $subject,
                        'comment' => ['body' => $body],
                        'requester' => ['name' => trim("{$lead->first_name} {$lead->last_name}"), 'email' => $lead->email],
                    ],
                ]);
        } else {
            $userData = $this->applyFieldMapping(
                [
                    'name'  => trim("{$lead->first_name} {$lead->last_name}"),
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'role'  => 'end-user',
                ],
                $lead,
                $fieldMapping
            );

            $res = Http::withBasicAuth(...$this->auth())->timeout(15)
                ->post("{$this->baseUrl()}/users", ['user' => $userData]);
        }

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }

    protected function connectorSlug(): string
    {
        return 'zendesk';
    }

    public function getAvailableFields(): array
    {
        return [
            'name'         => $this->fieldLabel('name', 'Full Name'),
            'email'        => $this->fieldLabel('email', 'Email'),
            'phone'        => $this->fieldLabel('phone', 'Phone'),
            'organization' => $this->fieldLabel('organization', 'Organization'),
            'external_id'  => $this->fieldLabel('external_id', 'External ID'),
            'details'      => $this->fieldLabel('details', 'Details'),
            'notes'        => $this->fieldLabel('notes', 'Notes'),
        ];
    }
}
