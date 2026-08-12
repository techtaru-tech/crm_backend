<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Models\PipelineStage;

class MovePipelineAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $stageId = $config['pipeline_stage_id'] ?? null;
        if (! $stageId) return false;

        $stage = PipelineStage::find($stageId);
        if (! $stage) return false;

        $lead->update([
            'pipeline_id'       => $stage->pipeline_id,
            'pipeline_stage_id' => $stage->id,
        ]);

        return true;
    }
}
