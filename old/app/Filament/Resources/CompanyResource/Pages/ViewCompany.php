<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompany extends ViewRecord
{
    protected static string $resource = CompanyResource::class;

    public function getView(): string
    {
        return 'filament.resources.companies.view';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getViewData(): array
    {
        $company = $this->record->load(['assignedUser', 'leads' => fn($q) => $q->latest()]);
        return [
            'company' => $company,
            'leads'   => $company->leads,
        ];
    }
}
