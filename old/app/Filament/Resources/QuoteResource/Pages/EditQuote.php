<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_public')
                ->label(__('filament/quotes.public_link'))
                ->icon('heroicon-o-link')
                ->color('gray')
                ->url(fn () => $this->record->publicUrl())
                ->openUrlInNewTab(),
            Action::make('download_pdf')
                ->label(__('filament/quotes.download_pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('quote.pdf', $this->record->public_token))
                ->openUrlInNewTab(),
            DeleteAction::make()->visible(fn () => $this->record->status === Quote::STATUS_DRAFT),
        ];
    }

    protected function afterSave(): void
    {
        // Items::boot triggers recalc on each save, but a totals-only edit
        // (tax_rate / discount_amount) won't touch any item — force one.
        $this->record->refresh()->recalculate();
        Notification::make()->success()->title(__('filament/quotes.notif_saved'))->send();
    }
}
