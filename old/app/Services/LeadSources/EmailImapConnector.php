<?php

namespace App\Services\LeadSources;

use App\Contracts\SourceConnector;
use App\Models\Lead;
use App\Models\LeadEmail;
use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use App\Services\LeadActivityService;
use App\Services\LeadIngestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;

class EmailImapConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        return true;
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        return [];
    }

    public function pollMailbox(LeadSourceConnection $connection): array
    {
        $credentials = $connection->credentials ?? [];
        $leads       = [];

        try {
            $cm = new ClientManager();
            $client = $cm->make([
                'host'          => $credentials['host'] ?? 'imap.gmail.com',
                'port'          => (int) ($credentials['port'] ?? 993),
                'encryption'    => ($credentials['ssl'] ?? true) ? 'ssl' : 'tls',
                'username'      => $credentials['username'] ?? '',
                'password'      => $credentials['password'] ?? '',
                'validate_cert' => false,
                'protocol'      => 'imap',
            ]);

            $client->connect();

            $folder   = $client->getFolder('INBOX');
            $messages = $folder->messages()->unseen()->limit(50)->get();

            $fakeLog = new WebhookLog([
                'source'              => 'email',
                'source_connection_id' => $connection->id,
                'tenant_id'           => $connection->tenant_id,
            ]);

            foreach ($messages as $message) {
                $from      = $message->getFrom()->first();
                $email     = $from?->mail ?? null;
                $fromName  = $from?->personal ?? '';
                $nameParts = $this->parseFullName($fromName);

                if (! $email) {
                    continue;
                }

                $email = strtolower(trim($email));

                // Extract threading headers (used to link replies to outbound emails)
                $inReplyTo  = $this->headerString($message, 'in_reply_to');
                $references = $this->headerString($message, 'references');
                $messageId  = $this->headerString($message, 'message_id');
                $subject    = (string) ($message->getSubject()->first() ?? '');
                $bodyHtml   = $this->safeBody($message, 'getHTMLBody');
                $bodyText   = $this->safeBody($message, 'getTextBody');

                // Normalise to bracket-free ids ("<abc@host>" -> "abc@host") so they
                // match the bracket-free message_id stored on our outbound emails.
                $candidateIds = array_values(array_filter(array_map(
                    fn ($id) => $this->normalizeMessageId($id),
                    array_merge([$inReplyTo], preg_split('/\s+/', $references ?? '') ?: [])
                )));

                $lead = null;

                // 1. Threading match: In-Reply-To / References → lead_emails.message_id
                if (! empty($candidateIds)) {
                    $match = LeadEmail::withoutGlobalScope('tenant')
                        ->where('tenant_id', $connection->tenant_id)
                        ->whereIn('message_id', $candidateIds)
                        ->first();
                    if ($match) {
                        $lead = Lead::withoutGlobalScope('tenant')->find($match->lead_id);
                    }
                }

                // 2. Fallback: match on from_address → existing lead in tenant
                if (! $lead) {
                    $lead = Lead::withoutGlobalScope('tenant')
                        ->where('tenant_id', $connection->tenant_id)
                        ->where('email', $email)
                        ->first();
                }

                // 3. No match — create the lead via the normal ingestion pipeline.
                if (! $lead) {
                    $lead = $this->createLead([
                        'source_id'  => $message->getUid(),
                        'first_name' => $nameParts['first_name'],
                        'last_name'  => $nameParts['last_name'],
                        'email'      => $email,
                        'phone'      => null,
                        'raw_data'   => [
                            'subject' => $subject,
                            'from'    => $email,
                            'date'    => $message->getDate()->first()?->toString(),
                        ],
                    ], $connection, $fakeLog);
                }

                if ($lead) {
                    // Record the inbound email on the conversation thread.
                    LeadEmail::create([
                        'tenant_id'    => $connection->tenant_id,
                        'lead_id'      => $lead->id,
                        'user_id'      => null,
                        'direction'    => 'inbound',
                        'message_id'   => $this->normalizeMessageId($messageId),
                        'in_reply_to'  => $this->normalizeMessageId($inReplyTo),
                        'subject'      => $subject ?: '(no subject)',
                        'body_html'    => $bodyHtml,
                        'body_text'    => $bodyText ?: ($bodyHtml ? trim(strip_tags($bodyHtml)) : null),
                        'from_address' => $email,
                        'from_name'    => $fromName ?: null,
                        'to_addresses' => $this->collectAddresses($message, 'getTo'),
                        'cc_addresses' => $this->collectAddresses($message, 'getCc'),
                        'sent_at'      => optional($message->getDate()->first())->toDateTime()
                            ? \Carbon\Carbon::parse($message->getDate()->first()?->toString())
                            : now(),
                    ]);

                    // Log the reply on the lead's Activity Timeline so it surfaces
                    // EVERYWHERE (not just the Conversations panel) and flips the lead's
                    // "Waiting on" indicator to us. Without this the inbound LeadEmail row
                    // existed but the timeline / waiting-on never moved — which read to
                    // users as "the reply was never added to the customer".
                    app(LeadActivityService::class)->logEmailReceived(
                        $lead,
                        $subject ?: '(no subject)',
                        $email,
                    );

                    $leads[] = $lead;
                    $message->setFlag('Seen');
                }
            }

            $client->disconnect();

            // Stamp connection health on a successful poll so the operator can see the
            // IMAP integration is alive (the buyer docs tell users to look for a recent
            // timestamp / "connected" status). Previously the poll only ever called
            // markError() on failure, so a *working* mailbox looked dead in the UI.
            if (! empty($leads)) {
                $connection->markReceived();   // last_received_at = now, status = connected
            } else {
                $connection->markConnected();  // status = connected, clears last_error
            }
        } catch (\Throwable $e) {
            Log::error('IMAP polling failed', [
                'connection_id' => $connection->id,
                'error'         => $e->getMessage(),
            ]);
            $connection->markError($e->getMessage());
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'host'     => self::field('email_imap', 'host',     'string',  true,  'IMAP Host'),
            'port'     => self::field('email_imap', 'port',     'integer', true,  'IMAP Port (993 for SSL)'),
            'username' => self::field('email_imap', 'username', 'string',  true,  'Email Username'),
            'password' => self::field('email_imap', 'password', 'string',  true,  'Email Password'),
            'ssl'      => self::field('email_imap', 'ssl',      'boolean', false, 'Use SSL'),
        ];
    }

    public function testConnection(LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];

        try {
            $cm = new ClientManager();
            $client = $cm->make([
                'host'          => $credentials['host'] ?? 'imap.gmail.com',
                'port'          => (int) ($credentials['port'] ?? 993),
                'encryption'    => ($credentials['ssl'] ?? true) ? 'ssl' : 'tls',
                'username'      => $credentials['username'] ?? '',
                'password'      => $credentials['password'] ?? '',
                'validate_cert' => false,
                'protocol'      => 'imap',
            ]);

            $client->connect();
            $client->disconnect();

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Normalise a Message-ID / In-Reply-To token to its bracket-free core, so
     * inbound reply ids match the bracket-free ids stored on outbound emails.
     * "<abc@host>" -> "abc@host"; null / empty -> null.
     */
    protected function normalizeMessageId(?string $id): ?string
    {
        if ($id === null) {
            return null;
        }
        $id = trim(trim($id), '<> ');

        return $id !== '' ? $id : null;
    }

    protected function headerString($message, string $key): ?string
    {
        try {
            $header = $message->getHeader();
            if (! $header) {
                return null;
            }
            $value = $header->get($key);
            if (is_object($value) && method_exists($value, 'toString')) {
                return trim($value->toString());
            }
            if (is_array($value)) {
                return trim(implode(' ', array_map(fn($v) => (string) $v, $value)));
            }
            return $value ? trim((string) $value) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function safeBody($message, string $method): ?string
    {
        try {
            $body = $message->{$method}();
            return is_string($body) && $body !== '' ? $body : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function collectAddresses($message, string $method): array
    {
        try {
            $list = $message->{$method}();
            if (! $list) {
                return [];
            }
            $out = [];
            foreach ($list as $entry) {
                if (! empty($entry->mail)) {
                    $out[] = strtolower(trim($entry->mail));
                }
            }
            return $out;
        } catch (\Throwable) {
            return [];
        }
    }
}
