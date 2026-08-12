<?php

namespace App\Notifications;

use App\Events\UserNotificationCreated;
use App\Jobs\SendBrowserPush;
use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeadReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable, \App\Notifications\Concerns\UsesBrandedMail;

    public function __construct(public readonly Lead $lead) {}

    public function via(object $notifiable): array
    {
        $userId       = $notifiable->id;
        $type         = 'lead_received';
        $channels     = [];
        $inApp        = NotificationPreference::isEnabled($userId, $type, 'in_app');
        $emailEnabled = NotificationPreference::isEnabled($userId, $type, 'email');
        $emailFreq    = NotificationPreference::emailFrequency($userId, $type);

        if ($inApp) {
            $channels[] = 'database';
            broadcast(new UserNotificationCreated(
                $userId,
                $type,
                __('notifications.lead_received_broadcast_title', [
                    'name' => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                ]),
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
                'source'    => $this->lead->source,
            ]);
        }

        if (NotificationPreference::isEnabled($userId, $type, 'push')) {
            SendBrowserPush::dispatch(
                $userId,
                __('notifications.lead_received_push_title'),
                __('notifications.lead_received_broadcast_title', [
                    'name' => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                ]),
                \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            );
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'      => 'lead_received',
            'lead_id'   => $this->lead->id,
            'lead_name' => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'email'     => $this->lead->email,
            'source'    => $this->lead->source,
        ];
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $tenant = $this->resolveNotifiableTenant($notifiable);
        $name   = trim("{$this->lead->first_name} {$this->lead->last_name}");

        // XSS fix: the lead-notification.blade.php template renders
        // these lines via {!! $line !!} so the lang strings' trusted
        // <strong>…</strong> shell renders correctly.  Laravel's __()
        // does NOT escape :placeholder substitutions, so user-controlled
        // values (first_name, last_name, email, phone, source) MUST be
        // e()'d here before reaching the translator — otherwise a lead
        // submitted via a public web form could plant
        // "<img src=x onerror=…>" in the recipient's HTML inbox.
        return (new LeadNotificationMail(
            emailSubject: __('notifications.lead_received_mail_subject', ['name' => $name]),
            headline: __('notifications.lead_received_mail_headline'),
            lines: [
                __('notifications.lead_received_mail_line_intro'),
                __('notifications.lead_received_mail_line_name',   ['name'   => e($name)]),
                __('notifications.lead_received_mail_line_email',  ['email'  => e($this->lead->email  ?? '—')]),
                __('notifications.lead_received_mail_line_phone',  ['phone'  => e($this->lead->phone  ?? '—')]),
                __('notifications.lead_received_mail_line_source', ['source' => e($this->lead->source ?? '—')]),
            ],
            actionUrl: \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            actionLabel: __('notifications.btn_view_lead'),
        ))->withTenant($tenant);
    }
}
