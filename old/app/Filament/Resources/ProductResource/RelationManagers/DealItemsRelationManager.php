<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DealItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'dealItems';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-currency-dollar';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/products.deal_items_relation_title');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        $currency = fn ($record) => $record?->lead?->deal_currency ?: 'USD';

        return $table
            ->columns([
                TextColumn::make('lead.full_name')
                    ->label(__('filament/products.col_lead'))
                    ->searchable(['leads.first_name', 'leads.last_name'])
                    ->weight('medium')
                    ->placeholder('—'),
                TextColumn::make('quantity')
                    ->label(__('filament/products.col_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(__('filament/products.col_unit_price'))
                    ->money(fn ($record) => $currency($record))
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('filament/products.col_total'))
                    ->money(fn ($record) => $currency($record))
                    ->weight('medium')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament/products.col_deal_item_created_at'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
