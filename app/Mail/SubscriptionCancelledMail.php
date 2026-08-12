<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancelledMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public ?string $endsAt;
    public ?string $reason;
    public string $reactivateUrl;

    public function __construct(Tenant $tenant, ?string $endsAt = null, ?string $reason = null)
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->endsAt        = $endsAt;
        $this->reason        = $reason;
        $this->reactivateUrl = \App\Support\AdminUrl::for('billing');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.subscription_cancelled_subject', ['workspace' => $this->workspaceName]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-cancelled');
    }
}
