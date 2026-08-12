<?php

namespace App\Filament\Resources\LandingPageResource\Pages;

use App\Filament\Resources\LandingPageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLandingPage extends EditRecord
{
    protected static string $resource = LandingPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(__('filament/landing_pages.preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                // Mirror the List view's Preview URL — new
                // /{workspace}/{slug} route, with ?t=<tenant> and
                // &preview=1 kept so the staff-preview path still
                // unlocks drafts.
                ->url(
                    fn () => url(
                        '/' . ($this->record->tenant?->slug ?: 'p') . '/' . $this->record->slug
                    )
                        . '?t=' . (int) $this->record->tenant_id
                        . '&preview=1',
                    shouldOpenInNewTab: true
                ),
            DeleteAction::make(),
        ];
    }
}
