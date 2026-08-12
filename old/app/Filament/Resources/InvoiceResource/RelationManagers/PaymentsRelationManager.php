<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\InvoicePayment;
use App\Support\Currency;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-credit-card';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/invoices.payments_relation_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('gateway')
                ->label(__('filament/invoices.field_gateway_label'))
                ->options([
                    'manual'   => __('filament/invoices.option_gateway_manual'),
                    'stripe'   => 'Stripe',
                    'paypal'   => 'PayPal',
                    'razorpay' => 'Razorpay',
                    'paystack' => 'Paystack',
                ])
                ->default('manual')
                ->required(),
            TextInput::make('amount')
                ->label(__('filament/invoices.field_amount_label'))
                ->numeric()
                ->prefix(fn () => Currency::defaultSymbol())
                ->required(),
            TextInput::make('currency')
                ->label(__('filament/invoices.field_payment_currency_label'))
                ->maxLength(3)
                ->default(fn ($livewire) => $livewire->ownerRecord->currency)
                ->required(),
            Select::make('status')
                ->label(__('filament/invoices.field_payment_status_label'))
                ->options([
                    InvoicePayment::STATUS_PENDING   => __('filament/invoices.option_payment_pending'),
                    InvoicePayment::STATUS_SUCCEEDED => __('filament/invoices.option_payment_succeeded'),
                    InvoicePayment::STATUS_FAILED    => __('filament/invoices.option_payment_failed'),
                    InvoicePayment::STATUS_REFUNDED  => __('filament/invoices.option_payment_refunded'),
                ])
                ->default(InvoicePayment::STATUS_SUCCEEDED)
                ->required(),
            TextInput::make('external_id')->label(__('filament/invoices.external_reference'))->nullable(),
            DateTimePicker::make('paid_at')
                ->label(__('filament/invoices.field_paid_at_label'))
                ->seconds(false)
                ->default(now()),
        ]);
    }

    public function table(Table $table): Table
    {
        $currency = fn ($record) => $record?->currency ?: 'USD';

        return $table
            ->columns([
                TextColumn::make('gateway')
                    ->label(__('filament/invoices.col_gateway'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $key      = 'filament/invoices.payment_gateway_' . $state;
                        $fallback = [
                            'stripe'   => 'Stripe',
                            'paypal'   => 'PayPal',
                            'razorpay' => 'Razorpay',
                            'paystack' => 'Paystack',
                            'manual'   => 'Manual',
                        ];
                        $translated = __($key);
                        return $translated === $key ? ($fallback[$state] ?? $state) : $translated;
                    })
                    ->colors([
                        'primary' => 'stripe',
                        'info'    => 'paypal',
                        'warning' => 'razorpay',
                        'success' => 'paystack',
                        'gray'    => 'manual',
                    ]),
                TextColumn::make('amount')
                    ->label(__('filament/invoices.col_amount'))
                    ->money(fn ($record) => $currency($record))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/invoices.col_payment_status'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $key      = 'filament/invoices.payment_status_' . $state;
                        $fallback = [
                            'pending'   => 'Pending',
                            'succeeded' => 'Succeeded',
                            'failed'    => 'Failed',
                            'refunded'  => 'Refunded',
                        ];
                        $translated = __($key);
                        return $translated === $key ? ($fallback[$state] ?? $state) : $translated;
                    })
                    ->colors([
                        'gray'    => 'pending',
                        'success' => 'succeeded',
                        'danger'  => 'failed',
                        'warning' => 'refunded',
                    ]),
                TextColumn::make('external_id')
                    ->label(__('filament/invoices.col_reference'))
                    ->placeholder('—')
                    ->limit(32)
                    ->toggleable(),
                TextColumn::make('paid_at')
                    ->label(__('filament/invoices.col_paid_at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament/invoices.col_payment_created_at'))
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament/invoices.record_payment'))
                    ->mutateFormDataUsing(function (array $data, $livewire) {
                        $data['tenant_id'] = $livewire->ownerRecord->tenant_id;
                        return $data;
                    }),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
