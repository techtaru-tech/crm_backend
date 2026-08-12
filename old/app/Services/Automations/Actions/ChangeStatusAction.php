<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\Lead;

class ChangeStatusAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $status = $config['status'] ?? null;
        if (! $status) return false;

        $lead->update(['status' => $status]);
        return true;
    }
}
