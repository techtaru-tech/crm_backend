<?php

namespace App\Filament\SuperAdmin\Resources\LocaleResource\Pages;

use App\Filament\SuperAdmin\Resources\LocaleResource;
use App\Models\Locale;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLocale extends CreateRecord
{
    protected static string $resource = LocaleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = strtolower((string) ($data['code'] ?? ''));

        // Ensure only ONE default locale exists at a time.
        if (! empty($data['is_default'])) {
            Locale::query()->where('is_default', true)->update(['is_default' => false]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $code = $this->record->code;
        $langDir = base_path('lang/' . $code);

        // Give the operator a heads-up about admin-UI coverage so
        // they know what to expect — a language without a lang/{code}/
        // directory still works for the marketing site but the admin
        // panel renders in English for those users.
        if ($code !== 'en' && ! is_dir($langDir)) {
            Notification::make()
                ->warning()
                ->title(__('filament/sa_locales.created_marketing_ready_title', ['name' => $this->record->name]))
                ->body(__('filament/sa_locales.created_marketing_ready_body', ['code' => $code]))
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->success()
                ->title(__('filament/sa_locales.created_success_title', ['name' => $this->record->name]))
                ->send();
        }
    }
}
