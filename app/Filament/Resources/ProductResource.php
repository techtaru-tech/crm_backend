<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Support\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static string|UnitEnum|null $navigationGroup = 'Tools';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/products.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/products.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/products.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.product_details'))->schema([
                TextInput::make('name')
                    ->label(__('filament/products.field_name_label'))
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),
                TextInput::make('sku')->label(__('filament/products.sku'))->maxLength(80),
                TextInput::make('category')
                    ->label(__('filament/products.field_category_label'))
                    ->maxLength(100),
                Textarea::make('description')
                    ->label(__('filament/products.field_description_label'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('sections.pricing'))->schema([
                TextInput::make('price')
                    ->label(__('filament/products.field_price_label'))
                    ->numeric()
                    ->prefix(fn () => Currency::defaultSymbol())
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                Select::make('currency')
                    ->label(__('filament/products.field_currency_label'))
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'GBP' => 'GBP',
                        'INR' => 'INR',
                        'AED' => 'AED',
                        'SAR' => 'SAR',
                    ])
                    ->default('USD')
                    ->required(),
                Toggle::make('is_active')->default(true)->label(__('filament/products.active')),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/products.col_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('sku')
                    ->label(__('filament/products.sku'))
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('price')
                    ->label(__('filament/products.col_price'))
                    ->money(fn($record) => $record->currency ?: 'USD')
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('filament/products.col_category'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('filament/products.active')),
                TextColumn::make('created_at')
                    ->label(__('filament/products.col_created_at'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('filament/products.filter_label_category'))
                    ->options(function () {
                        $tenantId = \App\Support\TenantContext::currentId();
                        return Product::where('tenant_id', $tenantId)
                            ->whereNotNull('category')
                            ->distinct()
                            ->pluck('category', 'category');
                    }),
                TernaryFilter::make('is_active')
                    ->label(__('filament/products.active'))
                    ->trueLabel(__('filament/products.active_only'))
                    ->falseLabel(__('filament/products.inactive_only')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DealItemsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view'   => Pages\ViewProduct::route('/{record}'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
