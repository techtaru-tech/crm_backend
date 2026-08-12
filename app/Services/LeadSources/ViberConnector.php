<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class ViberConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $authToken   = $credentials['auth_token'] ?? null;

        if (! $authToken) {
            return true;
        }

        $signature = $request->header('X-Viber-Content-Signature');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $authToken);

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body     = json_decode($request->getContent(), true) ?? [];
        $leads    = [];
        $keywords = array_map('strtolower', $connection->settings['qualification_keywords'] ?? []);
        $event    = $body['event'] ?? '';

        if (! in_array($event, ['message', 'conversation_started'])) {
            return $leads;
        }

        $sender = $body['sender'] ?? [];
        $text   = $body['message']['text'] ?? '';

        if ($event === 'message' && ! empty($keywords)) {
            $matched = false;
            foreach ($keywords as $kw) {
                if (stripos($text, $kw) !== false) {
                    $matched = true;
                    break;
                }
            }
            if (! $matched) {
                return $leads;
            }
        }

        $nameParts = $this->parseFullName($sender['name'] ?? '');

        $lead = $this->createLead([
            'source_id'    => $sender['id'] ?? null,
            'first_name'   => $nameParts['first_name'],
            'last_name'    => $nameParts['last_name'],
            'email'        => null,
            'phone'        => null,
            'raw_data'     => $body,
            'custom_fields' => ['message' => $text],
        ], $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'auth_token'             => self::field('viber', 'auth_token',             'string', true,  'Auth Token'),
            'bot_name'               => self::field('viber', 'bot_name',               'string', false, 'Bot Name'),
            'qualification_keywords' => self::field('viber', 'qualification_keywords', 'array',  false, 'Qualification Keywords'),
        ];
    }
}
