<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiSdrAgentResource\RelationManagers;

use App\Models\AiSdrEnrollment;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Read-only list of a given agent's enrollments, with a per-row
 * "Decision log" modal that surfaces the AI decision audit trail
 * (ai_sdr_messages) for that enrollment.
 */
class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-user-group';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/ai_sdr.enrollments_relation_title');
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
                    ->label(__('filament/ai_sdr.col_lead'))
                    ->searchable(['leads.first_name', 'leads.last_name'])
                    ->weight('medium')
                    ->placeholder('—'),
                TextColumn::make('lead.email')
                    ->label(__('filament/ai_sdr.col_email'))
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('state')
                    ->label(__('filament/ai_sdr.col_state'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::stateLabel($state))
                    ->color(fn (?string $state): string => match ($state) {
                        AiSdrEnrollment::STATE_ACTIVE         => 'success',
                        AiSdrEnrollment::STATE_AWAITING_REPLY => 'info',
                        AiSdrEnrollment::STATE_PAUSED_HUMAN   => 'warning',
                        AiSdrEnrollment::STATE_QUALIFIED,
                        AiSdrEnrollment::STATE_HANDED_OFF     => 'primary',
                        AiSdrEnrollment::STATE_OPTED_OUT      => 'danger',
                        default                                => 'gray',
                    }),
                TextColumn::make('touches_sent')
                    ->label(__('filament/ai_sdr.col_touches'))
                    ->numeric(),
                TextColumn::make('intent_score')
                    ->label(__('filament/ai_sdr.col_intent'))
                    ->placeholder('—'),
                TextColumn::make('next_action_at')
                    ->label(__('filament/ai_sdr.col_next_action'))
                    ->since()
                    ->placeholder('—'),
                TextColumn::make('enrolled_at')
                    ->label(__('filament/ai_sdr.col_enrolled_at'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__('filament/ai_sdr.filter_state'))
                    ->options([
                        AiSdrEnrollment::STATE_ACTIVE         => self::stateLabel(AiSdrEnrollment::STATE_ACTIVE),
                        AiSdrEnrollment::STATE_AWAITING_REPLY => self::stateLabel(AiSdrEnrollment::STATE_AWAITING_REPLY),
                        AiSdrEnrollment::STATE_PAUSED_HUMAN   => self::stateLabel(AiSdrEnrollment::STATE_PAUSED_HUMAN),
                        AiSdrEnrollment::STATE_HANDED_OFF     => self::stateLabel(AiSdrEnrollment::STATE_HANDED_OFF),
                        AiSdrEnrollment::STATE_OPTED_OUT      => self::stateLabel(AiSdrEnrollment::STATE_OPTED_OUT),
                        AiSdrEnrollment::STATE_COMPLETED      => self::stateLabel(AiSdrEnrollment::STATE_COMPLETED),
                    ]),
            ])
            ->actions([
                Action::make('decision_log')
                    ->label(__('filament/ai_sdr.decision_log'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('gray')
                    ->modalHeading(fn (AiSdrEnrollment $record) => __('filament/ai_sdr.decision_log_heading', [
                        'lead' => $record->lead?->full_name ?? ('#' . $record->lead_id),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/ai_sdr.close'))
                    ->modalContent(function (AiSdrEnrollment $record) {
                        $messages = $record->messages()
                            ->orderByDesc('created_at')
                            ->limit(50)
                            ->get();
                        return view('filament.ai-sdr.decision-log', ['messages' => $messages]);
                    }),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }

    private static function stateLabel(?string $state): string
    {
        return match ($state) {
            AiSdrEnrollment::STATE_ACTIVE         => __('filament/ai_sdr.state_active'),
            AiSdrEnrollment::STATE_AWAITING_REPLY => __('filament/ai_sdr.state_awaiting_reply'),
            AiSdrEnrollment::STATE_PAUSED_HUMAN   => __('filament/ai_sdr.state_paused_human'),
            AiSdrEnrollment::STATE_QUALIFIED      => __('filament/ai_sdr.state_qualified'),
            AiSdrEnrollment::STATE_HANDED_OFF     => __('filament/ai_sdr.state_handed_off'),
            AiSdrEnrollment::STATE_OPTED_OUT      => __('filament/ai_sdr.state_opted_out'),
            AiSdrEnrollment::STATE_COMPLETED      => __('filament/ai_sdr.state_completed'),
            default                                => (string) $state,
        };
    }
}
