<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\Tag;
use App\Services\Automations\AutomationDispatcher;

class TagAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run, bool $add = true): bool
    {
        $tagName = $config['tag'] ?? null;
        if (! $tagName) return false;

        $tag = Tag::firstOrCreate(
            ['name' => $tagName, 'tenant_id' => $lead->tenant_id],
            ['color' => '#6366f1']
        );

        if ($add) {
            $lead->tags()->syncWithoutDetaching([$tag->id]);

            $parentAutomationId = $run->automation_id ?? null;
            app(AutomationDispatcher::class)->dispatch(
                'tag_added',
                $lead,
                ['tag' => $tagName, '_skip_automation_ids' => array_filter([$parentAutomationId])]
            );
        } else {
            $lead->tags()->detach($tag->id);
        }

        return true;
    }
}
