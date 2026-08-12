<?php

namespace App\Notifications;

use App\Events\UserNotificationCreated;
use App\Jobs\SendBrowserPush;
use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use App\Models\PipelineStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeadStageChangedNotification extends Notification implements ShouldQueue
{
    use Queueable, \App\Notifications\Concerns\UsesBrandedMail;

    public function __construct(
        public readonly Lead $lead,
        public readonly ?PipelineStage $fromStage,
        public readonly ?PipelineStage $toStage,
    ) {}

    public function via(object $notifiable): array
    {
        $userId       = $notifiable->id;
        $type         = 'lead_stage_changed';
        $channels     = [];
        $inApp        = NotificationPreference::isEnabled($userId, $type, 'in_app');
        $emailEnabled = NotificationPreference::isEnabled($userId, $type, 'email');
        $emailFreq    = NotificationPreference::emailFrequency($userId, $type);

        if ($inApp) {
            $channels[] = 'database';
            broadcast(new UserNotificationCreated(
                $userId,
                $type,
                __('notifications.lead_stage_changed_broadcast_title', [
                    'stage' => $this->toStage?->name ?? '',
                    'name'  => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                ]),
                $this->lead->id,
            ))->toOthers();
        }

        if ($emailEnabled && $emailFreq === 'immediate') {
            $channels[] = 'mail';
        } elseif ($emailEnabled && $emailFreq === 'hourly') {
            NotificationDigest::queue($userId, $type, [
                'lead_id'    => $this->lead->id,
                'lead_name'  => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                'from_stage' => $this->fromStage?->name,
                'to_stage'   => $this->toStage?->name,
            ]);
        }

        if (NotificationPreference::isEnabled($userId, $type, 'push')) {
            SendBrowserPush::dispatch(
                $userId,
                __('notifications.lead_stage_changed_push_title'),
                __('notifications.lead_stage_changed_push_body', [
                    'name'  => trim("{$this->lead->first_name} {$this->lead->last_name}"),
                    'stage' => $this->toStage?->name ?? '',
                ]),
                \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            );
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'lead_stage_changed',
            'lead_id'    => $this->lead->id,
            'lead_name'  => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'from_stage' => $this->fromStage?->name,
            'to_stage'   => $this->toStage?->name,
        ];
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $tenant = $this->resolveNotifiableTenant($notifiable);
        $name   = trim("{$this->lead->first_name} {$this->lead->last_name}");

        // XSS fix: lead-notification.blade.php renders lines via
        // {!! $line !!} (trusted <strong> shell from lang strings).
        // User-controlled values (lead name, pipeline stage names that
        // tenant admins type) MUST be e()'d before __() substitution.
        return (new LeadNotificationMail(
            emailSubject: __('notifications.lead_stage_changed_mail_subject'),
            headline: __('notifications.lead_stage_changed_mail_headline'),
            lines: [
                __('notifications.lead_stage_changed_mail_line_intro'),
                __('notifications.lead_stage_changed_mail_line_lead', ['name'  => e($name)]),
                __('notifications.lead_stage_changed_mail_line_from', ['stage' => e($this->fromStage?->name ?? '—')]),
                __('notifications.lead_stage_changed_mail_line_to',   ['stage' => e($this->toStage?->name   ?? '—')]),
            ],
            actionUrl: \App\Support\AdminUrl::for('leads/' . $this->lead->id),
            actionLabel: __('notifications.btn_view_lead'),
        ))->withTenant($tenant);
    }
}
