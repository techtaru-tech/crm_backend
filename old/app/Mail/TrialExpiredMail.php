<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiredMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public string $upgradeUrl;

    public function __construct(Tenant $tenant)
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->upgradeUrl    = \App\Support\AdminUrl::for('subscription-required');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.trial_expired_subject', ['workspace' => $this->workspaceName]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial-expired');
    }
}
