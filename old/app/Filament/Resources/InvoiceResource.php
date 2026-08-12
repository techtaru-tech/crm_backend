<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Lead;
use App\Models\Product;
use App\Support\Currency;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class InvoiceResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = Invoice::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-banknotes';
    protected static string|UnitEnum|null    $navigationGroup = 'Sales';
    protected static ?int    $navigationSort  = 11;

    public static function getNavigationLabel(): string
    {
        return __('filament/invoices.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/invoices.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/invoices.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        // Unconditional `where('tenant_id', $tenantId)` instead of
        // `when($tenantId, ...)`.  When $tenantId is null, the older
        // pattern returned EVERY tenant's invoices via the parent
        // BelongsToTenant scope's SA-cross-tenant carve-out.  The
        // unconditional version hard-fails to zero rows
        // (WHERE tenant_id IS NULL matches nothing in MySQL) so a
        // future code path that invokes this Resource without
        // current_tenant bound can't accidentally cross tenants.
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make(__('filament/invoices.tabs_outer'))->columnSpanFull()->tabs([
                Tab::make(__('filament/invoices.tab_info'))->icon('heroicon-o-information-circle')->schema([
                    Section::make(__('sections.invoice_details'))->schema([
                        Select::make('lead_id')
                            ->label(__('filament/invoices.lead'))
                            ->options(function () {
                                $tenantId = \App\Support\TenantContext::currentId();
                                return Lead::where('tenant_id', $tenantId)
                                    ->orderBy('first_name')
                                    ->get()
                                    ->mapWithKeys(fn ($l) => [$l->id => trim($l->first_name . ' ' . $l->last_name) . ' — ' . ($l->email ?? 'no email')]);
                            })
                            ->searchable()
                            ->nullable(),
                        Select::make('company_id')
                            ->label(__('filament/invoices.company'))
                            ->options(function () {
                                $tenantId = \App\Support\TenantContext::currentId();
                                return Company::where('tenant_id', $tenantId)->orderBy('name')->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable(),
                        Select::make('currency')
                            ->label(__('filament/invoices.field_currency_label'))
                            ->options(\App\Support\Currency::options())
                            ->searchable()
                            ->default(fn () => \App\Support\Currency::forTenant(auth()->user()?->tenant))
                            ->required(),
                        Select::make('status')
                            ->label(__('filament/invoices.field_status_label'))
                            ->options([
                                Invoice::STATUS_DRAFT     => __('filament/invoices.option_status_draft'),
                                Invoice::STATUS_SENT      => __('filament/invoices.option_status_sent'),
                                Invoice::STATUS_PARTIAL   => __('filament/invoices.option_status_partial'),
                                Invoice::STATUS_PAID      => __('filament/invoices.option_status_paid'),
                                Invoice::STATUS_OVERDUE   => __('filament/invoices.option_status_overdue'),
                                Invoice::STATUS_CANCELLED => __('filament/invoices.option_status_cancelled'),
                                Invoice::STATUS_REFUNDED  => __('filament/invoices.option_status_refunded'),
                            ])
                            ->default(Invoice::STATUS_DRAFT)
                            ->required(),
                        DatePicker::make('issued_date')
                            ->label(__('filament/invoices.issued'))
                            ->default(now())
                            ->required(),
                        DatePicker::make('due_date')
                            ->label(__('filament/invoices.due_date'))
                            ->default(now()->addDays(14))
                            ->nullable(),
                        Textarea::make('notes')
                            ->label(__('filament/invoices.field_notes_label'))
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
                ]),

                Tab::make(__('filament/invoices.tab_line_items'))->icon('heroicon-o-list-bullet')->schema([
                    Section::make(__('sections.items'))->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->label('')
                            ->reorderable('sort_order')
                            ->orderColumn('sort_order')
                            ->addActionLabel(__('filament/invoices.add_item'))
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('filament/invoices.product'))
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
                                    ->label(__('filament/invoices.field_item_name_label'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Textarea::make('description')
                                    ->label(__('filament/invoices.field_item_description_label'))
                                    ->rows(2)
                                    ->nullable()
                                    ->columnSpanFull(),
                                TextInput::make('quantity')
                                    ->label(__('filament/invoices.field_item_quantity_label'))
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->reactive(),
                                TextInput::make('unit_price')->label(__('filament/invoices.unit_price'))->numeric()->prefix(fn () => Currency::defaultSymbol())->minValue(0)->default(0)->required()->reactive(),
                                TextInput::make('discount_percent')->label(__('filament/invoices.discount_percent'))->numeric()->minValue(0)->maxValue(100)->default(0)->reactive(),
                                TextInput::make('total')->label(__('filament/invoices.line_total'))->prefix(fn () => Currency::defaultSymbol())->disabled()->dehydrated(false)->placeholder(__('filament/invoices.line_total_placeholder')),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? __('filament/invoices.line_item_default_label')),
                    ]),
                ]),

                Tab::make(__('filament/invoices.tab_totals'))->icon('heroicon-o-calculator')->schema([
                    Section::make(__('sections.totals'))->schema([
                        TextInput::make('subtotal')->label(__('filament/invoices.field_subtotal_label'))->prefix(fn () => Currency::defaultSymbol())->disabled()->dehydrated(false),
                        TextInput::make('tax_rate')->label(__('filament/invoices.tax_rate'))->numeric()->suffix('%')->minValue(0)->maxValue(100)->default(fn () => auth()->user()?->tenant?->defaultTaxRate() ?? 0.0),
                        TextInput::make('tax_amount')->label(__('filament/invoices.field_tax_amount_label'))->prefix(fn () => Currency::defaultSymbol())->disabled()->dehydrated(false),
                        TextInput::make('discount_amount')->label(__('filament/invoices.additional_discount'))->prefix(fn () => Currency::defaultSymbol())->numeric()->minValue(0)->default(0),
                        TextInput::make('total')->label(__('filament/invoices.field_total_label'))->prefix(fn () => Currency::defaultSymbol())->disabled()->dehydrated(false),
                        TextInput::make('amount_paid')->label(__('filament/invoices.field_amount_paid_label'))->prefix(fn () => Currency::defaultSymbol())->disabled()->dehydrated(false),
                    ])->columns(2),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label(__('filament/invoices.col_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('lead.full_name')
                    ->label(__('filament/invoices.col_lead'))
                    ->placeholder('—')
                    ->searchable(['leads.first_name', 'leads.last_name']),
                TextColumn::make('total')
                    ->label(__('filament/invoices.col_total'))
                    ->money(fn ($record) => $record->currency ?: 'USD')
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label(__('filament/invoices.col_paid'))
                    ->money(fn ($record) => $record->currency ?: 'USD'),
                TextColumn::make('due_date')
                    ->label(__('filament/invoices.col_due'))
                    ->date()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/invoices.col_status'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $key      = 'filament/invoices.status_' . $state;
                        $fallback = [
                            'draft'     => 'Draft',
                            'sent'      => 'Sent',
                            'partial'   => 'Partial',
                            'overdue'   => 'Overdue',
                            'paid'      => 'Paid',
                            'cancelled' => 'Cancelled',
                            'refunded'  => 'Refunded',
                        ];
                        $translated = __($key);
                        return $translated === $key ? ($fallback[$state] ?? $state) : $translated;
                    })
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'sent',
                        'warning' => ['partial', 'overdue'],
                        'success' => 'paid',
                        'danger'  => ['cancelled', 'refunded'],
                    ]),
                TextColumn::make('created_at')
                    ->label(__('filament/invoices.col_created_at'))
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/invoices.filter_label_status'))
                    ->options([
                        Invoice::STATUS_DRAFT     => __('filament/invoices.option_status_draft'),
                        Invoice::STATUS_SENT      => __('filament/invoices.option_status_sent'),
                        Invoice::STATUS_PARTIAL   => __('filament/invoices.option_status_partial'),
                        Invoice::STATUS_PAID      => __('filament/invoices.option_status_paid'),
                        Invoice::STATUS_OVERDUE   => __('filament/invoices.option_status_overdue'),
                        Invoice::STATUS_CANCELLED => __('filament/invoices.option_status_cancelled'),
                        Invoice::STATUS_REFUNDED  => __('filament/invoices.option_status_refunded'),
                    ]),
            ])
            ->actions([
                // Primary on-row actions (View + Edit) stay inline; every
                // other invoice action (send, record payment, mark paid,
                // download PDF, cancel, refund, delete) goes into a single
                // ⋮ "More" dropdown so row heights stay uniform and the
                // table doesn't need horizontal scroll.
                ViewAction::make(),
                EditAction::make()->visible(fn (Invoice $record) => $record->status === Invoice::STATUS_DRAFT),

                \Filament\Actions\ActionGroup::make([

                Action::make('send')
                    ->label(__('filament/invoices.send'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn (Invoice $record) => in_array($record->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE], true))
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        if (! $record->lead?->email) {
                            Notification::make()->danger()->title(__('filament/invoices.notify_no_email'))->send();
                            return;
                        }
                        try {
                            Mail::raw(
                                __('mail.invoice_send_body', [
                                    'name'   => $record->lead->first_name,
                                    'number' => $record->invoice_number,
                                    'url'    => $record->publicUrl(),
                                ]),
                                function ($m) use ($record) {
                                    $m->to($record->lead->email)->subject(__('mail.invoice_send_subject', ['number' => $record->invoice_number]));
                                }
                            );
                            $record->markSent();
                            Notification::make()->success()->title(__('filament/invoices.notify_sent'))->send();
                        } catch (\Throwable $e) {
                            Log::error('[invoice.send] ' . $e->getMessage());
                            Notification::make()->danger()->title(__('filament/invoices.notify_send_failed_prefix') . $e->getMessage())->send();
                        }
                    }),

                Action::make('download_pdf')
                    ->label(__('filament/invoices.download_pdf'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record->public_token))
                    ->openUrlInNewTab(),

                Action::make('record_payment')
                    ->label(__('filament/invoices.record_payment'))
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->amountDue() > 0 && ! in_array($record->status, [Invoice::STATUS_CANCELLED, Invoice::STATUS_REFUNDED], true))
                    ->form(fn (Invoice $record) => [
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
                            ->minValue(0.01)
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->default(fn () => $record->amountDue())
                            ->required(),
                        Select::make('status')
                            ->label(__('filament/invoices.field_payment_status_label'))
                            ->options([
                                InvoicePayment::STATUS_SUCCEEDED => __('filament/invoices.option_payment_succeeded'),
                                InvoicePayment::STATUS_PENDING   => __('filament/invoices.option_payment_pending'),
                                InvoicePayment::STATUS_FAILED    => __('filament/invoices.option_payment_failed'),
                            ])
                            ->default(InvoicePayment::STATUS_SUCCEEDED)
                            ->required(),
                        TextInput::make('external_id')
                            ->label(__('filament/invoices.external_reference'))
                            ->maxLength(255)
                            ->nullable(),
                        DateTimePicker::make('paid_at')
                            ->label(__('filament/invoices.field_paid_at_label'))
                            ->default(now())
                            ->seconds(false),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        InvoicePayment::create([
                            'tenant_id'   => $record->tenant_id,
                            'invoice_id'  => $record->id,
                            'gateway'     => $data['gateway'],
                            'amount'      => $data['amount'],
                            'currency'    => $record->currency,
                            'status'      => $data['status'],
                            'external_id' => $data['external_id'] ?? null,
                            'paid_at'     => $data['paid_at'] ?? null,
                        ]);
                        Notification::make()->success()->title(__('filament/invoices.notify_payment_recorded'))->send();
                    }),

                Action::make('mark_as_paid')
                    ->label(__('filament/invoices.mark_as_paid'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->amountDue() > 0 && ! in_array($record->status, [Invoice::STATUS_CANCELLED, Invoice::STATUS_REFUNDED], true))
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        InvoicePayment::create([
                            'tenant_id'  => $record->tenant_id,
                            'invoice_id' => $record->id,
                            'gateway'    => 'manual',
                            'amount'     => $record->amountDue(),
                            'currency'   => $record->currency,
                            'status'     => InvoicePayment::STATUS_SUCCEEDED,
                            'paid_at'    => now(),
                        ]);
                        Notification::make()->success()->title(__('filament/invoices.notify_marked_paid'))->send();
                    }),

                Action::make('cancel')
                    ->label(__('filament/invoices.cancel'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Invoice $record) => ! in_array($record->status, [Invoice::STATUS_CANCELLED, Invoice::STATUS_PAID, Invoice::STATUS_REFUNDED], true))
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        $record->update(['status' => Invoice::STATUS_CANCELLED]);
                        Notification::make()->success()->title(__('filament/invoices.notify_cancelled'))->send();
                    }),

                Action::make('refund')
                    ->label(__('filament/invoices.refund'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (Invoice $record) => $record->status === Invoice::STATUS_PAID)
                    ->requiresConfirmation()
                    ->modalHeading(__('filament/invoices.refund_modal_heading'))
                    ->modalDescription(__('filament/invoices.refund_modal_description'))
                    ->action(function (Invoice $record) {
                        InvoicePayment::create([
                            'tenant_id'  => $record->tenant_id,
                            'invoice_id' => $record->id,
                            'gateway'    => 'manual',
                            'amount'     => -1 * (float) $record->amount_paid,
                            'currency'   => $record->currency,
                            'status'     => InvoicePayment::STATUS_REFUNDED,
                            'paid_at'    => now(),
                        ]);
                        $record->update(['status' => Invoice::STATUS_REFUNDED]);
                        Notification::make()->success()->title(__('filament/invoices.notify_refunded'))->send();
                    }),

                DeleteAction::make()->visible(fn (Invoice $record) => $record->status === Invoice::STATUS_DRAFT),
                ])
                    ->label(__('filament/invoices.more'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_sent')
                        ->label(__('filament/invoices.mark_as_sent'))
                        ->icon('heroicon-o-paper-airplane')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === Invoice::STATUS_DRAFT) {
                                    $record->markSent();
                                }
                            }
                            Notification::make()->success()->title(__('filament/invoices.notify_bulk_marked_sent'))->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
