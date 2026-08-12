<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanChangedMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public string $oldPlan;
    public string $newPlan;
    public string $direction;
    public string $manageUrl;

    public function __construct(Tenant $tenant, string $oldPlan, string $newPlan, string $direction = 'change')
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->oldPlan       = $oldPlan;
        $this->newPlan       = $newPlan;
        $this->direction     = $direction;
        $this->manageUrl     = \App\Support\AdminUrl::for('billing');
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->direction) {
            'upgrade'   => __('mail.plan_changed_subject_upgrade',   ['plan' => $this->newPlan]),
            'downgrade' => __('mail.plan_changed_subject_downgrade', ['plan' => $this->newPlan]),
            default     => __('mail.plan_changed_subject_default',   ['plan' => $this->newPlan]),
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.plan-changed');
    }
}
