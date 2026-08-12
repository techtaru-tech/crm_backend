<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingSoonMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public int $daysRemaining;
    public string $trialEndsAt;
    public string $upgradeUrl;

    public function __construct(Tenant $tenant, int $daysRemaining)
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->daysRemaining = $daysRemaining;
        $this->trialEndsAt   = $tenant->trial_ends_at?->translatedFormat('F j, Y') ?? '';
        $this->upgradeUrl    = \App\Support\AdminUrl::for('subscription-required');
    }

    public function envelope(): Envelope
    {
        $subject = $this->daysRemaining === 1
            ? __('mail.trial_ending_soon_subject_tomorrow', ['workspace' => $this->workspaceName])
            : __('mail.trial_ending_soon_subject_days',     ['workspace' => $this->workspaceName, 'days' => $this->daysRemaining]);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial-ending-soon');
    }
}
