<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\StaticPageResource\Pages;
use App\Models\StaticPage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * SuperAdmin CRUD for SaaS-level static marketing pages
 * (About / Terms / Privacy / Contact + any custom ones).
 *
 * Public URL: /pages/{slug}
 * Surfaced in the marketing nav when `show_in_nav` is on, in the
 * footer when `show_in_footer` is on.
 */
class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 70;
    public static function getLabel(): string
    {
        return __('filament/sa_static_pages.static_page');
    }

    public static function getPluralLabel(): string
    {
        return __('filament/sa_static_pages.static_pages');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.page_content'))
                ->description(__('filament/sa_static_pages.page_content_description'))
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(200)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, callable $set, callable $get, ?StaticPage $record) {
                            // Only auto-fill slug on CREATE when the
                            // operator hasn't hand-typed one.
                            if ($record?->exists) return;
                            if ($state && empty($get('slug'))) {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->label(__('filament/sa_static_pages.slug'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(140)
                        ->unique(ignoreRecord: true)
                        ->prefix('/pages/')
                        ->helperText(__('filament/sa_static_pages.slug_helper')),

                    Textarea::make('excerpt')
                        ->label(__('filament/sa_static_pages.excerpt'))
                        ->maxLength(500)
                        ->rows(2)
                        ->helperText(__('filament/sa_static_pages.excerpt_helper'))
                        ->columnSpanFull(),

                    RichEditor::make('content_html')
                        ->label(__('filament/sa_static_pages.content'))
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'blockquote', 'codeBlock',
                            'bulletList', 'orderedList',
                            'link', 'undo', 'redo',
                        ])
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make(__('sections.seo'))
                ->description(__('filament/sa_static_pages.seo_description'))
                ->collapsed()
                ->schema([
                    Textarea::make('meta_description')
                        ->label(__('filament/sa_static_pages.meta_description'))
                        ->maxLength(255)
                        ->rows(2)
                        ->helperText(__('filament/sa_static_pages.meta_description_helper')),
                ]),

            Section::make(__('sections.translations'))
                ->description(__('filament/sa_static_pages.translations_description'))
                ->icon('heroicon-o-language')
                ->collapsed()
                ->schema([
                    Tabs::make(__('filament/sa_static_pages.tabs_outer'))
                        ->tabs(collect(config('locales.supported', []))
                            ->reject(fn ($_, $code) => $code === 'en')
                            ->map(fn (array $meta, string $code) => Tab::make(($meta['flag'] ?? '') . ' ' . ($meta['name'] ?? strtoupper($code)))
                                ->schema([
                                    TextInput::make("translations.{$code}.title")
                                        ->label(__('filament/sa_static_pages.title'))
                                        ->maxLength(200),
                                    Textarea::make("translations.{$code}.excerpt")
                                        ->label(__('filament/sa_static_pages.excerpt'))
                                        ->rows(2)
                                        ->maxLength(500),
                                    Textarea::make("translations.{$code}.meta_description")
                                        ->label(__('filament/sa_static_pages.meta_description'))
                                        ->rows(2)
                                        ->maxLength(255),
                                    RichEditor::make("translations.{$code}.content_html")
                                        ->label(__('filament/sa_static_pages.content'))
                                        ->toolbarButtons([
                                            'bold', 'italic', 'underline', 'strike',
                                            'h2', 'h3', 'blockquote', 'codeBlock',
                                            'bulletList', 'orderedList',
                                            'link', 'undo', 'redo',
                                        ]),
                                ]))
                            ->values()
                            ->all()),
                ]),

            Section::make(__('sections.visibility'))
                ->schema([
                    Toggle::make('published')
                        ->label(__('filament/sa_static_pages.published'))
                        ->default(true)
                        ->helperText(__('filament/sa_static_pages.published_helper')),

                    Toggle::make('show_in_nav')
                        ->label(__('filament/sa_static_pages.show_in_nav'))
                        ->default(false)
                        ->helperText(__('filament/sa_static_pages.show_in_nav_helper')),

                    Toggle::make('show_in_footer')
                        ->label(__('filament/sa_static_pages.show_in_footer'))
                        ->default(true)
                        ->helperText(__('filament/sa_static_pages.show_in_footer_helper')),

                    TextInput::make('nav_order')
                        ->label(__('filament/sa_static_pages.nav_order'))
                        ->numeric()
                        ->default(100)
                        ->minValue(0)
                        ->maxValue(9999)
                        ->helperText(__('filament/sa_static_pages.nav_order_helper')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('filament/sa_static_pages.title'))->searchable()->sortable()->weight('medium'),
                TextColumn::make('slug')
                    ->label(__('filament/sa_static_pages.slug'))
                    ->prefix('/pages/')
                    ->copyable()
                    ->color('gray')
                    ->searchable(),
                IconColumn::make('published')
                    ->label(__('filament/sa_static_pages.column_live'))
                    ->boolean(),
                IconColumn::make('show_in_nav')
                    ->label(__('filament/sa_static_pages.column_nav'))
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('show_in_footer')
                    ->label(__('filament/sa_static_pages.column_footer'))
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('nav_order')
                    ->label(__('filament/sa_static_pages.column_order'))
                    ->sortable()
                    ->alignRight(),
                TextColumn::make('updated_at')
                    ->label(__('filament/sa_static_pages.updated_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('nav_order')
            ->actions([
                Action::make('view_live')
                    ->label(__('filament/sa_static_pages.view'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (StaticPage $record) => url('/pages/' . $record->slug))
                    ->openUrlInNewTab(),
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
            'index'  => Pages\ListStaticPages::route('/'),
            'create' => Pages\CreateStaticPage::route('/create'),
            'edit'   => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }
}
