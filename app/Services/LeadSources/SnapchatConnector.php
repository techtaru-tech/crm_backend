<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SnapchatConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $secret      = $credentials['app_secret'] ?? null;

        if (! $secret) {
            return true;
        }

        $signature = $request->header('X-Snapchat-Signature');
        $payload   = $request->getContent();

        if (! $signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        $leadEvents = $body['events'] ?? [$body];

        foreach ($leadEvents as $event) {
            $leadData = $event['data'] ?? $event;
            $fields   = collect($leadData['fields'] ?? [])
                ->keyBy('field_name')
                ->map(fn ($f) => $f['value'] ?? null)
                ->all();

            $fullName  = $fields['full_name'] ?? '';
            $nameParts = $this->parseFullName($fullName);

            $lead = $this->createLead([
                'source_id'  => $leadData['lead_id'] ?? $event['event_id'] ?? null,
                'first_name' => $fields['first_name'] ?? $nameParts['first_name'],
                'last_name'  => $fields['last_name'] ?? $nameParts['last_name'],
                'email'      => $fields['email'] ?? null,
                'phone'      => $this->normalizePhone($fields['phone_number'] ?? null),
                'raw_data'   => $event,
                'custom_fields' => array_diff_key($fields, array_flip([
                    'full_name', 'first_name', 'last_name', 'email', 'phone_number',
                ])),
            ], $connection, $log);

            if ($lead) {
                $leads[] = $lead;
            }
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'client_id'     => self::field('snapchat', 'client_id',     'string', true, 'Client ID'),
            'client_secret' => self::field('snapchat', 'client_secret', 'string', true, 'Client Secret'),
            'app_secret'    => self::field('snapchat', 'app_secret',    'string', true, 'App Secret (for signature verification)'),
            'access_token'  => self::field('snapchat', 'access_token',  'string', true, 'Access Token'),
        ];
    }
}
