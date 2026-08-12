<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent by ProcessSubscriptionLifecycle::processAutoSuspensions when a
 * tenant has been past expiry for longer than the SA-configured
 * auto-suspend grace period.  Different from TrialExpiredMail /
 * SubscriptionExpiredMail in tone — those announce "your access is
 * paused but your data is here, pick a plan".  This one announces
 * "we tried, you didn't act, your workspace is now suspended" while
 * still offering a reactivation path so the buyer can pay their way
 * back in.
 *
 * Reuses the BrandedMailable layout so tenant-level branding (header
 * gradient, app name, primary colour) carries through.
 */
class WorkspaceSuspendedMail extends BrandedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public string $reactivateUrl;

    public function __construct(Tenant $tenant)
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        // /admin/subscription-required handles every "blocked" state
        // and shows the reason via flash.  EnforceSubscription routes
        // suspended tenants there with reason='suspended'.
        $this->reactivateUrl = \App\Support\AdminUrl::for('subscription-required');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.workspace_suspended_subject', ['workspace' => $this->workspaceName]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.workspace-suspended');
    }
}
