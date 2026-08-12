<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Events\PlanChanged;
use App\Events\SubscriptionActivated;
use App\Events\SubscriptionCancelled;
use App\Mail\PaymentFailedMail;
use App\Mail\PlanChangedMail;
use App\Mail\SubscriptionActivatedMail;
use App\Mail\SubscriptionCancelledMail;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Single listener that reacts to every subscription-related event and
 * dispatches the matching branded mailable to the tenant owner. Each
 * send is wrapped in try/catch so a transient mail failure never kills
 * the source operation (webhook, checkout, cancellation).
 *
 * Tenants can opt out of these notifications via
 * `settings.notifications.subscription_emails = false`, but the
 * critical "payment failed" email is sent regardless.
 */
class SendSubscriptionNotifications implements ShouldQueue
{
    public string $queue = 'mail';

    public function subscribe($events): array
    {
        return [
            SubscriptionActivated::class => 'handleActivated',
            SubscriptionCancelled::class => 'handleCancelled',
            PaymentFailed::class         => 'handlePaymentFailed',
            PlanChanged::class           => 'handlePlanChanged',
        ];
    }

    public function handleActivated(SubscriptionActivated $event): void
    {
        $recipient = $this->recipientFor($event->tenant);
        if (! $recipient) return;

        if (! $this->isOptedIn($event->tenant)) {
            $this->logAudit($event->tenant, 'subscription.notification_skipped', ['type' => 'activated']);
            return;
        }

        $this->safeSend(
            $recipient,
            new SubscriptionActivatedMail($event->tenant, $event->plan, $event->billingCycle),
            'activated',
            $event->tenant,
        );
    }

    public function handleCancelled(SubscriptionCancelled $event): void
    {
        $recipient = $this->recipientFor($event->tenant);
        if (! $recipient) return;

        if (! $this->isOptedIn($event->tenant)) {
            return;
        }

        $this->safeSend(
            $recipient,
            new SubscriptionCancelledMail($event->tenant, $event->endsAt, $event->reason),
            'cancelled',
            $event->tenant,
        );
    }

    public function handlePaymentFailed(PaymentFailed $event): void
    {
        $recipient = $this->recipientFor($event->tenant);
        if (! $recipient) return;

        // Payment-failed is critical — always send, ignores opt-out.
        $this->safeSend(
            $recipient,
            new PaymentFailedMail(
                $event->tenant,
                $event->amount,
                $event->currency,
                $event->retryAttempt,
                $event->nextRetryAt,
            ),
            'payment_failed',
            $event->tenant,
        );
    }

    public function handlePlanChanged(PlanChanged $event): void
    {
        $recipient = $this->recipientFor($event->tenant);
        if (! $recipient) return;

        if (! $this->isOptedIn($event->tenant)) {
            return;
        }

        $this->safeSend(
            $recipient,
            new PlanChangedMail($event->tenant, $event->oldPlan, $event->newPlan, $event->direction),
            'plan_changed_' . $event->direction,
            $event->tenant,
        );
    }

    private function recipientFor(Tenant $tenant): ?string
    {
        $tenant->loadMissing('owner');
        $email = $tenant->owner?->email;

        if (! $email) {
            Log::warning('Subscription notification skipped — no owner email', [
                'tenant_id' => $tenant->id,
            ]);
            return null;
        }

        return $email;
    }

    private function isOptedIn(Tenant $tenant): bool
    {
        $value = data_get($tenant->settings, 'notifications.subscription_emails', true);
        return (bool) $value;
    }

    private function safeSend(string $recipient, $mailable, string $type, Tenant $tenant): void
    {
        try {
            // The listener itself is already queued, so force the mail to send
            // synchronously inside the queue job instead of re-queuing it.
            Mail::to($recipient)->sendNow($mailable);
            $this->logAudit($tenant, 'subscription.notification_sent', [
                'type'      => $type,
                'recipient' => $recipient,
            ]);
        } catch (\Throwable $e) {
            Log::error('Subscription notification failed', [
                'tenant_id' => $tenant->id,
                'type'      => $type,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    private function logAudit(Tenant $tenant, string $action, array $meta): void
    {
        try {
            AuditLog::record(
                action: $action,
                auditable: $tenant,
                oldValues: [],
                newValues: $meta,
                tags: 'subscription,notification',
            );
        } catch (\Throwable) {
            // Audit failures never block outbound mail.
        }
    }
}
