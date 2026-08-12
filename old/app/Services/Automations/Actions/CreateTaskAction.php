<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\LeadTask;

class CreateTaskAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $title = $config['title'] ?? __('automation_runs.defaults.task_title');
        if (! $title) return false;

        $dueAt = null;
        if (isset($config['hours_from_now']) && $config['hours_from_now'] !== '') {
            $dueAt = now()->addHours((int) $config['hours_from_now']);
        } elseif (! empty($config['due_in_hours'])) {
            $dueAt = now()->addHours((int) $config['due_in_hours']);
        } elseif (! empty($config['due_in_days'])) {
            $dueAt = now()->addDays((int) $config['due_in_days']);
        } else {
            // Fall back to default "hours_from_now" = 24
            $dueAt = now()->addHours(24);
        }

        $priority = $config['priority'] ?? LeadTask::PRIORITY_NORMAL;
        if (! in_array($priority, array_keys(LeadTask::PRIORITIES), true)) {
            $priority = LeadTask::PRIORITY_NORMAL;
        }

        // Simple token substitution for title/description
        $title       = $this->interpolate($title, $lead);
        $description = $this->interpolate($config['description'] ?? null, $lead);

        LeadTask::create([
            'lead_id'          => $lead->id,
            'tenant_id'        => $lead->tenant_id,
            'assigned_user_id' => $config['assigned_user_id'] ?? $lead->assigned_user_id,
            'title'            => $title,
            'description'      => $description,
            'due_at'           => $dueAt,
            'priority'         => $priority,
        ]);

        return true;
    }

    private function interpolate(?string $text, Lead $lead): ?string
    {
        if (! $text) {
            return $text;
        }

        return strtr($text, [
            '{first_name}' => $lead->first_name ?? '',
            '{last_name}'  => $lead->last_name ?? '',
            '{full_name}'  => trim(($lead->first_name ?? '') . ' ' . ($lead->last_name ?? '')),
            '{email}'      => $lead->email ?? '',
            '{source}'     => $lead->source ?? '',
            '{company}'    => $lead->company ?? ($lead->company_name ?? ''),
            '{lead_score}' => (string) ($lead->lead_score ?? 0),
        ]);
    }
}
