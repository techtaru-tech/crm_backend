<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\LocaleResource\Pages;
use App\Models\Locale;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\File;
use UnitEnum;

/**
 * Operator-facing CRUD for the `locales` table.  Adding a language
 * becomes:
 *   /super-admin/locales → New language → fill form → save.
 *
 * No file editing, no code deploy.  The LocaleServiceProvider reads
 * this table on every boot (cached 30 min, busted on save).
 */
class LocaleResource extends Resource
{
    protected static ?string $model = Locale::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-language';
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 75;
    public static function getLabel(): string
    {
        return __('filament/sa_locales.language');
    }

    public static function getPluralLabel(): string
    {
        return __('filament/sa_locales.languages');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.language_details'))
                ->schema([
                    TextInput::make('code')
                        ->label(__('filament/sa_locales.code'))
                        ->required()
                        ->maxLength(8)
                        ->unique(ignoreRecord: true)
                        ->alphaDash()
                        ->helperText(__('filament/sa_locales.code_helper'))
                        ->placeholder('fr')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('code', strtolower((string) $state))),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(60)
                        ->label(__('filament/sa_locales.native_name'))
                        ->helperText(__('filament/sa_locales.native_name_helper'))
                        ->placeholder(__('filament/sa_locales.native_name_placeholder')),

                    TextInput::make('english_name')
                        ->maxLength(60)
                        ->label(__('filament/sa_locales.english_name'))
                        ->helperText(__('filament/sa_locales.english_name_helper'))
                        ->placeholder(__('filament/sa_locales.english_name_placeholder')),

                    TextInput::make('flag')
                        ->maxLength(8)
                        ->label(__('filament/sa_locales.flag_label'))
                        ->helperText(__('filament/sa_locales.flag_helper'))
                        ->placeholder('🇫🇷'),

                    TextInput::make('sort_order')
                        ->label(__('filament/sa_locales.sort_order'))
                        ->numeric()
                        ->default(100)
                        ->minValue(0)
                        ->maxValue(9999)
                        ->helperText(__('filament/sa_locales.sort_order_helper')),
                ])
                ->columns(2),

            Section::make(__('sections.behaviour'))
                ->schema([
                    Toggle::make('enabled')
                        ->label(__('filament/sa_locales.enabled'))
                        ->default(true)
                        ->helperText(__('filament/sa_locales.enabled_helper')),

                    Toggle::make('rtl')
                        ->label(__('filament/sa_locales.rtl_label'))
                        ->helperText(__('filament/sa_locales.rtl_helper')),

                    Toggle::make('is_default')
                        ->label(__('filament/sa_locales.is_default_label'))
                        ->helperText(__('filament/sa_locales.is_default_helper')),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('flag')->label(__('filament/sa_locales.flag'))->size('lg')->alignCenter(),
                TextColumn::make('code')
                    ->label(__('filament/sa_locales.code'))
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->fontFamily('mono'),
                TextColumn::make('name')->label(__('filament/sa_locales.column_native'))->searchable()->sortable()->weight('medium'),
                TextColumn::make('english_name')->label(__('filament/sa_locales.column_english'))->sortable()->toggleable(),
                IconColumn::make('enabled')
                    ->boolean()
                    ->label(__('filament/sa_locales.column_on')),
                IconColumn::make('rtl')
                    ->boolean()
                    ->label(__('filament/sa_locales.column_rtl'))
                    ->toggleable(),
                IconColumn::make('is_default')
                    ->boolean()
                    ->label(__('filament/sa_locales.column_default')),
                TextColumn::make('sort_order')->label(__('filament/sa_locales.column_order'))->sortable()->alignRight(),

                // Admin-UI coverage indicator: is there a lang/{code}/
                // directory shipped with the app?  If not, the admin
                // panel renders in English for that locale (the
                // marketing site still respects per-locale overrides
                // from the Landing / Static Page editors).
                TextColumn::make('lang_files_status')
                    ->label(__('filament/sa_locales.column_admin_ui'))
                    ->state(function (Locale $record) {
                        $path = base_path('lang/' . $record->code);
                        if ($record->code === 'en') return 'shipped';
                        return File::isDirectory($path) ? 'installed' : 'english-fallback';
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'shipped', 'installed'   => 'success',
                        'english-fallback'       => 'warning',
                        default                  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'shipped'           => __('filament/sa_locales.admin_ui_shipped'),
                        'installed'         => __('filament/sa_locales.admin_ui_installed'),
                        'english-fallback'  => __('filament/sa_locales.admin_ui_english_fallback'),
                        default             => $state,
                    })
                    ->tooltip(__('filament/sa_locales.admin_ui_tooltip')),
            ])
            ->defaultSort('sort_order')
            ->actions([
                EditAction::make(),

                // Quick "Make default" one-click — clears the flag on
                // the previously-default row so only one locale is
                // ever marked default.
                Action::make('make_default')
                    ->label(__('filament/sa_locales.make_default'))
                    ->icon('heroicon-o-star')
                    ->color('gray')
                    ->visible(fn (Locale $record) => ! $record->is_default)
                    ->requiresConfirmation()
                    ->modalDescription(fn (Locale $record) => __('filament/sa_locales.make_default_description', ['name' => $record->name]))
                    ->action(function (Locale $record) {
                        Locale::query()->where('is_default', true)->update(['is_default' => false]);
                        $record->update(['is_default' => true, 'enabled' => true]);
                        Locale::bustCache();
                        Notification::make()->success()
                            ->title(__('filament/sa_locales.default_language_set', ['name' => $record->name]))
                            ->send();
                    }),

                DeleteAction::make()
                    ->visible(fn (Locale $record) => ! $record->is_default)
                    ->modalDescription(__('filament/sa_locales.delete_description')),
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
            'index'  => Pages\ListLocales::route('/'),
            'create' => Pages\CreateLocale::route('/create'),
            'edit'   => Pages\EditLocale::route('/{record}/edit'),
        ];
    }
}
