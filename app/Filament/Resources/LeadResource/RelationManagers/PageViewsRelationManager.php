<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageViewsRelationManager extends RelationManager
{
    protected static string $relationship = 'pageViews';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-chart-bar-square';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/leads.web_activity_title');
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
        return $table
            ->columns([
                TextColumn::make('path')
                    ->label(__('filament/leads.page_path'))
                    ->searchable()
                    ->limit(50)
                    ->weight('medium'),
                TextColumn::make('title')
                    ->label(__('filament/leads.page_title'))
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('utm_source')
                    ->label(__('filament/leads.page_utm_source'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('viewed_at')
                    ->label(__('filament/leads.viewed'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('viewed_at', 'desc');
    }
}
