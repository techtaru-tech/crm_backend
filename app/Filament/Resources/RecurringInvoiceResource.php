<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\RecurringInvoiceResource\Pages;
use App\Models\Company;
use App\Models\Lead;
use App\Models\RecurringInvoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Tenant-facing CRUD for Recurring Invoices ("dues"). Each row is a standing
 * monthly/annual charge against a member (lead/company). The daily command
 * `invoices:process-recurring` materialises real Invoices from these and sends
 * due-date reminders. See App\Models\RecurringInvoice.
 */
class RecurringInvoiceResource extends Resource
{
    use HasRolePermissions;

    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = RecurringInvoice::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-arrow-path';
    protected static string|UnitEnum|null    $navigationGroup = 'Sales';
    protected static ?int    $navigationSort  = 12;

    public static function getNavigationLabel(): string
    {
        return __('filament/recurring_invoices.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/recurring_invoices.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/recurring_invoices.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        // Hard tenant pin (mirrors InvoiceResource) so a context without a
        // bound tenant fails closed to zero rows rather than leaking.
        $tenantId = \App\Support\TenantContext::currentId();

        return parent::getEloquentQuery()
            ->where('tenant_id', $tenantId)
            ->with(['lead', 'company']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament/recurring_invoices.section_schedule'))
                ->description(__('filament/recurring_invoices.section_schedule_desc'))
                ->columns(2)
                ->schema([
                    Select::make('lead_id')
                        ->label(__('filament/recurring_invoices.field_lead'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();

                            return Lead::where('tenant_id', $tenantId)
                                ->orderBy('first_name')
                                ->get()
                                ->mapWithKeys(fn ($l) => [$l->id => trim($l->first_name . ' ' . $l->last_name) . ' — ' . ($l->email ?? 'no email')]);
                        })
                        ->searchable()
                        ->required(),
                    Select::make('company_id')
                        ->label(__('filament/recurring_invoices.field_company'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();

                            return Company::where('tenant_id', $tenantId)->orderBy('name')->pluck('name', 'id');
                        })
                        ->searchable()
                        ->nullable(),
                    TextInput::make('title')
                        ->label(__('filament/recurring_invoices.field_title'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('amount')
                        ->label(__('filament/recurring_invoices.field_amount'))
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    Select::make('currency')
                        ->label(__('filament/recurring_invoices.field_currency'))
                        ->options([
                            'USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP',
                            'INR' => 'INR', 'AED' => 'AED', 'SAR' => 'SAR',
                            'AUD' => 'AUD', 'CAD' => 'CAD',
                        ])
                        ->default('USD')
                        ->required(),
                    Select::make('interval')
                        ->label(__('filament/recurring_invoices.field_interval'))
                        ->options([
                            RecurringInvoice::INTERVAL_MONTH => __('filament/recurring_invoices.interval_month'),
                            RecurringInvoice::INTERVAL_YEAR  => __('filament/recurring_invoices.interval_year'),
                        ])
                        ->default(RecurringInvoice::INTERVAL_MONTH)
                        ->required(),
                    TextInput::make('anchor_day')
                        ->label(__('filament/recurring_invoices.field_anchor_day'))
                        ->helperText(__('filament/recurring_invoices.field_anchor_day_help'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(28)
                        ->nullable(),
                    DatePicker::make('next_run_date')
                        ->label(__('filament/recurring_invoices.field_next_run_date'))
                        ->required()
                        ->default(now()),
                    TextInput::make('due_days')
                        ->label(__('filament/recurring_invoices.field_due_days'))
                        ->helperText(__('filament/recurring_invoices.field_due_days_help'))
                        ->numeric()
                        ->minValue(0)
                        ->default(7)
                        ->required(),
                    Toggle::make('auto_send')
                        ->label(__('filament/recurring_invoices.field_auto_send'))
                        ->helperText(__('filament/recurring_invoices.field_auto_send_help')),
                    Toggle::make('active')
                        ->label(__('filament/recurring_invoices.field_active'))
                        ->default(true),
                    Textarea::make('notes')
                        ->label(__('filament/recurring_invoices.field_notes'))
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament/recurring_invoices.col_title'))
                    ->searchable()
                    ->limit(40),
                TextColumn::make('member')
                    ->label(__('filament/recurring_invoices.col_member'))
                    ->state(fn (RecurringInvoice $record) => $record->lead
                        ? trim($record->lead->first_name . ' ' . $record->lead->last_name)
                        : ($record->company?->name ?? '—')),
                TextColumn::make('amount')
                    ->label(__('filament/recurring_invoices.col_amount'))
                    ->money(fn (RecurringInvoice $record) => $record->currency ?: 'USD')
                    ->sortable(),
                TextColumn::make('interval')
                    ->label(__('filament/recurring_invoices.col_interval'))
                    ->badge(),
                TextColumn::make('next_run_date')
                    ->label(__('filament/recurring_invoices.col_next_run'))
                    ->date()
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament/recurring_invoices.col_active'))
                    ->boolean(),
            ])
            ->defaultSort('next_run_date', 'asc')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecurringInvoices::route('/'),
            'create' => Pages\CreateRecurringInvoice::route('/create'),
            'edit'   => Pages\EditRecurringInvoice::route('/{record}/edit'),
        ];
    }
}
