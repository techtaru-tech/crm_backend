<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\AutomationResource\Pages;
use App\Models\Automation;
use App\Models\AutomationStep;
use App\Models\Form;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AutomationResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'automations';
    protected static ?string $model = Automation::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-bolt';
    protected static string|UnitEnum|null    $navigationGroup = 'Leads';
    protected static ?int    $navigationSort  = 6;

    public static function getNavigationLabel(): string
    {
        return __('filament/automations.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/automations.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/automations.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        // Safe-wrapped so a missing/locked table never 500s the panel.
        return \App\Support\NavBadge::safe(function (): ?string {
            $tenantId = auth()->user()?->tenant_id;
            if (! $tenantId) return null;

            // 60 s cache per tenant. This badge runs the priciest query of
            // the three sidebar badges (whereHas subquery joining
            // automation_runs to automations + a date filter), and fires
            // on every Filament page navigation. Caching cuts dozens of
            // these per minute down to one.
            $count = \App\Support\TenantCache::remember(
                $tenantId,
                "nav-badge:automations-failed:tenant:{$tenantId}:v1",
                60,
                fn () => \App\Models\AutomationRun::where('status', \App\Models\AutomationRun::STATUS_FAILED)
                    ->whereHas('automation', fn ($q) => $q->where('tenant_id', $tenantId))
                    ->where('finished_at', '>=', now()->subDay())
                    ->count(),
            );

            return $count > 0 ? (string) $count : null;
        });
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('filament/automations.nav_badge_tooltip');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $schema->components([
            Section::make(__('sections.basic_details'))->schema([
                TextInput::make('name')
                    ->label(__('filament/automations.automation_name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('filament/automations.description'))
                    ->rows(2)
                    ->nullable(),
                Toggle::make('enabled')
                    ->label(__('filament/automations.active'))
                    ->default(true),
                Toggle::make('respect_business_hours')
                    ->label(__('filament/automations.respect_business_hours'))
                    ->helperText(__('filament/automations.respect_business_hours_help'))
                    ->default(false),
            ])->columns(2),

            Section::make(__('sections.trigger'))
                ->description(__('filament/automations.trigger_description'))
                ->schema([
                    Select::make('trigger_type')
                        ->label(__('filament/automations.trigger_event'))
                        ->options(Automation::triggerLabels())
                        ->required()
                        ->live(),

                    Section::make(__('sections.trigger_configuration'))
                        ->schema(fn(callable $get) => static::buildTriggerConfig($get('trigger_type'), $tenantId))
                        ->visible(fn(callable $get) => (bool) $get('trigger_type'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('sections.steps'))
                ->description(__('filament/automations.steps_description'))
                ->columnSpanFull()
                ->schema([
                    Repeater::make('steps')
                        ->relationship('steps')
                        ->label('')
                        ->reorderable('sort_order')
                        ->addActionLabel(__('filament/automations.add_step'))
                        ->schema([
                            Select::make('type')
                                ->label(__('filament/automations.step_type'))
                                ->options(AutomationStep::stepTypeLabels())
                                ->required()
                                ->live(),

                            Section::make(__('sections.condition_config'))
                                ->schema(fn(callable $get) => static::buildConditionConfig($get('config.type'), $tenantId))
                                ->visible(fn(callable $get) => $get('type') === 'condition')
                                ->columnSpanFull(),

                            Section::make(__('sections.action_config'))
                                ->schema(fn(callable $get) => static::buildActionConfig($get('config.action_type'), $tenantId))
                                ->visible(fn(callable $get) => $get('type') === 'action')
                                ->columnSpanFull(),

                            Section::make(__('sections.delay_config'))
                                ->schema(static::buildDelayConfig())
                                ->visible(fn(callable $get) => $get('type') === 'delay')
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->defaultItems(0)
                        ->itemLabel(fn(array $state): ?string => match ($state['type'] ?? null) {
                            'condition' => __('filament/automations.item_label_condition') . ($state['config']['type'] ?? ''),
                            'action'    => __('filament/automations.item_label_action')    . ($state['config']['action_type'] ?? ''),
                            'delay'     => __('filament/automations.item_label_delay_wait')       . ($state['config']['amount'] ?? '1') . ' ' . ($state['config']['unit'] ?? __('filament/automations.item_label_delay_default_unit')),
                            default     => null,
                        }),
                ]),
        ]);
    }

    private static function buildTriggerConfig(?string $triggerType, callable $tenantId): array
    {
        return match ($triggerType) {
            'lead_created' => [
                Select::make('trigger_config.sources')
                    ->label(__('filament/automations.filter_by_sources'))
                    ->multiple()
                    ->options(\App\Enums\LeadSource::class)
                    ->helperText(__('filament/automations.filter_by_sources_help'))
                    ->nullable(),
            ],
            'lead_stage_changed' => [
                Select::make('trigger_config.from_stage_id')
                    ->label(__('filament/automations.from_stage'))
                    ->options(fn() => PipelineStage::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->nullable(),
                Select::make('trigger_config.to_stage_id')
                    ->label(__('filament/automations.to_stage'))
                    ->options(fn() => PipelineStage::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->nullable(),
            ],
            'tag_added' => [
                TextInput::make('trigger_config.tag')
                    ->label(__('filament/automations.tag_name'))
                    ->required(),
            ],
            'lead_score_threshold' => [
                TextInput::make('trigger_config.threshold')
                    ->label(__('filament/automations.score_threshold'))
                    ->numeric()
                    ->required(),
                Select::make('trigger_config.direction')
                    ->label(__('filament/automations.crosses'))
                    ->options([
                        'above' => __('filament/automations.option_above_threshold'),
                        'below' => __('filament/automations.option_below_threshold'),
                    ])
                    ->default('above'),
            ],
            'no_activity' => [
                TextInput::make('trigger_config.value')
                    ->label(__('filament/automations.no_activity_for'))
                    ->numeric()
                    ->default(24),
                Select::make('trigger_config.unit')
                    ->label(__('filament/automations.unit'))
                    ->options([
                        'hours' => __('filament/automations.option_hours'),
                        'days'  => __('filament/automations.option_days'),
                    ])
                    ->default('hours'),
            ],
            'form_submitted' => [
                Select::make('trigger_config.form_id')
                    ->label(__('filament/automations.form_blank_for_any'))
                    ->options(fn() => Form::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->nullable(),
            ],
            default => [],
        };
    }

    private static function buildConditionConfig(?string $condType, callable $tenantId): array
    {
        $select = [
            Select::make('config.type')
                ->label(__('filament/automations.condition_type'))
                ->options(AutomationStep::conditionTypeLabels())
                ->required()
                ->live(),
        ];

        $extras = match ($condType) {
            'source_is', 'source_is_not' => [
                Select::make('config.value')
                    ->label(__('filament/automations.source'))
                    ->options(\App\Enums\LeadSource::class)
                    ->required(),
            ],
            'has_tag', 'not_has_tag' => [
                TextInput::make('config.value')->label(__('filament/automations.tag_name'))->required(),
            ],
            'field_equals', 'field_contains', 'field_is_empty' => [
                TextInput::make('config.field')->label(__('filament/automations.field_name'))->placeholder(__('filament/automations.field_name_placeholder'))->required(),
                TextInput::make('config.value')->label(__('filament/automations.value'))->visible(fn($get) => $condType !== 'field_is_empty'),
            ],
            'score_gt', 'score_lt' => [
                TextInput::make('config.value')->label(__('filament/automations.score'))->numeric()->required(),
            ],
            'assigned_to' => [
                Select::make('config.value')
                    ->label(__('filament/automations.user'))
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->required(),
            ],
            'time_of_day' => [
                TextInput::make('config.value')->label(__('filament/automations.time_range'))->placeholder(__('filament/automations.time_range_placeholder'))->required(),
            ],
            'day_of_week' => [
                TextInput::make('config.value')->label(__('filament/automations.days'))->placeholder(__('filament/automations.days_placeholder'))->required(),
            ],
            default => [],
        };

        return array_merge($select, $extras);
    }

    private static function buildActionConfig(?string $actionType, callable $tenantId): array
    {
        $select = [
            Select::make('config.action_type')
                ->label(__('filament/automations.action'))
                ->options(AutomationStep::actionTypeLabels())
                ->required()
                ->live(),
        ];

        $extras = match ($actionType) {
            'send_email' => [
                Select::make('config.email_template_id')
                    ->label(__('filament/automations.email_template'))
                    ->options(fn() => \App\Models\EmailTemplate::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->required(),
            ],
            'notify_users' => [
                Select::make('config.user_ids')
                    ->label(__('filament/automations.notify_users'))
                    ->multiple()
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id')),
                Toggle::make('config.notify_assigned')->label(__('filament/automations.notify_assigned_agent')),
                TextInput::make('config.message')->label(__('filament/automations.custom_message'))->nullable(),
            ],
            'assign_lead' => [
                Select::make('config.mode')
                    ->label(__('filament/automations.assignment_mode'))
                    ->options([
                        'specific'    => __('filament/automations.option_specific_user'),
                        'round_robin' => __('filament/automations.option_round_robin'),
                    ])
                    ->default('specific')
                    ->live(),
                Select::make('config.user_id')
                    ->label(__('filament/automations.user'))
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->visible(fn($get) => $get('config.mode') === 'specific'),
                Select::make('config.user_ids')
                    ->label(__('filament/automations.users_round_robin_pool'))
                    ->multiple()
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->visible(fn($get) => $get('config.mode') === 'round_robin'),
            ],
            'add_tag', 'remove_tag' => [
                TextInput::make('config.tag')->label(__('filament/automations.tag_name'))->required(),
            ],
            'move_pipeline' => [
                Select::make('config.pipeline_stage_id')
                    ->label(__('filament/automations.target_stage'))
                    ->options(fn() => PipelineStage::where('tenant_id', $tenantId())
                        ->with('pipeline')
                        ->get()
                        ->mapWithKeys(fn($s) => [$s->id => ($s->pipeline?->name ?? '') . ' → ' . $s->name]))
                    ->required(),
            ],
            'change_status' => [
                Select::make('config.status')
                    ->label(__('filament/automations.new_status'))
                    ->options([
                        'new'        => __('filament/automations.option_lead_status_new'),
                        'contacted'  => __('filament/automations.option_lead_status_contacted'),
                        'qualified'  => __('filament/automations.option_lead_status_qualified'),
                        'lost'       => __('filament/automations.option_lead_status_lost'),
                        'won'        => __('filament/automations.option_lead_status_won'),
                    ])
                    ->required(),
            ],
            'send_webhook' => [
                TextInput::make('config.url')->label(__('filament/automations.webhook_url'))->url()->required(),
                TextInput::make('config.secret')->label(__('filament/automations.hmac_secret'))->nullable(),
            ],
            'create_task' => [
                TextInput::make('config.title')
                    ->label(__('filament/automations.task_title'))
                    ->helperText(__('filament/automations.task_title_help'))
                    ->required(),
                Textarea::make('config.description')->label(__('filament/automations.description'))->nullable()->rows(2),
                TextInput::make('config.hours_from_now')
                    ->label(__('filament/automations.due_in_hours'))
                    ->numeric()
                    ->default(24)
                    ->minValue(0)
                    ->helperText(__('filament/automations.due_in_hours_help'))
                    ->nullable(),
                Select::make('config.priority')
                    ->label(__('filament/automations.priority'))
                    ->options(\App\Models\LeadTask::priorityLabels())
                    ->default('normal'),
                Select::make('config.assigned_user_id')
                    ->label(__('filament/automations.assign_task_to'))
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->helperText(__('filament/automations.assign_task_to_help'))
                    ->nullable(),
            ],
            'send_slack' => [
                TextInput::make('config.webhook_url')->label(__('filament/automations.slack_webhook_url'))->url()->required(),
                Textarea::make('config.message')
                    ->label(__('filament/automations.slack_message'))
                    ->rows(2)
                    ->nullable(),
            ],
            'send_sms' => [
                Textarea::make('config.message')
                    ->label(__('filament/automations.sms_message'))
                    ->helperText(__('filament/automations.sms_message_help'))
                    ->required()
                    ->rows(3)
                    ->maxLength(160),
            ],
            default => [],
        };

        return array_merge($select, $extras);
    }

    private static function buildDelayConfig(): array
    {
        return [
            TextInput::make('config.amount')
                ->label(__('filament/automations.wait'))
                ->numeric()
                ->default(1)
                ->required(),
            Select::make('config.unit')
                ->label(__('filament/automations.unit'))
                ->options([
                    'minutes' => __('filament/automations.option_minutes'),
                    'hours'   => __('filament/automations.option_hours'),
                    'days'    => __('filament/automations.option_days'),
                ])
                ->default('hours')
                ->required(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/automations.name'))->searchable()->sortable(),
                TextColumn::make('trigger_type')
                    ->label(__('filament/automations.trigger'))
                    ->formatStateUsing(fn($state) => Automation::triggerLabels()[$state] ?? $state),
                TextColumn::make('steps_count')
                    ->label(__('filament/automations.steps'))
                    ->counts('steps'),
                TextColumn::make('runs_count')
                    ->label(__('filament/automations.runs'))
                    ->counts('runs'),
                IconColumn::make('enabled')->label(__('filament/automations.active'))->boolean(),
                TextColumn::make('created_at')->label(__('filament/automations.created'))->date()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('enabled')
                    ->label(__('filament/automations.active')),
                SelectFilter::make('trigger_type')
                    ->label(__('filament/automations.trigger'))
                    ->options(Automation::triggerLabels()),
            ])
            ->actions([
                EditAction::make(),
                Action::make('run_history')
                    ->label(__('filament/automations.history'))
                    ->icon('heroicon-o-clock')
                    ->url(fn(Automation $record) => static::getUrl('run-history', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('enable')
                        ->label(__('filament/automations.enable_selected'))
                        ->icon('heroicon-o-check-circle')
                        ->action(fn(Collection $records) => $records->each->update(['enabled' => true]))
                        ->requiresConfirmation()
                        ->color('success'),
                    BulkAction::make('disable')
                        ->label(__('filament/automations.disable_selected'))
                        ->icon('heroicon-o-x-circle')
                        ->action(fn(Collection $records) => $records->each->update(['enabled' => false]))
                        ->requiresConfirmation()
                        ->color('warning'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'       => Pages\ListAutomations::route('/'),
            'create'      => Pages\CreateAutomation::route('/create'),
            'edit'        => Pages\EditAutomation::route('/{record}/edit'),
            'flow'        => Pages\AutomationFlowBuilder::route('/{record}/flow'),
            'run-history' => Pages\AutomationRunHistory::route('/{record}/run-history'),
        ];
    }
}
