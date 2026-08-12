<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WhatsAppConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $appSecret   = $credentials['app_secret'] ?? null;

        if (! $appSecret) {
            return true;
        }

        $signature = $request->header('X-Hub-Signature-256');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body       = json_decode($request->getContent(), true) ?? [];
        $leads      = [];
        $keywords   = array_map('strtolower', $connection->settings['qualification_keywords'] ?? []);

        $entries = $body['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $messages = $change['value']['messages'] ?? [];
                $contacts = collect($change['value']['contacts'] ?? [])->keyBy('wa_id')->all();

                foreach ($messages as $message) {
                    $from    = $message['from'] ?? null;
                    $text    = $message['text']['body'] ?? '';
                    $msgType = $message['type'] ?? 'text';

                    if ($msgType !== 'text') {
                        continue;
                    }

                    if (! empty($keywords)) {
                        $matched = false;
                        foreach ($keywords as $kw) {
                            if (stripos($text, $kw) !== false) {
                                $matched = true;
                                break;
                            }
                        }
                        if (! $matched) {
                            continue;
                        }
                    }

                    $contact    = $contacts[$from] ?? null;
                    $profileName = $contact['profile']['name'] ?? '';
                    $nameParts  = $this->parseFullName($profileName);

                    $lead = $this->createLead([
                        'source_id'  => $message['id'] ?? $from,
                        'first_name' => $nameParts['first_name'],
                        'last_name'  => $nameParts['last_name'],
                        'email'      => null,
                        'phone'      => $this->normalizePhone('+' . $from),
                        'raw_data'   => $message,
                        'custom_fields' => ['message' => $text],
                    ], $connection, $log);

                    if ($lead) {
                        $leads[] = $lead;
                    }
                }
            }
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'app_id'                 => self::field('whatsapp', 'app_id',                 'string', true,  'App ID'),
            'app_secret'             => self::field('whatsapp', 'app_secret',             'string', true,  'App Secret'),
            'phone_number_id'        => self::field('whatsapp', 'phone_number_id',        'string', true,  'Phone Number ID'),
            'access_token'           => self::field('whatsapp', 'access_token',           'string', true,  'Access Token'),
            'qualification_keywords' => self::field('whatsapp', 'qualification_keywords', 'array',  false, 'Qualification Keywords'),
        ];
    }
}
