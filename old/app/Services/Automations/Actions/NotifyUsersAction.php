<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LeadAutomationNotification;

class NotifyUsersAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $userIds = $config['user_ids'] ?? [];
        if ($config['notify_assigned'] ?? false) {
            if ($lead->assigned_user_id) {
                $userIds[] = $lead->assigned_user_id;
            }
        }

        $users = User::whereIn('id', array_unique($userIds))->get();
        if ($users->isEmpty()) return false;

        try {
            Notification::send($users, new LeadAutomationNotification(
                $lead,
                $config['message'] ?? __('automation_runs.defaults.notify_users_message', ['lead' => $lead->full_name])
            ));
            return true;
        } catch (\Throwable $e) {
            logger()->warning('NotifyUsersAction failed', [
                'lead_id'  => $lead->id,
                'run_id'   => $run->id,
                'user_ids' => array_unique($userIds),
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }
}
