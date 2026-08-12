<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalMagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly string $token,
    ) {}

    public function build(): self
    {
        $url = url('/portal/verify/' . $this->token);
        $tenantName = $this->lead->tenant?->name ?? config('app.name', 'LeadHub');

        return $this
            ->subject(__('mail.portal_magic_link_subject', ['app' => $tenantName]))
            ->view('emails.portal-magic-link', [
                'lead'       => $this->lead,
                'tenantName' => $tenantName,
                'url'        => $url,
            ]);
    }
}
