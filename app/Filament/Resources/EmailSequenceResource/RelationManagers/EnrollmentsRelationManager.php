<?php

namespace App\Filament\Resources\EmailSequenceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-user-group';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/email_sequences.enrollments_relation_title');
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
                TextColumn::make('lead.full_name')
                    ->label(__('filament/email_sequences.col_lead'))
                    ->searchable(['leads.first_name', 'leads.last_name'])
                    ->weight('medium')
                    ->placeholder('—'),
                TextColumn::make('lead.email')
                    ->label(__('filament/email_sequences.col_email'))
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('filament/email_sequences.col_status'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'     => 'success',
                        'completed'  => 'info',
                        'replied'    => 'warning',
                        'unenrolled' => 'gray',
                        default      => 'gray',
                    }),
                TextColumn::make('current_step')
                    ->label(__('filament/email_sequences.col_step'))
                    ->numeric(),
                TextColumn::make('next_send_at')
                    ->label(__('filament/email_sequences.col_next_send'))
                    ->since()
                    ->placeholder('—'),
                TextColumn::make('enrolled_at')
                    ->label(__('filament/email_sequences.col_enrolled_at'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/email_sequences.filter_label_status'))
                    ->options([
                        'active'     => __('filament/email_sequences.option_enrollment_active'),
                        'completed'  => __('filament/email_sequences.option_enrollment_completed'),
                        'replied'    => __('filament/email_sequences.option_enrollment_replied'),
                        'unenrolled' => __('filament/email_sequences.option_enrollment_unenrolled'),
                    ]),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }
}
