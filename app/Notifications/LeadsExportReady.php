<?php

namespace App\Notifications;

use App\Events\UserNotificationCreated;
use App\Jobs\SendBrowserPush;
use App\Mail\LeadNotificationMail;
use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use App\Notifications\Concerns\UsesBrandedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeadsExportReady extends Notification implements ShouldQueue
{
    use Queueable, UsesBrandedMail;

    public function __construct(
        private string $downloadUrl,
        private string $filename,
    ) {}

    public function via(object $notifiable): array
    {
        $userId       = $notifiable->id;
        $type         = 'export_ready';
        $channels     = [];
        $inApp        = NotificationPreference::isEnabled($userId, $type, 'in_app');
        $emailEnabled = NotificationPreference::isEnabled($userId, $type, 'email');
        $emailFreq    = NotificationPreference::emailFrequency($userId, $type);

        if ($inApp) {
            $channels[] = 'database';
            broadcast(new UserNotificationCreated(
                $userId,
                $type,
                __('notifications.export_ready_db_message'),
            ))->toOthers();
        }

        if ($emailEnabled && $emailFreq === 'immediate') {
            $channels[] = 'mail';
        } elseif ($emailEnabled && $emailFreq === 'hourly') {
            NotificationDigest::queue($userId, $type, [
                'message'      => __('notifications.export_ready_db_message'),
                'download_url' => $this->downloadUrl,
                'filename'     => $this->filename,
            ]);
        }

        if (NotificationPreference::isEnabled($userId, $type, 'push')) {
            SendBrowserPush::dispatch(
                $userId,
                __('notifications.export_ready_push_title'),
                __('notifications.export_ready_push_body', ['filename' => $this->filename]),
                $this->downloadUrl,
            );
        }

        return $channels;
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $tenant = $this->resolveNotifiableTenant($notifiable);

        return (new LeadNotificationMail(
            emailSubject: __('notifications.export_ready_mail_subject', ['filename' => $this->filename]),
            headline: __('notifications.export_ready_mail_headline'),
            lines: [
                __('notifications.export_ready_mail_line_intro'),
                __('notifications.export_ready_mail_line_file', ['filename' => e($this->filename)]),
            ],
            actionUrl: $this->downloadUrl,
            actionLabel: __('notifications.export_ready_btn_download'),
        ))->withTenant($tenant);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'export_ready',
            'message'      => __('notifications.export_ready_db_message'),
            'download_url' => $this->downloadUrl,
            'filename'     => $this->filename,
        ];
    }
}
