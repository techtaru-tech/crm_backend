<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public ?string $amount;
    public ?string $currency;
    public int $retryAttempt;
    public ?string $nextRetryAt;
    public string $updatePaymentUrl;

    public function __construct(
        Tenant $tenant,
        ?string $amount = null,
        ?string $currency = null,
        int $retryAttempt = 1,
        ?string $nextRetryAt = null,
    ) {
        $this->withTenant($tenant);
        $this->workspaceName    = $tenant->name;
        $this->amount           = $amount;
        $this->currency         = $currency;
        $this->retryAttempt     = $retryAttempt;
        $this->nextRetryAt      = $nextRetryAt;
        $this->updatePaymentUrl = \App\Support\AdminUrl::for('billing');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.payment_failed_subject', ['workspace' => $this->workspaceName]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-failed');
    }
}
