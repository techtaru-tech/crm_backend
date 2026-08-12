<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(fn () => $this->record->status === Invoice::STATUS_DRAFT),

            Action::make('public_link')
                ->label(__('filament/invoices.public_link'))
                ->icon('heroicon-o-link')
                ->color('gray')
                ->url(fn () => $this->record->publicUrl())
                ->openUrlInNewTab(),

            Action::make('download_pdf')
                ->label(__('filament/invoices.download_pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('invoice.pdf', $this->record->public_token))
                ->openUrlInNewTab(),
        ];
    }
}
