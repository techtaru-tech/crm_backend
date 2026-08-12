<?php

namespace App\Services\Automations;

use App\Models\Lead;

class AutomationConditionEvaluator
{
    public function evaluate(Lead $lead, array $conditions): bool
    {
        foreach ($conditions as $condition) {
            if (! $this->evaluateOne($lead, $condition)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateOne(Lead $lead, array $condition): bool
    {
        $type   = $condition['type'] ?? '';
        $value  = $condition['value'] ?? null;
        $field  = $condition['field'] ?? null;

        return match ($type) {
            'source_is'     => $lead->source === $value,
            'source_is_not' => $lead->source !== $value,
            'has_tag'       => $lead->tags->pluck('name')->contains($value),
            'not_has_tag'   => ! $lead->tags->pluck('name')->contains($value),
            'field_equals'  => $field && isset($lead->$field) && (string) $lead->$field === (string) $value,
            'field_contains'=> $field && isset($lead->$field) && str_contains((string) $lead->$field, (string) $value),
            'field_is_empty'=> $field && empty($lead->$field),
            'score_gt'      => $lead->lead_score > (int) $value,
            'score_lt'      => $lead->lead_score < (int) $value,
            'assigned_to'   => $lead->assigned_user_id == $value,
            'unassigned'    => $lead->assigned_user_id === null,
            'time_of_day'   => $this->matchTimeOfDay($value),
            'day_of_week'   => $this->matchDayOfWeek($value),
            default         => true,
        };
    }

    private function matchTimeOfDay(?string $value): bool
    {
        if (! $value) return true;
        [$from, $to] = explode('-', $value . '-');
        $hour = (int) now()->format('H');
        $fromH = (int) explode(':', $from)[0];
        $toH   = (int) explode(':', $to)[0];
        return $hour >= $fromH && $hour < $toH;
    }

    private function matchDayOfWeek(?string $value): bool
    {
        if (! $value) return true;
        $days = array_map('trim', explode(',', $value));
        return in_array(now()->format('l'), $days, true);
    }
}
