<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    use Concerns\SyncsTeamMembership;

    protected static string $resource = TeamResource::class;

    /** @var array<string, array<int,int>> */
    protected array $membershipData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractMembership($data);
    }

    protected function afterCreate(): void
    {
        $this->syncMembership($this->record);
    }
}
