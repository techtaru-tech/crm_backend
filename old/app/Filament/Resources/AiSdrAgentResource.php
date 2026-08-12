<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\AiSdrAgentResource\Pages;
use App\Filament\Resources\AiSdrAgentResource\RelationManagers;
use App\Models\AiSdrAgent;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Admin CRUD for AI SDR agents (autonomous follow-up personas).
 *
 * Mirrors EmailSequenceResource (tenant-scoped query, HasRolePermissions,
 * translatable labels). Enrollment management + the decision audit log are
 * surfaced through the two relation managers.
 */
class AiSdrAgentResource extends Resource
{
    use HasRolePermissions;

    protected static string $permissionPrefix = 'automations';
    protected static ?string $model = AiSdrAgent::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-cpu-chip';
    protected static string|UnitEnum|null    $navigationGroup = 'Leads';
    protected static ?int    $navigationSort  = 9;

    public static function getNavigationLabel(): string
    {
        return __('filament/ai_sdr.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/ai_sdr.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/ai_sdr.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament/ai_sdr.section_agent'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/ai_sdr.field_name'))
                        ->required()
                        ->maxLength(255),
                    Select::make('goal')
                        ->label(__('filament/ai_sdr.field_goal'))
                        ->options([
                            AiSdrAgent::GOAL_QUALIFY => __('filament/ai_sdr.goal_qualify'),
                            AiSdrAgent::GOAL_BOOK    => __('filament/ai_sdr.goal_book'),
                        ])
                        ->default(AiSdrAgent::GOAL_QUALIFY)
                        ->required()
                        ->helperText(__('filament/ai_sdr.field_goal_help')),
                    Toggle::make('active')
                        ->label(__('filament/ai_sdr.field_active'))
                        ->default(true),
                    Select::make('channel')
                        ->label(__('filament/ai_sdr.field_channel'))
                        ->options([
                            AiSdrAgent::CHANNEL_EMAIL    => __('filament/ai_sdr.channel_email'),
                            AiSdrAgent::CHANNEL_SMS      => __('filament/ai_sdr.channel_sms'),
                            AiSdrAgent::CHANNEL_WHATSAPP => __('filament/ai_sdr.channel_whatsapp'),
                        ])
                        ->default(AiSdrAgent::CHANNEL_EMAIL)
                        ->required()
                        ->helperText(__('filament/ai_sdr.field_channel_help')),
                ])->columns(2),

            Section::make(__('filament/ai_sdr.section_persona'))
                ->schema([
                    Textarea::make('persona')
                        ->label(__('filament/ai_sdr.field_persona'))
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText(__('filament/ai_sdr.field_persona_help')),
                    Textarea::make('offer_context')
                        ->label(__('filament/ai_sdr.field_offer_context'))
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText(__('filament/ai_sdr.field_offer_context_help')),
                ]),

            Section::make(__('filament/ai_sdr.section_guardrails'))
                ->schema([
                    TextInput::make('max_touches')
                        ->label(__('filament/ai_sdr.field_max_touches'))
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(20)
                        ->default(4)
                        ->required()
                        ->helperText(__('filament/ai_sdr.field_max_touches_help')),
                    TextInput::make('min_hours_between_touches')
                        ->label(__('filament/ai_sdr.field_min_hours'))
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(720)
                        ->default(24)
                        ->required()
                        ->helperText(__('filament/ai_sdr.field_min_hours_help')),
                    Toggle::make('respect_business_hours')
                        ->label(__('filament/ai_sdr.field_business_hours'))
                        ->default(true)
                        ->helperText(__('filament/ai_sdr.field_business_hours_help')),
                ])->columns(2),

            Section::make(__('filament/ai_sdr.section_handoff'))
                ->schema([
                    Select::make('assign_to_user_id')
                        ->label(__('filament/ai_sdr.field_assign_to'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\User::where('tenant_id', $tenantId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->nullable()
                        ->helperText(__('filament/ai_sdr.field_assign_to_help')),
                    Select::make('meeting_type_id')
                        ->label(__('filament/ai_sdr.field_meeting_type'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\MeetingType::where('tenant_id', $tenantId)
                                ->where('is_active', true)
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->nullable()
                        ->helperText(__('filament/ai_sdr.field_meeting_type_help')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/ai_sdr.col_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                IconColumn::make('active')
                    ->label(__('filament/ai_sdr.col_active'))
                    ->boolean(),
                TextColumn::make('max_touches')
                    ->label(__('filament/ai_sdr.col_max_touches')),
                TextColumn::make('active_enrollments_count')
                    ->label(__('filament/ai_sdr.col_active_enrollments'))
                    ->counts([
                        'enrollments as active_enrollments_count' => fn ($q) => $q->where('state', 'active'),
                    ]),
                TextColumn::make('handed_off_count')
                    ->label(__('filament/ai_sdr.col_handed_off'))
                    ->counts([
                        'enrollments as handed_off_count' => fn ($q) => $q->where('state', 'handed_off'),
                    ]),
                TextColumn::make('assignee.name')
                    ->label(__('filament/ai_sdr.col_handoff_rep'))
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('filament/ai_sdr.col_created'))
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAiSdrAgents::route('/'),
            'create' => Pages\CreateAiSdrAgent::route('/create'),
            'edit'   => Pages\EditAiSdrAgent::route('/{record}/edit'),
        ];
    }
}
