<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\LeadEmail;
use App\Services\LeadActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendLeadEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $leadId,
        public string $subject,
        public string $body,
        public string $recipient,
        public int $sentByUserId,
        public array $attachments = [],
        // Optional explicit tenant id — when set, the queue's
        // before-job hook in AppServiceProvider binds `current_tenant`
        // for this job's lifetime so the BelongsToTenant global scope
        // resolves correctly inside an async worker (where there is
        // no auth user / ResolveTenant middleware to set it).
        public ?int $tenantId = null,
        // AI SDR provenance. When true, the persisted LeadEmail row is
        // stamped ai_sdr=true (+ the enrollment id) so the conversation
        // thread can badge agent-sent emails and — critically — so the
        // human-reply pause can tell the agent's OWN sends apart from a
        // rep's. The pause itself keys on direction='inbound', so an
        // outbound agent send never pauses the agent.
        public bool $aiSdr = false,
        public ?int $aiSdrEnrollmentId = null,
    ) {}

    public function handle(LeadActivityService $activityService): void
    {
        $lead = Lead::with('tenant')->find($this->leadId);
        if (! $lead) {
            return;
        }

        $tenant   = $lead->tenant;
        $settings = $tenant->getSetting('smtp', []);

        // Pre-allocate a tracking token so the pixel/click URLs match the saved row.
        $trackingToken = Str::random(32);

        // Interpolate lead tokens, then wrap the body in the tenant's
        // branded email template (gradient header, logo, footer) so
        // every sequence / automation / manual send goes out with the
        // tenant's identity.  Previously only the raw body was sent,
        // which made emails look unstyled and left {first_name} stubs
        // unsubstituted when seeded sequences used a mismatched token
        // syntax.
        $renderer = app(\App\Services\BrandedEmailRenderer::class);
        $ctx = [
            'first_name' => $lead->first_name,
            'last_name'  => $lead->last_name,
            'full_name'  => $lead->full_name,
            'email'      => $lead->email,
            'company'    => $lead->company,
            'phone'      => $lead->phone,
        ];
        $interpolatedBody = $renderer->interpolate($this->body, $ctx);
        $brandedBody      = $renderer->wrap($tenant, $interpolatedBody, $this->subject);
        $trackedBody      = $this->injectTracking($brandedBody, $trackingToken);

        $fromAddress = $settings['from_address'] ?? config('mail.from.address');
        $fromName    = $settings['from_name']    ?? ($tenant->name ?? config('mail.from.name'));

        // Pre-generate a stable Message-ID so the outbound LeadEmail row stores the
        // exact id we put on the wire. Inbound replies carry it in In-Reply-To /
        // References, which EmailImapConnector matches against lead_emails.message_id
        // to thread the reply onto THIS lead (instead of only matching the sender's
        // address, which misses alias/forwarded replies and can mint duplicate leads).
        // Stored bracket-free; the inbound matcher normalises both sides.
        $messageHost = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'leadhub.local';
        $messageId   = (string) Str::uuid() . '@' . $messageHost;

        try {
            if (! empty($settings['host'])) {
                $password = '';
                if (! empty($settings['password'])) {
                    try {
                        $password = decrypt($settings['password']);
                    } catch (\Throwable) {
                        $password = $settings['password'];
                    }
                }

                Config::set('mail.mailers.tenant_smtp', [
                    'transport'  => 'smtp',
                    'host'       => $settings['host'],
                    'port'       => $settings['port'] ?? 587,
                    'username'   => $settings['username'] ?? null,
                    'password'   => $password,
                    'encryption' => $settings['encryption'] ?? 'tls',
                ]);

                if (! empty($settings['from_address'])) {
                    Config::set('mail.from', [
                        'address' => $settings['from_address'],
                        'name'    => $settings['from_name'] ?? $tenant->name,
                    ]);
                }

                $mailer = Mail::mailer('tenant_smtp');
            } else {
                $mailer = Mail::mailer('smtp');
            }

            $mailer->send([], [], function (Message $message) use ($trackedBody, $messageId) {
                // Put our own Message-ID on the wire so replies thread back (see above).
                // Remove any pre-set one first to avoid a duplicate header; Symfony wraps
                // the value in angle brackets when rendering.
                $headers = $message->getSymfonyMessage()->getHeaders();
                $headers->remove('Message-ID');
                $headers->addIdHeader('Message-ID', $messageId);

                $msg = $message->to($this->recipient)->subject($this->subject)->html($trackedBody);
                foreach ($this->attachments as $path) {
                    if (file_exists($path)) {
                        $msg->attach($path);
                    }
                }
            });

            // Persist a LeadEmail record so the conversation thread & tracking work.
            $attachmentMeta = [];
            foreach ($this->attachments as $path) {
                if (file_exists($path)) {
                    $attachmentMeta[] = [
                        'filename' => basename($path),
                        'size'     => @filesize($path) ?: null,
                        'path'     => $path,
                    ];
                }
            }

            LeadEmail::create([
                'tenant_id'      => $lead->tenant_id,
                'lead_id'        => $lead->id,
                'user_id'        => $this->sentByUserId,
                'direction'      => 'outbound',
                'ai_sdr'              => $this->aiSdr,
                'ai_sdr_enrollment_id' => $this->aiSdrEnrollmentId,
                'message_id'     => $messageId,
                'subject'        => $this->subject,
                'body_html'      => $trackedBody,
                'body_text'      => trim(strip_tags($this->body)) ?: null,
                'from_address'   => $fromAddress ?: 'unknown@localhost',
                'from_name'      => $fromName,
                'to_addresses'   => [$this->recipient],
                'attachments'    => $attachmentMeta ?: null,
                'sent_at'        => now(),
                'tracking_token' => $trackingToken,
            ]);

            // Pass the original sender id explicitly to the activity
            // logger.  Previously this called `auth()->loginUsingId()`
            // to make `auth()->id()` resolve to the sender — but on
            // QUEUE_CONNECTION=sync that swap mutates the *outer*
            // request's auth guard mid-flight, attributing the rest
            // of the request (audit logs, rate-limit checks) to the
            // wrong user.  An explicit userId param avoids the swap.
            $activityService->logEmailSent(
                $lead,
                $this->subject,
                $this->recipient,
                $this->sentByUserId,
            );

        } catch (\Throwable $e) {
            Log::error('SendLeadEmail failed', ['lead_id' => $this->leadId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Insert a 1x1 tracking pixel and rewrite http(s) anchor hrefs to go
     * through the click-tracking endpoint. Falls back to appending the pixel
     * if no </body> tag is present.
     */
    protected function injectTracking(string $html, string $token): string
    {
        $baseUrl = rtrim(config('app.url') ?: url('/'), '/');
        $pixel   = '<img src="' . $baseUrl . '/emails/track/open/' . $token . '" width="1" height="1" alt="" style="display:none" />';

        // Rewrite http/https hrefs through the click redirect.
        $html = preg_replace_callback(
            '/href\s*=\s*(["\'])(https?:\/\/[^"\']+)\1/i',
            function (array $m) use ($baseUrl, $token) {
                $quote  = $m[1];
                $orig   = $m[2];
                $rewrite = $baseUrl . '/emails/track/click/' . $token . '?to=' . urlencode($orig);
                return 'href=' . $quote . $rewrite . $quote;
            },
            $html
        ) ?? $html;

        if (stripos($html, '</body>') !== false) {
            return preg_replace('/<\/body>/i', $pixel . '</body>', $html, 1) ?? ($html . $pixel);
        }

        return $html . $pixel;
    }
}
