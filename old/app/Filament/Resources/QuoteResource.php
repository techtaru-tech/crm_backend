<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Quote;
use App\Support\Currency;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-document-text';
    protected static string|UnitEnum|null    $navigationGroup = 'Sales';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/quotes.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/quotes.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/quotes.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        // Unconditional `where('tenant_id', $tenantId)` — see the
        // matching commit on InvoiceResource for full rationale.
        // When $tenantId is null this hard-fails to zero rows
        // instead of leaning on the parent BelongsToTenant scope's
        // SA-cross-tenant carve-out.
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make(__('filament/quotes.tabs_outer'))->columnSpanFull()->tabs([
                Tab::make(__('filament/quotes.tab_info'))->icon('heroicon-o-information-circle')->schema([
                    Section::make(__('sections.quote_details'))->schema([
                        TextInput::make('title')
                            ->label(__('filament/quotes.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('lead_id')
                            ->label(__('filament/quotes.lead'))
                            ->options(function () {
                                $tenantId = \App\Support\TenantContext::currentId();
                                return Lead::where('tenant_id', $tenantId)
                                    ->orderBy('first_name')
                                    ->get()
                                    ->mapWithKeys(fn ($l) => [$l->id => trim(($l->first_name . ' ' . $l->last_name)) . ' — ' . ($l->email ?? 'no email')]);
                            })
                            ->searchable()
                            ->nullable(),
                        Select::make('company_id')
                            ->label(__('filament/quotes.company'))
                            ->options(function () {
                                $tenantId = \App\Support\TenantContext::currentId();
                                return Company::where('tenant_id', $tenantId)->orderBy('name')->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable(),
                        Select::make('currency')
                            ->label(__('filament/quotes.currency'))
                            ->options(\App\Support\Currency::options())
                            ->searchable()
                            ->default(fn () => \App\Support\Currency::forTenant(auth()->user()?->tenant))
                            ->required(),
                        DateTimePicker::make('valid_until')
                            ->label(__('filament/quotes.valid_until'))
                            ->seconds(false)
                            ->default(now()->addDays(14)),
                        Textarea::make('introduction')
                            ->label(__('filament/quotes.introduction'))
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                        Textarea::make('terms')
                            ->label(__('filament/quotes.terms'))
                            ->rows(4)
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
                ]),

                Tab::make(__('filament/quotes.tab_line_items'))->icon('heroicon-o-list-bullet')->schema([
                    Section::make(__('sections.items'))
                        ->description(__('filament/quotes.items_description'))
                        ->schema([
                            Repeater::make('items')
                                ->relationship('items')
                                ->label('')
                                ->reorderable('sort_order')
                                ->orderColumn('sort_order')
                                ->addActionLabel(__('filament/quotes.add_item'))
                                ->schema([
                                    Select::make('product_id')
                                        ->label(__('filament/quotes.product'))
                                        ->options(function () {
                                            $tenantId = \App\Support\TenantContext::currentId();
                                            return Product::where('tenant_id', $tenantId)
                                                ->where('is_active', true)
                                                ->orderBy('name')
                                                ->pluck('name', 'id');
                                        })
                                        ->searchable()
                                        ->reactive()
                                        ->nullable()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state) {
                                                $product = Product::find($state);
                                                if ($product) {
                                                    $set('name', $product->name);
                                                    $set('unit_price', (float) $product->price);
                                                    $set('description', $product->description);
                                                }
                                            }
                                        })
                                        ->columnSpan(2),
                                    TextInput::make('name')
                                        ->label(__('filament/quotes.name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(2),
                                    Textarea::make('description')
                                        ->label(__('filament/quotes.field_item_description_label'))
                                        ->rows(2)
                                        ->nullable()
                                        ->columnSpanFull(),
                                    TextInput::make('quantity')
                                        ->label(__('filament/quotes.field_item_quantity_label'))
                                        ->numeric()
                                        ->integer()
                                        ->minValue(1)
                                        ->default(1)
                                        ->required()
                                        ->reactive(),
                                    TextInput::make('unit_price')
                                        ->label(__('filament/quotes.unit_price'))
                                        ->numeric()
                                        ->prefix(fn () => Currency::defaultSymbol())
                                        ->minValue(0)
                                        ->default(0)
                                        ->required()
                                        ->reactive(),
                                    TextInput::make('discount_percent')
                                        ->label(__('filament/quotes.discount_percent'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->default(0)
                                        ->reactive(),
                                    TextInput::make('total')
                                        ->label(__('filament/quotes.line_total'))
                                        ->prefix(fn () => Currency::defaultSymbol())
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->placeholder(__('filament/quotes.line_total_placeholder')),
                                ])
                                ->columns(4)
                                ->defaultItems(0)
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? __('filament/quotes.line_item_default_label')),
                        ]),
                ]),

                Tab::make(__('filament/quotes.tab_totals'))->icon('heroicon-o-calculator')->schema([
                    Section::make(__('sections.totals'))->schema([
                        TextInput::make('subtotal')
                            ->label(__('filament/quotes.subtotal'))
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('tax_rate')
                            ->label(__('filament/quotes.tax_rate'))
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(fn () => auth()->user()?->tenant?->defaultTaxRate() ?? 0.0),
                        TextInput::make('tax_amount')
                            ->label(__('filament/quotes.tax_amount'))
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('discount_amount')
                            ->label(__('filament/quotes.additional_discount'))
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('total')
                            ->label(__('filament/quotes.total'))
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quote_number')
                    ->label(__('filament/quotes.col_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('title')
                    ->label(__('filament/quotes.col_title'))
                    ->searchable()
                    ->limit(40),
                TextColumn::make('lead.full_name')
                    ->label(__('filament/quotes.col_lead'))
                    ->placeholder('—')
                    ->searchable(['leads.first_name', 'leads.last_name']),
                TextColumn::make('total')
                    ->label(__('filament/quotes.col_total'))
                    ->money(fn ($record) => $record->currency ?: 'USD')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/quotes.col_status'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $key      = 'filament/quotes.status_' . $state;
                        $fallback = [
                            'draft'     => 'Draft',
                            'sent'      => 'Sent',
                            'accepted'  => 'Accepted',
                            'declined'  => 'Declined',
                            'expired'   => 'Expired',
                            'converted' => 'Converted',
                        ];
                        $translated = __($key);
                        return $translated === $key ? ($fallback[$state] ?? $state) : $translated;
                    })
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'sent',
                        'success' => 'accepted',
                        'danger'  => 'declined',
                        'warning' => 'expired',
                        'primary' => 'converted',
                    ]),
                TextColumn::make('valid_until')
                    ->label(__('filament/quotes.col_valid_until'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('filament/quotes.col_created'))
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/quotes.filter_label_status'))
                    ->options([
                        'draft'     => __('filament/quotes.option_status_draft'),
                        'sent'      => __('filament/quotes.option_status_sent'),
                        'accepted'  => __('filament/quotes.option_status_accepted'),
                        'declined'  => __('filament/quotes.option_status_declined'),
                        'expired'   => __('filament/quotes.option_status_expired'),
                        'converted' => __('filament/quotes.option_status_converted'),
                    ]),
            ])
            ->actions([
                // Primary on-row actions: view + edit are the two the
                // user reaches for most often. Everything else is a
                // dropdown so the row stays visually aligned instead
                // of showing 7 different-width buttons per row.
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Quote $record) => $record->status === Quote::STATUS_DRAFT),

                \Filament\Actions\ActionGroup::make([

                Action::make('duplicate')
                    ->label(__('filament/quotes.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Quote $record) {
                        $copy = $record->replicate(['quote_number', 'public_token', 'sent_at', 'accepted_at', 'declined_at', 'signed_at', 'signed_name', 'signed_ip', 'invoice_id']);
                        $copy->status = Quote::STATUS_DRAFT;
                        $copy->title  = $record->title . ' ' . __('filament/quotes.duplicate_suffix');
                        $copy->save();

                        foreach ($record->items as $item) {
                            $copy->items()->create($item->only([
                                'product_id', 'name', 'description',
                                'quantity', 'unit_price', 'discount_percent', 'sort_order',
                            ]));
                        }

                        Notification::make()->success()->title(__('filament/quotes.notif_duplicated'))->send();

                        return redirect(QuoteResource::getUrl('edit', ['record' => $copy->id]));
                    }),

                Action::make('send')
                    ->label(__('filament/quotes.send'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn (Quote $record) => in_array($record->status, [Quote::STATUS_DRAFT, Quote::STATUS_SENT], true))
                    ->requiresConfirmation()
                    ->modalHeading(__('filament/quotes.send_modal_heading'))
                    ->modalDescription(fn (Quote $record) => __('filament/quotes.send_modal_description', ['recipient' => $record->lead?->email ?? __('filament/quotes.send_modal_recipient_fallback')]))
                    ->action(function (Quote $record) {
                        if (! $record->lead?->email) {
                            Notification::make()->danger()->title(__('filament/quotes.notif_lead_no_email'))->send();
                            return;
                        }
                        try {
                            Mail::raw(
                                __('mail.quote_send_body', [
                                    'name'   => $record->lead->first_name,
                                    'number' => $record->quote_number,
                                    'url'    => $record->publicUrl(),
                                ]),
                                function ($m) use ($record) {
                                    $m->to($record->lead->email)->subject(__('mail.quote_send_subject', ['number' => $record->quote_number]));
                                }
                            );
                            $record->markSent();
                            Notification::make()->success()->title(__('filament/quotes.notif_sent'))->send();
                        } catch (\Throwable $e) {
                            Log::error('[quote.send] ' . $e->getMessage());
                            Notification::make()->danger()->title(__('filament/quotes.notif_send_failed', ['error' => $e->getMessage()]))->send();
                        }
                    }),

                Action::make('download_pdf')
                    ->label(__('filament/quotes.download_pdf'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Quote $record) => route('quote.pdf', $record->public_token))
                    ->openUrlInNewTab(),

                Action::make('convert_to_invoice')
                    ->label(__('filament/quotes.convert_to_invoice'))
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Quote $record) => $record->status === Quote::STATUS_ACCEPTED)
                    ->requiresConfirmation()
                    ->action(function (Quote $record) {
                        $invoice = $record->convertToInvoice();
                        Notification::make()->success()->title(__('filament/quotes.notif_invoice_created', ['number' => $invoice->invoice_number]))->send();
                        return redirect(InvoiceResource::getUrl('edit', ['record' => $invoice->id]));
                    }),

                DeleteAction::make()
                    ->visible(fn (Quote $record) => $record->status === Quote::STATUS_DRAFT),
                ])
                    ->label(__('filament/quotes.more'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view'   => Pages\ViewQuote::route('/{record}'),
            'edit'   => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
