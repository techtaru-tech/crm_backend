<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\ApiKeyResource\Pages;
use App\Models\ApiKey;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ApiKeyResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'api_keys';
    protected static ?string $model = ApiKey::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/api_keys.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/api_keys.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/api_keys.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('filament/api_keys.key_name'))
                ->required()
                ->maxLength(100)
                ->placeholder(__('filament/api_keys.key_name_placeholder'))
                ->helperText(__('filament/api_keys.key_name_help')),

            CheckboxList::make('scopes')
                ->label(__('filament/api_keys.permissions'))
                ->options(ApiKey::scopeLabels())
                ->columns(2)
                ->helperText(__('filament/api_keys.permissions_help')),

            TextInput::make('rate_limit_per_hour')
                ->label(__('filament/api_keys.rate_limit'))
                ->numeric()
                ->default(1000)
                ->minValue(1)
                ->maxValue(100000),

            DateTimePicker::make('expires_at')
                ->label(__('filament/api_keys.expires_at'))
                ->helperText(__('filament/api_keys.expires_at_help')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('tenant_id', \App\Support\TenantContext::currentId()))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/api_keys.col_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key_prefix')
                    ->label(__('filament/api_keys.col_key_prefix'))
                    ->formatStateUsing(fn($state) => $state . '••••••••••••••••')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('rate_limit_per_hour')
                    ->label(__('filament/api_keys.col_rate_limit'))
                    ->suffix(__('filament/api_keys.suffix_per_hour'))
                    ->sortable(),

                TextColumn::make('last_used_at')
                    ->label(__('filament/api_keys.col_last_used'))
                    ->since()
                    ->placeholder(__('filament/api_keys.never'))
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label(__('filament/api_keys.col_expires'))
                    ->dateTime()
                    ->placeholder(__('filament/api_keys.never'))
                    ->sortable(),

                IconColumn::make('revoked_at')
                    ->label(__('filament/api_keys.col_active'))
                    ->boolean()
                    ->state(fn($record) => $record->isActive())
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('filament/api_keys.col_created'))
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Action::make('revoke')
                    ->label(__('filament/api_keys.revoke'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn($record) => $record->isRevoked())
                    ->action(function ($record) {
                        $record->update(['revoked_at' => now()]);
                        Notification::make()->title(__('notifications.api_key_revoked'))->danger()->send();
                    }),

                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('filament/api_keys.empty_heading'))
            ->emptyStateDescription(__('filament/api_keys.empty_description'));
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
        ];
    }
}
