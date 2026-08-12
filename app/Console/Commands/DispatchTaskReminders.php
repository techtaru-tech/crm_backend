<?php

namespace App\Console\Commands;

use App\Models\LeadTask;
use App\Notifications\LeadTaskReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class DispatchTaskReminders extends Command
{
    protected $signature   = 'tasks:send-reminders';
    protected $description = 'Dispatch reminder notifications for LeadTasks whose reminder_at has elapsed.';

    public function handle(): int
    {
        $count = 0;

        LeadTask::query()
            ->withoutGlobalScope('tenant')
            ->dueForReminder()
            ->with(['lead', 'assignedUser'])
            ->chunkById(100, function ($tasks) use (&$count) {
                foreach ($tasks as $task) {
                    try {
                        if ($task->assignedUser) {
                            Notification::send($task->assignedUser, new LeadTaskReminderNotification($task));
                        }
                        $task->forceFill(['reminder_sent_at' => now()])->saveQuietly();
                        $count++;
                    } catch (\Throwable $e) {
                        logger()->warning('DispatchTaskReminders: failed to send reminder', [
                            'task_id' => $task->id,
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Dispatched {$count} task reminder(s).");
        return self::SUCCESS;
    }
}
