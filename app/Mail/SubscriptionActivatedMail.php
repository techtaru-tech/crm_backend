<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivatedMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public string $planName;
    public ?string $billingCycle;
    public string $manageUrl;

    public function __construct(Tenant $tenant, string $planName, ?string $billingCycle = null)
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->planName      = $planName;
        $this->billingCycle  = $billingCycle;
        $this->manageUrl     = \App\Support\AdminUrl::for('billing');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.subscription_activated_subject', ['plan' => $this->planName]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-activated');
    }
}
