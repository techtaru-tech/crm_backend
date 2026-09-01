<?php

namespace App\Notifications;

use App\Events\UserNotificationCreated;
use App\Jobs\SendBrowserPush;
use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use App\Notifications\Concerns\UsesBrandedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeadAutomationNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesBrandedMail;

    public function __construct(
        public readonly Lead   $lead,
        public readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        $userId       = $notifiable->id;
        $type         = 'automation_ran';
        $channels     = [];
        $inApp        = NotificationPreference::isEnabled($userId, $type, 'in_app');
        $emailEnabled = NotificationPreference::isEnabled($userId, $type, 'email');
        $emailFreq    = NotificationPreference::emailFrequency($userId, $type);

        if ($inApp) {
            $channels[] = 'database';
            broadcast(new UserNotificationCreated(
                $userId,
                $type,
                $this->message,
                $this->lead->id,
            ))->toOthers();
        }

        if ($emailEnabled && $emailFreq === 'immediate') {
            $channels[] = 'mail';
        } elseif ($emailEnabled && $emailFreq === 'hourly') {
            NotificationDigest::queue($userId, $type, [
                'lead_id'   => $this->lead->id,
                'lead_name' => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                'email'     => $this->lead->email,
                'message'   => $this->message,
            ]);
        }

        if (NotificationPreference::isEnabled($userId, $type, 'push')) {
            SendBrowserPush::dispatch(
                $userId,
                __('notifications.automation_push_title'),
                $this->message . ' — ' . trim("{$this->lead->first_name} {$this->lead->last_name}"),
                \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            );
        }

        return $channels;
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $name   = trim("{$this->lead->first_name} {$this->lead->last_name}");

        // XSS fix: a prior fix escaped $this->message but missed the
        // lead-name substitution.  Both flow into {!! $line !!} rendering
        // so both need e() before __() substitution.
        return $this->brandedMailFor(new LeadNotificationMail(
            emailSubject: __('notifications.automation_mail_subject', ['message' => $this->message]),
            headline: __('notifications.automation_mail_headline'),
            lines: [
                __('notifications.automation_mail_line_intro'),
                __('notifications.automation_mail_line_lead',    ['name'    => e($name)]),
                __('notifications.automation_mail_line_message', ['message' => e($this->message)]),
            ],
            actionUrl: \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            actionLabel: __('notifications.btn_view_lead'),
        ), $notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'      => 'automation_ran',
            'lead_id'   => $this->lead->id,
            'lead_name' => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'email'     => $this->lead->email,
            'message'   => $this->message,
        ];
    }
}
