<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrackingSnippetResource\Pages;
use App\Models\TrackingSnippet;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrackingSnippetResource extends Resource
{
    protected static ?string $model                          = TrackingSnippet::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup  = 'Tools';
    protected static ?int    $navigationSort                 = 20;

    public static function getNavigationLabel(): string
    {
        return __('filament/tracking_snippets.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/tracking_snippets.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/tracking_snippets.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.tracker'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/tracking_snippets.name'))
                        ->required()
                        ->columnSpanFull()
                        ->placeholder(__('filament/tracking_snippets.name_placeholder')),
                    TagsInput::make('allowed_domains')
                        ->label(__('filament/tracking_snippets.allowed_domains'))
                        ->placeholder(__('filament/tracking_snippets.allowed_domains_placeholder'))
                        ->helperText(__('filament/tracking_snippets.allowed_domains_helper'))
                        ->columnSpanFull(),
                    Toggle::make('is_active')->label(__('filament/tracking_snippets.active'))->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/tracking_snippets.name'))->searchable()->sortable(),
                TextColumn::make('uuid')
                    ->label(__('filament/tracking_snippets.col_id'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state) => substr($state, 0, 8) . '…'),
                TextColumn::make('views_count')
                    ->label(__('filament/tracking_snippets.col_views'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_viewed_at')
                    ->label(__('filament/tracking_snippets.col_last_view'))
                    ->since()
                    ->placeholder('—')
                    ->sortable(),
                IconColumn::make('is_active')->boolean()->label(__('filament/tracking_snippets.active')),
                TextColumn::make('created_at')->label(__('filament/tracking_snippets.created_at'))->date()->sortable()->toggleable(),
            ])
            ->actions([
                Action::make('get_snippet')
                    ->label(__('filament/tracking_snippets.get_snippet'))
                    ->icon('heroicon-o-code-bracket')
                    ->color('success')
                    ->modalHeading(__('filament/tracking_snippets.snippet_modal_heading'))
                    ->modalDescription(__('filament/tracking_snippets.snippet_modal_description'))
                    // Modal markup + static styles extracted to a Blade
                    // view + dedicated stylesheet so this file no longer
                    // carries inline style="" attributes (translatable-
                    // strings + no-inline-styles rule).  See:
                    //   resources/views/filament/resources/tracking-snippets/snippet-modal.blade.php
                    //   public/css/views/filament/resources/tracking-snippets/snippet-modal.css
                    ->modalContent(fn (TrackingSnippet $record) => new \Illuminate\Support\HtmlString(
                        view('filament.resources.tracking-snippets.snippet-modal', [
                            'record' => $record,
                        ])->render()
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/tracking_snippets.snippet_modal_close')),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTrackingSnippets::route('/'),
            'create' => Pages\CreateTrackingSnippet::route('/create'),
            'edit'   => Pages\EditTrackingSnippet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }
}
