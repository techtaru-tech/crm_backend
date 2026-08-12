<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class InstagramConnector extends MetaConnector
{
    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body   = json_decode($request->getContent(), true) ?? [];
        $leads  = [];

        $entries = $body['entry'] ?? [];

        foreach ($entries as $entry) {
            $messaging = $entry['messaging'] ?? [];
            foreach ($messaging as $message) {
                $senderId = $message['sender']['id'] ?? null;
                $text     = $message['message']['text'] ?? null;

                if (! $senderId || ! $text) {
                    continue;
                }

                $keywords = $connection->settings['qualification_keywords'] ?? [];
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

                $fakeLog = new WebhookLog(['source' => $connection->source]);
                $lead = $this->createLead([
                    'source_id'  => $senderId,
                    'first_name' => null,
                    'last_name'  => null,
                    'email'      => null,
                    'phone'      => null,
                    'raw_data'   => $message,
                ], $connection, $fakeLog);

                if ($lead) {
                    $leads[] = $lead;
                }
            }

            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                if (($change['field'] ?? '') === 'leadgen') {
                    $leadId = $change['value']['leadgen_id'] ?? null;
                    if ($leadId) {
                        $lead = $this->fetchLeadFromGraph($leadId, $connection);
                        if ($lead) {
                            $leads[] = $lead;
                        }
                    }
                }
            }
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'app_id'                 => self::field('instagram', 'app_id',                 'string', true,  'App ID'),
            'app_secret'             => self::field('instagram', 'app_secret',             'string', true,  'App Secret'),
            'page_access_token'      => self::field('instagram', 'page_access_token',      'string', true,  'Page Access Token'),
            'qualification_keywords' => self::field('instagram', 'qualification_keywords', 'array',  false, 'Qualification Keywords (comma-separated)'),
        ];
    }
}
