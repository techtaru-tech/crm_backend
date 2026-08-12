<?php

namespace App\Services\Automations;

use App\Jobs\RunAutomation;
use App\Models\Automation;
use App\Models\Lead;

class AutomationDispatcher
{
    public function dispatch(string $triggerType, Lead $lead, array $context = []): void
    {
        $automations = Automation::where('tenant_id', $lead->tenant_id)
            ->where('enabled', true)
            ->where('trigger_type', $triggerType)
            ->get();

        $skipIds = $context['_skip_automation_ids'] ?? [];

        foreach ($automations as $automation) {
            if (in_array($automation->id, $skipIds, true)) {
                continue;
            }
            if ($this->triggerMatches($automation, $lead, $context)) {
                if ($automation->respect_business_hours && ! $this->isWithinBusinessHours($lead)) {
                    // Visibility: suppressed dispatches used to be silent,
                    // so tenants with a partial business-hours config
                    // would wonder why their automations never fire.
                    // Log at info level + stamp a `last_suppressed_at`
                    // timestamp on the automation so the Automations
                    // list can surface "suppressed" state in the UI.
                    logger()->info('[Automations] suppressed by business hours', [
                        'automation_id' => $automation->id,
                        'automation'    => $automation->name,
                        'lead_id'       => $lead->id,
                        'tenant_id'     => $lead->tenant_id,
                    ]);
                    try {
                        $automation->forceFill([
                            'last_suppressed_at' => now(),
                        ])->saveQuietly();
                    } catch (\Throwable) {
                        // Column may not exist on older installs — fail
                        // open so dispatch logic is never broken by a
                        // missing metadata column.
                    }
                    continue;
                }
                RunAutomation::dispatch($automation->id, $lead->id, 0, null, $lead->tenant_id);
            }
        }
    }

    /**
     * Returns true when "now" in the tenant's business-hours timezone
     * falls within the configured open window for the current weekday.
     * Falls back to "true" when business hours are not configured so
     * dispatch is not accidentally suppressed.
     */
    private function isWithinBusinessHours(Lead $lead): bool
    {
        $tenant = $lead->tenant;
        if (! $tenant) {
            return true;
        }

        $hours = (array) ($tenant->getSetting('business_hours') ?? []);
        $days  = $hours['days'] ?? null;
        if (! is_array($days) || empty($days)) {
            return true;
        }

        $tz = (string) ($hours['timezone'] ?? 'UTC');

        try {
            $now = \Carbon\Carbon::now($tz);
        } catch (\Throwable) {
            $now = \Carbon\Carbon::now();
        }

        $dow = (int) $now->dayOfWeek; // 0 = Sunday
        $day = $days[$dow] ?? null;
        if (! is_array($day) || empty($day['enabled'])) {
            return false;
        }

        $start = (string) ($day['start'] ?? '00:00');
        $end   = (string) ($day['end']   ?? '23:59');

        $nowHm = $now->format('H:i');
        return $nowHm >= $start && $nowHm <= $end;
    }

    private function triggerMatches(Automation $automation, Lead $lead, array $context): bool
    {
        $config = $automation->trigger_config ?? [];

        return match ($automation->trigger_type) {
            'lead_created' => $this->matchLeadCreated($config, $lead, $context),
            'lead_stage_changed' => $this->matchStageChanged($config, $context),
            'lead_assigned'      => true,
            'tag_added'          => $this->matchTagAdded($config, $context),
            'lead_score_threshold' => $this->matchScoreThreshold($config, $lead, $context),
            'no_activity'        => true,
            'form_submitted'     => $this->matchFormSubmitted($config, $lead),
            'manual'             => true,
            default              => false,
        };
    }

    private function matchLeadCreated(array $config, Lead $lead, array $context): bool
    {
        $sources = $config['sources'] ?? [];
        if (empty($sources)) return true;
        return in_array($lead->source, $sources, true);
    }

    private function matchStageChanged(array $config, array $context): bool
    {
        $fromStage = $config['from_stage_id'] ?? null;
        $toStage   = $config['to_stage_id'] ?? null;

        if ($fromStage && ($context['old_stage_id'] ?? null) != $fromStage) return false;
        if ($toStage   && ($context['new_stage_id'] ?? null) != $toStage)   return false;

        return true;
    }

    private function matchTagAdded(array $config, array $context): bool
    {
        $tag = $config['tag'] ?? null;
        if (! $tag) return true;
        return ($context['tag'] ?? null) === $tag;
    }

    private function matchScoreThreshold(array $config, Lead $lead, array $context): bool
    {
        $threshold  = (int) ($config['threshold'] ?? 0);
        $direction  = $config['direction'] ?? 'above';
        $oldScore   = $context['old_score'] ?? null;
        $newScore   = $lead->lead_score;

        if ($oldScore === null) return false;

        if ($direction === 'above') {
            return $oldScore < $threshold && $newScore >= $threshold;
        }

        return $oldScore > $threshold && $newScore <= $threshold;
    }

    private function matchFormSubmitted(array $config, Lead $lead): bool
    {
        $formId = $config['form_id'] ?? null;
        if (! $formId) return true;
        return $lead->form_id == $formId;
    }
}
