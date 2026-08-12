<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\EmailSequence;
use App\Models\EmailSequenceEnrollment;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SequenceEnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'sequenceEnrollments';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-envelope-open';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/leads.email_sequences_title');
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
        $tenantId = fn () => $this->ownerRecord->tenant_id
            ?? AppSupportTenantContext::currentId();

        return $table
            ->columns([
                TextColumn::make('sequence.name')
                    ->label(__('filament/leads.sequence'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('status')
                    ->label(__('filament/leads.sequence_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament/leads.enrollment_status_' . $state))
                    ->color(fn ($state) => match ($state) {
                        'active'     => 'success',
                        'completed'  => 'info',
                        'replied'    => 'warning',
                        'unenrolled' => 'gray',
                        default      => 'gray',
                    }),
                TextColumn::make('current_step')
                    ->label(__('filament/leads.step'))
                    ->numeric(),
                TextColumn::make('next_send_at')
                    ->label(__('filament/leads.next_send'))
                    ->since()
                    ->placeholder('—'),
                TextColumn::make('enrolled_at')
                    ->label(__('filament/leads.enrolled_at'))
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('enroll_in_sequence')
                    ->label(__('filament/leads.enroll_in_sequence'))
                    ->icon('heroicon-o-envelope-open')
                    ->color('primary')
                    ->form([
                        Select::make('sequence_id')
                            ->label(__('filament/leads.sequence'))
                            ->options(fn () => EmailSequence::where('tenant_id', $tenantId())
                                ->where('status', 'active')
                                ->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data) use ($tenantId) {
                        $sequenceId = (int) $data['sequence_id'];

                        // Tenant-scoped existence check.
                        $already = EmailSequenceEnrollment::query()
                            ->where('tenant_id', $tenantId())
                            ->where('sequence_id', $sequenceId)
                            ->where('lead_id', $this->ownerRecord->id)
                            ->exists();
                        if ($already) {
                            Notification::make()
                                ->warning()
                                ->title(__('filament/leads.already_enrolled'))
                                ->send();
                            return;
                        }

                        EmailSequenceEnrollment::create([
                            'tenant_id'    => $tenantId(),
                            'sequence_id'  => $sequenceId,
                            'lead_id'      => $this->ownerRecord->id,
                            'current_step' => 0,
                            'status'       => 'active',
                            'enrolled_at'  => now(),
                            'next_send_at' => now(),
                        ]);

                        Notification::make()->success()->title(__('filament/leads.lead_enrolled'))->send();
                    }),
            ])
            ->actions([
                Action::make('unenroll')
                    ->label(__('filament/leads.unenroll'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(function ($record) {
                        $record->update([
                            'status'          => 'unenrolled',
                            'unenroll_reason' => __('filament/leads.unenroll_reason_manual'),
                        ]);
                        Notification::make()->success()->title(__('filament/leads.lead_unenrolled'))->send();
                    }),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }
}
