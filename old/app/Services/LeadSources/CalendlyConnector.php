<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class CalendlyConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $secret      = $credentials['webhook_signing_key'] ?? null;

        if (! $secret) {
            return true;
        }

        $signature = $request->header('Calendly-Webhook-Signature');
        if (! $signature) {
            return false;
        }

        $parts    = explode(',', $signature);
        $t        = null;
        $v1       = null;

        foreach ($parts as $part) {
            [$key, $value] = explode('=', $part, 2);
            if ($key === 't') {
                $t = $value;
            }
            if ($key === 'v1') {
                $v1 = $value;
            }
        }

        if (! $t || ! $v1) {
            return false;
        }

        $payload  = $t . '.' . $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $v1);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];
        $event = $body['event'] ?? '';

        if ($event !== 'invitee.created') {
            return $leads;
        }

        $payload   = $body['payload'] ?? [];
        $invitee   = $payload['invitee'] ?? $payload;
        $questions = $payload['questions_and_answers'] ?? [];

        $name      = $invitee['name'] ?? '';
        $email     = $invitee['email'] ?? null;
        $nameParts = $this->parseFullName($name);

        $customFields = [];
        foreach ($questions as $qa) {
            $customFields[$qa['question'] ?? ''] = $qa['answer'] ?? null;
        }

        $lead = $this->createLead([
            'source_id'    => $invitee['uri'] ?? $invitee['uuid'] ?? null,
            'first_name'   => $invitee['first_name'] ?? $nameParts['first_name'],
            'last_name'    => $invitee['last_name'] ?? $nameParts['last_name'],
            'email'        => $email,
            'phone'        => null,
            'raw_data'     => $body,
            'custom_fields' => array_merge([
                'event_name'  => $payload['event_type']['name'] ?? null,
                'start_time'  => $payload['scheduled_event']['start_time'] ?? null,
            ], $customFields),
        ], $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'personal_access_token' => self::field('calendly', 'personal_access_token', 'string', true,  'Personal Access Token'),
            'webhook_signing_key'   => self::field('calendly', 'webhook_signing_key',   'string', false, 'Webhook Signing Key'),
            'organization_uri'      => self::field('calendly', 'organization_uri',      'string', false, 'Organization URI'),
        ];
    }
}
