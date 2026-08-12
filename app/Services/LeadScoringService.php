<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadScoringRule;

class LeadScoringService
{
    public function scoreForTenant(int $tenantId): void
    {
        $rules = LeadScoringRule::where('tenant_id', $tenantId)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        Lead::where('tenant_id', $tenantId)
            ->chunkById(200, function ($leads) use ($rules) {
                foreach ($leads as $lead) {
                    [$score, $applied] = $this->evaluate($lead, $rules);
                    if ($lead->lead_score !== $score || $lead->applied_scoring_rules !== $applied) {
                        $lead->updateQuietly([
                            'lead_score'            => $score,
                            'applied_scoring_rules' => $applied,
                        ]);
                    }
                }
            });
    }

    public function scoreLead(Lead $lead): int
    {
        $rules = LeadScoringRule::where('tenant_id', $lead->tenant_id)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        [$score, $applied] = $this->evaluate($lead, $rules);
        $lead->applied_scoring_rules = $applied;

        return $score;
    }

    /**
     * @return array{0:int, 1:array<int,array{rule_id:int,name:string,points:int}>}
     */
    private function evaluate(Lead $lead, $rules): array
    {
        $score   = 0;
        $applied = [];

        foreach ($rules as $rule) {
            $fieldValue = $lead->getAttribute($rule->field);

            if ($this->matches($fieldValue, $rule->operator, $rule->value)) {
                $score    += (int) $rule->points;
                $applied[] = [
                    'rule_id' => $rule->id,
                    'name'    => $rule->name,
                    'points'  => (int) $rule->points,
                ];
            }
        }

        return [max(0, $score), $applied];
    }

    private function matches(mixed $value, string $operator, ?string $ruleValue): bool
    {
        return match ($operator) {
            'equals'       => (string) $value === (string) $ruleValue,
            'not_equals'   => (string) $value !== (string) $ruleValue,
            'contains'     => str_contains((string) $value, (string) $ruleValue),
            'present'      => ! empty($value),
            'absent'       => empty($value),
            'greater_than' => is_numeric($value) && $value > (float) $ruleValue,
            'less_than'    => is_numeric($value) && $value < (float) $ruleValue,
            default        => false,
        };
    }
}
