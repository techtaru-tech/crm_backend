<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-chat-bubble-left-right';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/leads.messages_title');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('channel')->label(__('filament/leads.channel'))->disabled(),
            TextInput::make('direction')->label(__('filament/leads.direction'))->disabled(),
            TextInput::make('status')->label(__('filament/leads.message_status'))->disabled(),
            Textarea::make('body')->label(__('filament/leads.body'))->rows(6)->disabled()->columnSpanFull(),
            TextInput::make('media_url')->label(__('filament/leads.media_url'))->disabled()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('channel')
                    ->label(__('filament/leads.channel'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament/leads.channel_' . $state))
                    ->color(fn ($state) => match ($state) {
                        'whatsapp' => 'success',
                        'sms'      => 'info',
                        'telegram' => 'primary',
                        'viber'    => 'purple',
                        default    => 'gray',
                    }),
                TextColumn::make('direction')
                    ->label(__('filament/leads.direction'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament/leads.direction_' . $state))
                    ->color(fn ($state) => $state === 'inbound' ? 'gray' : 'indigo'),
                TextColumn::make('body')
                    ->label(__('filament/leads.body'))
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('status')
                    ->label(__('filament/leads.message_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament/leads.message_status_' . $state))
                    ->color(fn ($state) => match ($state) {
                        'sent'      => 'gray',
                        'delivered' => 'info',
                        'read'      => 'success',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('filament/leads.created_at'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->label(__('filament/leads.channel'))
                    ->options([
                        'whatsapp' => __('filament/leads.channel_whatsapp'),
                        'sms'      => __('filament/leads.channel_sms'),
                        'telegram' => __('filament/leads.channel_telegram'),
                        'viber'    => __('filament/leads.channel_viber'),
                    ]),
                SelectFilter::make('direction')
                    ->label(__('filament/leads.direction'))
                    ->options([
                        'inbound'  => __('filament/leads.inbound'),
                        'outbound' => __('filament/leads.outbound'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('filament/leads.filter_label_status'))
                    ->options([
                        'sent'      => __('filament/leads.status_sent'),
                        'delivered' => __('filament/leads.status_delivered'),
                        'read'      => __('filament/leads.status_read'),
                        'failed'    => __('filament/leads.status_failed'),
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(__('filament/leads.message_modal'))
                    ->schema([
                        Section::make(__('sections.message'))
                            ->schema([
                                TextInput::make('channel')->label(__('filament/leads.channel'))->disabled(),
                                TextInput::make('direction')->label(__('filament/leads.direction'))->disabled(),
                                TextInput::make('status')->label(__('filament/leads.message_status'))->disabled(),
                                Textarea::make('body')->label(__('filament/leads.body'))->rows(6)->disabled()->columnSpanFull(),
                                TextInput::make('media_url')
                                    ->label(__('filament/leads.media_url'))
                                    ->disabled()
                                    ->visible(fn ($record) => ! empty($record?->media_url))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
