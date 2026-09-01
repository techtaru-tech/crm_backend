<?php

namespace App\Notifications;

use App\Events\UserNotificationCreated;
use App\Jobs\SendBrowserPush;
use App\Mail\LeadNotificationMail;
use App\Models\Integration;
use App\Models\Lead;
use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class IntegrationSyncFailedNotification extends Notification implements ShouldQueue
{
    use Queueable, \App\Notifications\Concerns\UsesBrandedMail;

    public function __construct(
        public readonly Integration $integration,
        public readonly Lead $lead,
        public readonly string $error,
    ) {}

    public function via(object $notifiable): array
    {
        $userId       = $notifiable->id;
        $type         = 'integration_sync_failed';
        $channels     = [];
        $inApp        = NotificationPreference::isEnabled($userId, $type, 'in_app');
        $emailEnabled = NotificationPreference::isEnabled($userId, $type, 'email');
        $emailFreq    = NotificationPreference::emailFrequency($userId, $type);

        if ($inApp) {
            $channels[] = 'database';
            broadcast(new UserNotificationCreated(
                $userId,
                $type,
                __('notifications.integration_sync_failed_broadcast_title', [
                    'integration' => $this->integration->getLabel(),
                    'lead'        => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                ]),
                $this->lead->id,
            ))->toOthers();
        }

        if ($emailEnabled && $emailFreq === 'immediate') {
            $channels[] = 'mail';
        } elseif ($emailEnabled && $emailFreq === 'hourly') {
            NotificationDigest::queue($userId, $type, [
                'integration_type' => $this->integration->type,
                'integration_name' => $this->integration->getLabel(),
                'lead_id'          => $this->lead->id,
                'lead_name'        => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                'error'            => $this->error,
            ]);
        }

        if (NotificationPreference::isEnabled($userId, $type, 'push')) {
            SendBrowserPush::dispatch(
                $userId,
                __('notifications.integration_sync_failed_push_title'),
                __('notifications.integration_sync_failed_broadcast_title', [
                    'integration' => $this->integration->getLabel(),
                    'lead'        => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                ]),
                \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            );
        }

        return $channels;
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $name   = trim("{$this->lead->first_name} {$this->lead->last_name}");

        // XSS fix: a prior fix escaped $this->integration->getLabel()
        // and $this->error but missed the lead-name substitution.  All
        // three values flow into {!! $line !!} rendering so all three
        // need e() before __() substitution.
        return $this->brandedMailFor(new LeadNotificationMail(
            emailSubject: __('notifications.integration_sync_failed_mail_subject', ['integration' => $this->integration->getLabel()]),
            headline: __('notifications.integration_sync_failed_mail_headline'),
            lines: [
                __('notifications.integration_sync_failed_mail_line_intro'),
                __('notifications.integration_sync_failed_mail_line_integration', ['integration' => e($this->integration->getLabel())]),
                __('notifications.integration_sync_failed_mail_line_lead',        ['name'        => e($name)]),
                __('notifications.integration_sync_failed_mail_line_error',       ['error'       => e($this->error)]),
            ],
            actionUrl: \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            actionLabel: __('notifications.btn_view_lead'),
        ), $notifiable);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'             => 'integration_sync_failed',
            'integration_type' => $this->integration->type,
            'integration_name' => $this->integration->getLabel(),
            'lead_id'          => $this->lead->id,
            'lead_name'        => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'error'            => $this->error,
        ];
    }
}
