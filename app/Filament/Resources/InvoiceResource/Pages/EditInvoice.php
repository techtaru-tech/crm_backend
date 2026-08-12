<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_public')
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
            DeleteAction::make()->visible(fn () => $this->record->status === Invoice::STATUS_DRAFT),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->refresh()->recalculate();
        Notification::make()->success()->title(__('filament/invoices.notify_invoice_saved'))->send();
    }
}
