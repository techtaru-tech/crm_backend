<?php

namespace App\Filament\SuperAdmin\Resources\PlanResource\Pages;

use App\Filament\SuperAdmin\Resources\PlanResource;
use App\Services\PlanService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlan extends EditRecord
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->after(fn () => app(PlanService::class)->flushCache()),
        ];
    }

    protected function afterSave(): void
    {
        app(PlanService::class)->flushCache();
    }
}
