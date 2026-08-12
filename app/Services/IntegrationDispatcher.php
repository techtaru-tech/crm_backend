<?php

namespace App\Services;

use App\Jobs\SyncLeadToIntegration;
use App\Models\Integration;
use App\Models\Lead;

class IntegrationDispatcher
{
    public function dispatch(string $event, Lead $lead): void
    {
        $integrations = Integration::where('tenant_id', $lead->tenant_id)
            ->where('enabled', true)
            ->get();

        foreach ($integrations as $integration) {
            SyncLeadToIntegration::dispatch($integration->id, $lead->id, $event, null, $lead->tenant_id);
        }
    }
}
