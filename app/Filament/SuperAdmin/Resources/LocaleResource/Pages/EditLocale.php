<?php

namespace App\Filament\SuperAdmin\Resources\LocaleResource\Pages;

use App\Filament\SuperAdmin\Resources\LocaleResource;
use App\Models\Locale;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocale extends EditRecord
{
    protected static string $resource = LocaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->visible(fn () => ! $this->record->is_default),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['code'] = strtolower((string) ($data['code'] ?? ''));

        // Enforce single-default invariant.
        if (! empty($data['is_default']) && ! $this->record->is_default) {
            Locale::query()
                ->where('is_default', true)
                ->where('id', '!=', $this->record->id)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
