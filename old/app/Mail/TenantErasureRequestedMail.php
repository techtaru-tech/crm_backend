<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirms a GDPR Article 17 erasure request to the workspace owner.
 *
 * Sent by TenantErasureService::requestErasure() the moment the
 * countdown begins.  Different from WorkspaceSuspendedMail (billing
 * lockout) — this one announces "we received your delete request,
 * here is the cancel link, you have 30 days to change your mind".
 *
 * Reuses BrandedMailable so the tenant's own header gradient / logo
 * carries through right up to the moment of erasure — every other
 * email they receive looks like it came from their own workspace,
 * this one shouldn't be the odd one out.
 */
class TenantErasureRequestedMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public string $requesterName;
    public string $cancelUrl;

    public function __construct(
        Tenant $tenant,
        User $requester,
        public int $coolOffDays,
    ) {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->requesterName = $requester->name ?: $requester->email;
        // The privacy page hosts the Cancel button — sending users
        // back there gives them the same one-click revocation as
        // the in-app banner.
        $this->cancelUrl = \App\Support\AdminUrl::for('privacy');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.tenant_erasure_requested_subject', [
                'workspace' => $this->workspaceName,
                'days'      => $this->coolOffDays,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.tenant-erasure-requested');
    }
}
