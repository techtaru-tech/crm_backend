<?php

namespace App\Notifications;

use App\Events\UserNotificationCreated;
use App\Jobs\SendBrowserPush;
use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable, \App\Notifications\Concerns\UsesBrandedMail;

    public function __construct(
        public readonly Lead $lead,
        public readonly ?User $assignedBy,
    ) {}

    public function via(object $notifiable): array
    {
        $userId       = $notifiable->id;
        $type         = 'lead_assigned';
        $channels     = [];
        $inApp        = NotificationPreference::isEnabled($userId, $type, 'in_app');
        $emailEnabled = NotificationPreference::isEnabled($userId, $type, 'email');
        $emailFreq    = NotificationPreference::emailFrequency($userId, $type);

        if ($inApp) {
            $channels[] = 'database';
            broadcast(new UserNotificationCreated(
                $userId,
                $type,
                __('notifications.lead_assigned_broadcast_title', [
                    'name' => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                ]),
                $this->lead->id,
            ))->toOthers();
        }

        if ($emailEnabled && $emailFreq === 'immediate') {
            $channels[] = 'mail';
        } elseif ($emailEnabled && $emailFreq === 'hourly') {
            NotificationDigest::queue($userId, $type, [
                'lead_id'     => $this->lead->id,
                'lead_name'   => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                'assigned_by' => $this->assignedBy?->name,
            ]);
        }

        if (NotificationPreference::isEnabled($userId, $type, 'push')) {
            SendBrowserPush::dispatch(
                $userId,
                __('notifications.lead_assigned_push_title'),
                __('notifications.lead_assigned_broadcast_title', [
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
            'type'        => 'lead_assigned',
            'lead_id'     => $this->lead->id,
            'lead_name'   => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'assigned_by' => $this->assignedBy?->name,
        ];
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $tenant = $this->resolveNotifiableTenant($notifiable);
        $name   = trim("{$this->lead->first_name} {$this->lead->last_name}");

        // XSS fix: lead-notification.blade.php renders lines via
        // {!! $line !!} (trusted <strong> shell).  User-controlled values
        // (lead name, assigner's user.name) MUST be e()'d before __()
        // substitution.  The system fallback comes from a lang key so
        // it's developer-controlled — still e()'d for belt-and-suspenders
        // (cheap and protects against future lang-file edits that add HTML).
        return (new LeadNotificationMail(
            emailSubject: __('notifications.lead_assigned_mail_subject'),
            headline: __('notifications.lead_assigned_mail_headline'),
            lines: [
                __('notifications.lead_assigned_mail_line_intro'),
                __('notifications.lead_assigned_mail_line_lead',        ['name' => e($name)]),
                __('notifications.lead_assigned_mail_line_assigned_by', [
                    'name' => e($this->assignedBy?->name ?? __('notifications.system_fallback')),
                ]),
            ],
            actionUrl: \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            actionLabel: __('notifications.btn_view_lead'),
        ))->withTenant($tenant);
    }
}
