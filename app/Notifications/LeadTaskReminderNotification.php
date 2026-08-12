<?php

namespace App\Notifications;

use App\Models\LeadTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadTaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly LeadTask $task) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $lead = $this->task->lead;

        return [
            'type'       => 'task_reminder',
            'task_id'    => $this->task->id,
            'lead_id'    => $this->task->lead_id,
            'lead_name'  => $lead ? trim($lead->first_name . ' ' . $lead->last_name) : null,
            'title'      => $this->task->title,
            'due_at'     => optional($this->task->due_at)->toIso8601String(),
            'priority'   => $this->task->priority,
            'message'    => __('notifications.task_reminder_db_message', ['title' => $this->task->title]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lead   = $this->task->lead;
        $due    = $this->task->due_at ? $this->task->due_at->translatedFormat('M j, Y H:i') : __('notifications.task_reminder_due_no_date');
        $who    = $lead ? trim($lead->first_name . ' ' . $lead->last_name) : __('notifications.task_reminder_lead_fallback');
        $subj   = __('notifications.task_reminder_mail_subject', ['title' => $this->task->title]);

        $mail = (new MailMessage)
            ->subject($subj)
            ->greeting(__('notifications.task_reminder_mail_greeting'))
            ->line(__('notifications.task_reminder_mail_line_intro'))
            ->line('**' . $this->task->title . '**')
            ->line(__('notifications.task_reminder_mail_line_lead', ['name' => $who]))
            ->line(__('notifications.task_reminder_mail_line_due', ['due' => $due]))
            ->line(__('notifications.task_reminder_mail_line_priority', [
                'priority' => self::priorityLabel($this->task->priority),
            ]));

        if ($this->task->description) {
            $mail->line(__('notifications.task_reminder_mail_line_description', ['description' => $this->task->description]));
        }

        if ($lead) {
            $mail->action(__('notifications.btn_view_lead'), \App\Support\AdminUrl::for('leads/' . $lead->id . '/view'));
        }

        return $mail->line(__('notifications.task_reminder_mail_line_outro', ['app' => config('app.name', 'LeadHub')]));
    }

    /**
     * Translator-first priority label. Looks up
     * `filament/leads.task_priority_<key>` and falls back to a
     * legacy ucfirst() of the raw priority key if no translation row
     * matches (e.g. custom priorities added in a fork).
     */
    protected static function priorityLabel(?string $priority): string
    {
        $priority = strtolower((string) ($priority ?? ''));
        if ($priority === '') {
            return (string) __('notifications.task_reminder_priority_normal');
        }

        $key        = 'filament/leads.task_priority_' . $priority;
        $translated = __($key);

        // Laravel returns the key itself when a translation is missing.
        if ($translated !== $key) {
            return (string) $translated;
        }

        return ucfirst($priority);
    }
}
