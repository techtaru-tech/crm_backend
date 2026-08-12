<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(fn () => $this->record->status === Quote::STATUS_DRAFT),

            Action::make('send_for_signature')
                ->label(__('filament/quotes.send_for_signature'))
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn () => in_array($this->record->status, [Quote::STATUS_DRAFT, Quote::STATUS_SENT], true))
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->record;
                    if (! $record->lead?->email) {
                        Notification::make()->danger()->title(__('filament/quotes.notif_lead_no_email'))->send();
                        return;
                    }
                    try {
                        Mail::raw(
                            __('mail.quote_send_review_body', [
                                'name'   => $record->lead->first_name,
                                'number' => $record->quote_number,
                                'url'    => $record->publicUrl(),
                            ]),
                            function ($m) use ($record) {
                                $m->to($record->lead->email)->subject(__('mail.quote_send_review_subject', ['number' => $record->quote_number]));
                            }
                        );
                        $record->markSent();
                        Notification::make()->success()->title(__('filament/quotes.notif_signature_sent'))->send();
                    } catch (\Throwable $e) {
                        Log::error('[quote.send_for_signature] ' . $e->getMessage());
                        Notification::make()->danger()->title(__('filament/quotes.notif_signature_failed', ['error' => $e->getMessage()]))->send();
                    }
                }),

            Action::make('download_pdf')
                ->label(__('filament/quotes.download_pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('quote.pdf', $this->record->public_token))
                ->openUrlInNewTab(),

            Action::make('preview')
                ->label(__('filament/quotes.preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => $this->record->publicUrl())
                ->openUrlInNewTab(),

            Action::make('convert_to_invoice')
                ->label(__('filament/quotes.convert_to_invoice'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn () => $this->record->status === Quote::STATUS_ACCEPTED)
                ->requiresConfirmation()
                ->action(function () {
                    $invoice = $this->record->convertToInvoice();
                    Notification::make()->success()->title(__('filament/quotes.notif_invoice_created', ['number' => $invoice->invoice_number]))->send();
                    return redirect(InvoiceResource::getUrl('edit', ['record' => $invoice->id]));
                }),

            Action::make('cancel')
                ->label(__('filament/quotes.cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => ! in_array($this->record->status, [Quote::STATUS_DECLINED, Quote::STATUS_CONVERTED, Quote::STATUS_EXPIRED], true))
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->markDeclined(__('filament/quotes.decline_reason_cancelled_by_sender'));
                    Notification::make()->success()->title(__('filament/quotes.notif_cancelled'))->send();
                }),
        ];
    }
}
