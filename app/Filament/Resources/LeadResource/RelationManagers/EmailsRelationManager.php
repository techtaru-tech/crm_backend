<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmailsRelationManager extends RelationManager
{
    protected static string $relationship = 'emails';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-envelope';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/leads.emails_title');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject')->label(__('filament/leads.subject'))->disabled(),
            TextInput::make('from_address')->label(__('filament/leads.from'))->disabled(),
            Textarea::make('body_text')->label(__('filament/leads.body_text'))->rows(10)->disabled()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('direction')
                    ->label(__('filament/leads.direction'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament/leads.email_direction_' . $state))
                    ->color(fn ($state) => $state === 'inbound' ? 'gray' : 'indigo'),
                TextColumn::make('subject')
                    ->label(__('filament/leads.subject'))
                    ->searchable()
                    ->limit(50)
                    ->weight('medium'),
                TextColumn::make('from_address')
                    ->label(__('filament/leads.from'))
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->label(__('filament/leads.sent'))
                    ->since()
                    ->placeholder(fn ($record) => $record->created_at?->diffForHumans() ?? '—')
                    ->sortable(),
                IconColumn::make('opened_at')
                    ->label(__('filament/leads.opened'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->opened_at !== null)
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('clicked_at')
                    ->label(__('filament/leads.clicked'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->clicked_at !== null)
                    ->trueIcon('heroicon-o-cursor-arrow-rays')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),
                TextColumn::make('created_at')
                    ->label(__('filament/leads.received'))
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->label(__('filament/leads.direction'))
                    ->options([
                        'inbound'  => __('filament/leads.inbound'),
                        'outbound' => __('filament/leads.outbound'),
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn ($record) => $record->subject ?: __('filament/leads.email_modal_default'))
                    ->schema([
                        Section::make(__('sections.email_details'))
                            ->schema([
                                TextInput::make('subject')->label(__('filament/leads.subject'))->disabled(),
                                TextInput::make('from_address')->label(__('filament/leads.from'))->disabled(),
                                TextInput::make('direction')->label(__('filament/leads.direction'))->disabled(),
                                ViewField::make('body_html')
                                    ->label(__('filament/leads.body'))
                                    ->view('filament.components.raw-html')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
