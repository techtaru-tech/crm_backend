<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    use Concerns\SyncsTeamMembership;

    protected static string $resource = TeamResource::class;

    /** @var array<string, array<int,int>> */
    protected array $membershipData = [];

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['members']  = $this->record->members()->pluck('users.id')->all();
        $data['managers'] = $this->record->managers()->pluck('users.id')->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractMembership($data);
    }

    protected function afterSave(): void
    {
        $this->syncMembership($this->record);
    }
}
