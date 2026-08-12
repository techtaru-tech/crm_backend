<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class ActiveCampaignConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        return rtrim($this->config['api_url'] ?? '', '/') . '/api/3';
    }

    private function headers(): array
    {
        return ['Api-Token' => $this->config['api_key'] ?? ''];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl()}/contacts?limit=1");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $contact = $this->applyFieldMapping(
            ['email' => $lead->email, 'firstName' => $lead->first_name, 'lastName' => $lead->last_name, 'phone' => $lead->phone],
            $lead,
            $fieldMapping
        );
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl()}/contacts", ['contact' => $contact]);

        $contactId = $res->json('contact.id');

        if ($contactId && ! empty($this->config['list_id'])) {
            Http::withHeaders($this->headers())->timeout(10)
                ->post("{$this->baseUrl()}/contactLists", [
                    'contactList' => [
                        'list'    => $this->config['list_id'],
                        'contact' => $contactId,
                        'status'  => 1,
                    ],
                ]);
        }

        if ($contactId && ! empty($this->config['tags'])) {
            $this->applyTags($contactId, $this->config['tags']);
        }

        return ['status_code' => $res->status(), 'contact_id' => $contactId, 'body' => $res->json()];
    }

    private function applyTags(int|string $contactId, string $tags): void
    {
        $tagNames = array_filter(array_map('trim', explode(',', $tags)));
        foreach ($tagNames as $tagName) {
            try {
                $tagRes = Http::withHeaders($this->headers())->timeout(10)
                    ->post("{$this->baseUrl()}/tags", ['tag' => ['tag' => $tagName, 'tagType' => 'contact']]);
                $tagId = $tagRes->json('tag.id') ?? null;

                if (! $tagId) {
                    $search = Http::withHeaders($this->headers())->timeout(10)
                        ->get("{$this->baseUrl()}/tags", ['search' => $tagName]);
                    $tagId = $search->json('tags.0.id') ?? null;
                }

                if ($tagId) {
                    Http::withHeaders($this->headers())->timeout(10)
                        ->post("{$this->baseUrl()}/contactTags", [
                            'contactTag' => ['contact' => $contactId, 'tag' => $tagId],
                        ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('connector tag-sync failed', [
                    'provider'    => 'activecampaign',
                    'contact_id'  => $contactId,
                    'error'       => $e->getMessage(),
                ]);
            }
        }
    }

    protected function connectorSlug(): string
    {
        return 'activecampaign';
    }

    public function getAvailableFields(): array
    {
        return [
            'email'     => $this->fieldLabel('email', 'Email'),
            'firstName' => $this->fieldLabel('firstName', 'First Name'),
            'lastName'  => $this->fieldLabel('lastName', 'Last Name'),
            'phone'     => $this->fieldLabel('phone', 'Phone'),
            'orgname'   => $this->fieldLabel('orgname', 'Organization Name'),
        ];
    }
}
