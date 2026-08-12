<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\LeadCallResource\Pages;
use App\Models\LeadCall;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadCallResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model                           = LeadCall::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-phone';
    protected static string|\UnitEnum|null $navigationGroup   = 'Leads';
    protected static ?int    $navigationSort                  = 7;

    public static function getNavigationLabel(): string
    {
        return __('filament/lead_calls.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel /
     * $pluralModelLabel properties so the locale is resolved at request
     * time rather than at class-load time (when the translator is not
     * yet bound for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/lead_calls.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/lead_calls.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        // No create form — calls are persisted by the click-to-call flow.
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.call_details'))
                ->columns(2)
                ->schema([
                    Placeholder::make('lead')
                        ->label(__('filament/lead_calls.lead'))
                        ->content(fn (LeadCall $record) => $record->lead?->full_name ?? __('filament/lead_calls.fallback_unknown')),
                    Placeholder::make('user')
                        ->label(__('filament/lead_calls.agent'))
                        ->content(fn (LeadCall $record) => $record->user?->name ?? '--'),
                    Placeholder::make('direction')
                        ->label(__('filament/lead_calls.col_direction'))
                        ->content(function (LeadCall $record) {
                            $labels = [
                                'inbound'  => __('filament/lead_calls.direction_inbound'),
                                'outbound' => __('filament/lead_calls.direction_outbound'),
                            ];
                            return $labels[$record->direction] ?? ucfirst((string) $record->direction);
                        }),
                    Placeholder::make('status')
                        ->label(__('filament/lead_calls.col_status'))
                        ->content(function (LeadCall $record) {
                            $labels = [
                                'initiated'   => __('filament/lead_calls.status_initiated'),
                                'ringing'     => __('filament/lead_calls.status_ringing'),
                                'in-progress' => __('filament/lead_calls.status_in_progress'),
                                'completed'   => __('filament/lead_calls.status_completed'),
                                'busy'        => __('filament/lead_calls.status_busy'),
                                'failed'      => __('filament/lead_calls.status_failed'),
                                'no-answer'   => __('filament/lead_calls.status_no_answer'),
                                'canceled'    => __('filament/lead_calls.status_canceled'),
                            ];
                            return $labels[$record->status] ?? (string) $record->status;
                        }),
                    Placeholder::make('from_number')->label(__('filament/lead_calls.from')),
                    Placeholder::make('to_number')->label(__('filament/lead_calls.to')),
                    Placeholder::make('duration')
                        ->label(__('filament/lead_calls.duration'))
                        ->content(fn (LeadCall $record) => $record->formatted_duration),
                    Placeholder::make('started_at')
                        ->label(__('filament/lead_calls.started'))
                        ->content(fn (LeadCall $record) => $record->started_at?->format('Y-m-d H:i')),
                ]),

            Section::make(__('sections.recording_ai'))
                ->schema([
                    ViewField::make('recording_url')
                        ->label(__('filament/lead_calls.recording'))
                        ->view('filament.resources.lead-calls.recording-player')
                        ->visible(fn (LeadCall $record) => ! empty($record->recording_url)),
                    Placeholder::make('ai_summary')
                        ->label(__('filament/lead_calls.ai_summary'))
                        ->content(fn (LeadCall $record) => $record->ai_summary ?: __('filament/lead_calls.fallback_not_available')),
                    Placeholder::make('transcription')
                        ->label(__('filament/lead_calls.transcription'))
                        ->content(fn (LeadCall $record) => $record->transcription ?: __('filament/lead_calls.fallback_not_available')),
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lead.full_name')
                    ->label(__('filament/lead_calls.lead'))
                    ->searchable(['first_name', 'last_name'])
                    ->url(fn (LeadCall $record) => $record->lead_id
                        ? LeadResource::getUrl('view', ['record' => $record->lead_id])
                        : null),
                TextColumn::make('user.name')->label(__('filament/lead_calls.agent'))->toggleable(),
                BadgeColumn::make('direction')
                    ->label(__('filament/lead_calls.col_direction'))
                    ->colors([
                        'gray'    => 'inbound',
                        'primary' => 'outbound',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'inbound'  => __('filament/lead_calls.direction_inbound'),
                        'outbound' => __('filament/lead_calls.direction_outbound'),
                        default    => (string) $state,
                    }),
                TextColumn::make('from_number')->label(__('filament/lead_calls.from'))->toggleable(),
                TextColumn::make('to_number')->label(__('filament/lead_calls.to'))->toggleable(),
                TextColumn::make('formatted_duration')->label(__('filament/lead_calls.duration')),
                BadgeColumn::make('status')
                    ->label(__('filament/lead_calls.col_status'))
                    ->colors([
                        'gray'    => ['initiated', 'ringing', 'in-progress'],
                        'success' => 'completed',
                        'warning' => ['no-answer', 'busy'],
                        'danger'  => ['failed', 'canceled'],
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'initiated'   => __('filament/lead_calls.status_initiated'),
                        'ringing'     => __('filament/lead_calls.status_ringing'),
                        'in-progress' => __('filament/lead_calls.status_in_progress'),
                        'completed'   => __('filament/lead_calls.status_completed'),
                        'no-answer'   => __('filament/lead_calls.status_no_answer'),
                        'busy'        => __('filament/lead_calls.status_busy'),
                        'failed'      => __('filament/lead_calls.status_failed'),
                        'canceled'    => __('filament/lead_calls.status_canceled'),
                        default       => (string) $state,
                    }),
                TextColumn::make('created_at')->since()->label(__('filament/lead_calls.col_when'))->sortable(),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->label(__('filament/lead_calls.filter_label_direction'))
                    ->options([
                        'inbound'  => __('filament/lead_calls.option_inbound'),
                        'outbound' => __('filament/lead_calls.option_outbound'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('filament/lead_calls.filter_label_status'))
                    ->options([
                        'initiated'    => __('filament/lead_calls.option_initiated'),
                        'ringing'      => __('filament/lead_calls.option_ringing'),
                        'in-progress'  => __('filament/lead_calls.option_in_progress'),
                        'completed'    => __('filament/lead_calls.option_completed'),
                        'busy'         => __('filament/lead_calls.option_busy'),
                        'failed'       => __('filament/lead_calls.option_failed'),
                        'no-answer'    => __('filament/lead_calls.option_no_answer'),
                        'canceled'     => __('filament/lead_calls.option_canceled'),
                    ]),
                SelectFilter::make('user_id')
                    ->label(__('filament/lead_calls.filter_agent'))
                    ->options(function () {
                        $tenantId = \App\Support\TenantContext::currentId();
                        return User::query()
                            ->when($tenantId, fn (Builder $q) => $q->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeadCalls::route('/'),
            'view'  => Pages\ViewLeadCall::route('/{record}'),
        ];
    }
}
