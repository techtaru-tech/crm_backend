<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\Product;
use App\Support\Currency;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DealItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'dealItems';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-cube';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/leads.line_items_title');
    }

    public function form(Schema $schema): Schema
    {
        $tenantId = fn () => $this->ownerRecord->tenant_id
            ?? AppSupportTenantContext::currentId();

        return $schema->components([
            Select::make('product_id')
                ->label(__('filament/leads.product'))
                ->options(fn () => Product::where('tenant_id', $tenantId())
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->searchable()
                ->reactive()
                ->nullable()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (! $state) {
                        return;
                    }
                    $product = Product::find($state);
                    if ($product) {
                        $set('name', $product->name);
                        $set('unit_price', (float) $product->price);
                    }
                }),
            TextInput::make('name')
                ->label(__('filament/leads.item_name'))
                ->required()
                ->maxLength(150),
            TextInput::make('unit_price')
                ->label(__('filament/leads.unit_price'))
                ->numeric()
                ->prefix(fn () => Currency::defaultSymbol())
                ->minValue(0)
                ->required(),
            TextInput::make('quantity')
                ->label(__('filament/leads.quantity'))
                ->numeric()
                ->integer()
                ->minValue(1)
                ->default(1)
                ->required(),
            TextInput::make('discount_percent')
                ->label(__('filament/leads.discount_percent'))
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(0)
                ->suffix('%'),
        ]);
    }

    public function table(Table $table): Table
    {
        $currency = fn () => $this->ownerRecord->deal_currency ?: 'USD';

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/leads.item_name'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('unit_price')
                    ->label(__('filament/leads.unit_price'))
                    ->money($currency())
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('filament/leads.quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_percent')
                    ->label(__('filament/leads.discount'))
                    ->suffix('%')
                    ->placeholder('0%'),
                TextColumn::make('total')
                    ->label(__('filament/leads.total'))
                    ->money($currency())
                    ->weight('medium')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament/leads.add_line_item'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['tenant_id'] = $this->ownerRecord->tenant_id;
                        $data['lead_id']   = $this->ownerRecord->id;
                        $data['total']     = 0; // recalculated by DealItem::boot saving hook
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->defaultSort('id', 'asc');
    }
}
