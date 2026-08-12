<?php

namespace App\Filament\SuperAdmin\Resources\PlanResource\Pages;

use App\Filament\SuperAdmin\Resources\PlanResource;
use App\Services\PlanService;
use Filament\Resources\Pages\CreateRecord;

class CreatePlan extends CreateRecord
{
    protected static string $resource = PlanResource::class;

    protected function afterCreate(): void
    {
        app(PlanService::class)->flushCache();
    }
}
