<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Enums\LeadSource;
use App\Filament\Resources\Concerns\HasCustomFields;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Jobs\ExportLeads;
use App\Jobs\RunAutomation;
use App\Models\Automation;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tag;
use App\Models\User;
use App\Support\Currency;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LeadResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = Lead::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament/leads.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/leads.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/leads.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        // Safe-wrapped so a missing/locked table never 500s the panel.
        return \App\Support\NavBadge::safe(function (): ?string {
            $tenantId = auth()->user()?->tenant_id;
            if (! $tenantId) return null;

            // 60 s cache per tenant — sidebar badge fires on every page
            // navigation; uncached this is one of the hottest queries in
            // the panel. 60 s freshness is plenty for a "new in last 24 h"
            // counter that admins glance at, not poll.
            $count = \App\Support\TenantCache::remember(
                $tenantId,
                "nav-badge:leads-new:tenant:{$tenantId}:v1",
                60,
                fn () => static::getEloquentQuery()
                    ->where('status', 'new')
                    ->whereDate('created_at', '>=', now()->subDay())
                    ->count(),
            );

            return $count > 0 ? (string) $count : null;
        });
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->full_name ?: ($record->email ?? __('filament/leads.search_result_fallback', ['id' => $record->id]));
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            __('filament/leads.search_result_email')  => $record->email,
            __('filament/leads.search_result_source') => $record->source_label,
            __('filament/leads.search_result_status') => $record->status?->label() ?? '',
            __('filament/leads.search_result_score')  => $record->lead_score > 0
                ? __('filament/leads.search_result_score_value', ['score' => $record->lead_score])
                : null,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $schema->components([
            Section::make(__('sections.contact_info'))->schema([
                TextInput::make('first_name')->label(__('filament/leads.first_name'))->maxLength(100),
                TextInput::make('last_name')->label(__('filament/leads.last_name'))->maxLength(100),
                TextInput::make('email')
                    ->label(__('filament/leads.email'))
                    ->email()
                    ->unique(
                        table: 'leads',
                        column: 'email',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule) use ($tenantId) {
                            if ($tid = $tenantId()) {
                                $rule->where('tenant_id', $tid);
                            }
                            return $rule;
                        }
                    ),
                TextInput::make('phone')->label(__('filament/leads.phone'))->maxLength(30)->tel(),
                Select::make('company_id')
                    ->label(__('filament/leads.company'))
                    ->options(fn() => Company::where('tenant_id', $tenantId())->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->createOptionForm([
                        TextInput::make('name')->label(__('filament/leads.company_name'))->required()->maxLength(150),
                        TextInput::make('domain')->label(__('filament/leads.domain'))->maxLength(150),
                        TextInput::make('industry')->label(__('filament/leads.industry'))->maxLength(100),
                    ])
                    ->createOptionUsing(function (array $data) use ($tenantId) {
                        $company = Company::create([
                            'tenant_id' => $tenantId(),
                            'name'      => $data['name'],
                            'domain'    => $data['domain'] ?? null,
                            'industry'  => $data['industry'] ?? null,
                        ]);
                        return $company->id;
                    }),
            ])->columns(2),

            Section::make(__('sections.lead_details'))->schema([
                Select::make('source')
                    ->label(__('filament/leads.source'))
                    ->options(fn () => static::leadSourceOptions($tenantId()))
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('label')
                            ->label(__('filament/leads.custom_source_label'))
                            ->required()
                            ->maxLength(50),
                    ])
                    ->createOptionUsing(fn (array $data) => \Illuminate\Support\Str::slug((string) $data['label'], '_'))
                    ->required(),
                Select::make('status')
                    ->label(__('filament/leads.status'))
                    ->options(\App\Enums\LeadStatus::options())
                    ->default(\App\Enums\LeadStatus::New->value)
                    ->reactive()
                    ->required(),
                Select::make('assigned_user_id')
                    ->label(__('filament/leads.assigned_to'))
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Select::make('pipeline_id')
                    ->label(__('filament/leads.pipeline'))
                    ->options(fn() => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->reactive()
                    ->nullable(),
                Select::make('pipeline_stage_id')
                    ->label(__('filament/leads.stage'))
                    ->options(fn($get) => $get('pipeline_id')
                        ? PipelineStage::where('pipeline_id', $get('pipeline_id'))->pluck('name', 'id')
                        : []
                    )
                    ->nullable(),
                Select::make('tags')
                    ->label(__('filament/leads.tags'))
                    ->relationship('tags', 'name', fn($query) => $query->where('tags.tenant_id', $tenantId()))
                    ->multiple()
                    ->preload(),
                TextInput::make('lead_score')->numeric()->default(0)->label(__('filament/leads.score')),
                Toggle::make('is_starred')->label(__('filament/leads.starred')),
            ])->columns(2),

            Section::make(__('sections.deal'))->schema([
                TextInput::make('deal_value')
                    ->label(__('filament/leads.deal_value'))
                    ->numeric()
                    ->prefix(fn () => Currency::defaultSymbol())
                    ->minValue(0)
                    ->nullable(),
                Select::make('deal_currency')
                    ->label(__('filament/leads.currency'))
                    ->options(Currency::options())
                    ->searchable()
                    ->default(fn () => Currency::forTenant(auth()->user()?->tenant))
                    ->nullable(),
                DatePicker::make('expected_close_date')
                    ->label(__('filament/leads.expected_close_date'))
                    ->nullable(),
                Textarea::make('lost_reason')
                    ->label(__('filament/leads.lost_reason'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('status') === 'lost')
                    ->nullable(),
            ])->columns(3),

            Section::make(__('sections.additional_info'))->schema([
                TextInput::make('source_id')->label(__('filament/leads.source_reference_id')),
                Textarea::make('notes')->label(__('filament/leads.lead_notes'))->rows(3)->columnSpanFull(),
                DateTimePicker::make('contacted_at')->label(__('filament/leads.last_contacted')),
            ])->columns(2),

            Section::make(__('sections.attribution'))
                ->description(__('filament/leads.attribution_description'))
                ->collapsed()
                ->schema([
                    TextInput::make('utm_source')->label(__('filament/leads.utm_source'))->disabled()->dehydrated(false),
                    TextInput::make('utm_medium')->label(__('filament/leads.utm_medium'))->disabled()->dehydrated(false),
                    TextInput::make('utm_campaign')->label(__('filament/leads.utm_campaign'))->disabled()->dehydrated(false),
                    TextInput::make('utm_content')->label(__('filament/leads.utm_content'))->disabled()->dehydrated(false),
                    TextInput::make('utm_term')->label(__('filament/leads.utm_term'))->disabled()->dehydrated(false),
                    Textarea::make('landing_page')->label(__('filament/leads.landing_page'))->rows(2)->disabled()->dehydrated(false)->columnSpanFull(),
                    Textarea::make('referrer_url')->label(__('filament/leads.referrer'))->rows(2)->disabled()->dehydrated(false)->columnSpanFull(),
                ])
                ->columns(2),

            ...(count($customFields = HasCustomFields::getCustomFieldFormComponents('lead')) > 0
                ? [Section::make(__('sections.custom_fields'))
                    ->description(__('filament/leads.custom_fields_description'))
                    ->schema($customFields)
                    ->columns(2)]
                : []),
        ]);
    }

    /**
     * Built-in lead sources plus any custom source values this tenant has
     * already used, so an operator can re-pick "walk-ins" / "exhibition" etc.
     * after adding them via the Select's "+ Create" option. The dashboard
     * "Leads by Source" report groups by the raw source value, so a custom
     * source flows straight into reporting.
     *
     * @return array<string, string>
     */
    public static function leadSourceOptions(?int $tenantId): array
    {
        $options = LeadSource::options();

        if ($tenantId) {
            // Explicit tenant filter, so bypass the ambient BelongsToTenant
            // global scope (which would fail-closed outside a bound tenant
            // context) — same pattern as InvitationService.
            $custom = Lead::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->whereNotNull('source')
                ->distinct()
                ->pluck('source')
                ->all();

            foreach ($custom as $src) {
                $src = (string) $src;
                if ($src !== '' && ! array_key_exists($src, $options)) {
                    $options[$src] = \Illuminate\Support\Str::headline(str_replace('_', ' ', $src));
                }
            }
        }

        return $options;
    }

    public static function table(Table $table): Table
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('filament/leads.name'))
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->email ?? ''),
                TextColumn::make('phone')
                    ->label(__('filament/leads.phone'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('companyEntity.name')
                    ->label(__('filament/leads.company'))
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source')
                    ->label(__('filament/leads.source'))
                    ->formatStateUsing(fn($state) => LeadSource::tryFrom($state)?->label() ?? ucfirst($state))
                    ->badge()
                    ->color(fn($state) => match(LeadSource::tryFrom($state)) {
                        LeadSource::Meta, LeadSource::Instagram                          => 'primary',
                        LeadSource::WhatsApp, LeadSource::Telegram, LeadSource::Viber   => 'success',
                        LeadSource::LinkedIn                                             => 'info',
                        LeadSource::TikTok, LeadSource::Snapchat, LeadSource::Pinterest => 'danger',
                        LeadSource::GoogleAds, LeadSource::YouTube, LeadSource::Microsoft,
                        LeadSource::Twitter                                              => 'warning',
                        LeadSource::Typeform, LeadSource::JotForm, LeadSource::Calendly,
                        LeadSource::WebForm                                              => 'gray',
                        LeadSource::Email                                                => 'gray',
                        default                                                          => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/leads.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\LeadStatus
                        ? $state->label()
                        : __('filament/leads.status_' . $state))
                    ->color(fn($state) => match($state instanceof \App\Enums\LeadStatus ? $state->value : $state) {
                        'new'       => 'warning',
                        'contacted' => 'info',
                        'qualified' => 'success',
                        'won'       => 'success',
                        'converted' => 'success',
                        'lost'      => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('pipelineStage.name')
                    ->label(__('filament/leads.stage'))
                    ->badge()
                    ->color('purple')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('deal_value')
                    ->label(__('filament/leads.deal_value'))
                    ->money(fn($record) => $record->deal_currency ?: 'USD')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('expected_close_date')
                    ->label(__('filament/leads.expected_close'))
                    ->date()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lead_score')
                    ->label(__('filament/leads.score'))
                    ->badge()
                    ->color(fn($state) => $state > 50 ? 'success' : ($state > 20 ? 'warning' : 'gray'))
                    ->sortable(),
                TextColumn::make('tags.name')
                    ->label(__('filament/leads.tags'))
                    ->badge()
                    ->separator(',')
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('assignedUser.name')
                    ->label(__('filament/leads.assigned'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_starred')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                IconColumn::make('is_duplicate')
                    ->label(__('filament/leads.dup'))
                    ->boolean()
                    ->trueIcon('heroicon-o-document-duplicate')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament/leads.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
                TextColumn::make('waiting_on')
                    ->label(__('filament/leads.waiting_on'))
                    ->badge()
                    ->state(fn ($record) => $record->waiting_on)
                    ->icon(fn ($state) => match ($state) {
                        'us'    => 'heroicon-o-exclamation-triangle',
                        'them'  => 'heroicon-o-clock',
                        'new'   => 'heroicon-o-sparkles',
                        default => 'heroicon-o-minus',
                    })
                    ->color(fn ($state) => match ($state) {
                        'us'    => 'warning',
                        'them'  => 'info',
                        'new'   => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'us'    => __('filament/leads.conversation_last_by_us'),
                        'them'  => __('filament/leads.conversation_last_by_them'),
                        'new'   => __('filament/leads.conversation_last_by_new'),
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                ...HasCustomFields::getCustomFieldTableColumns('lead'),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label(__('filament/leads.filter_label_source'))
                    ->options(LeadSource::options()),
                SelectFilter::make('status')
                    ->label(__('filament/leads.filter_label_status'))
                    ->options(\App\Enums\LeadStatus::options()),
                SelectFilter::make('pipeline_id')
                    ->label(__('filament/leads.pipeline'))
                    ->relationship('pipeline', 'name'),
                SelectFilter::make('pipeline_stage_id')
                    ->label(__('filament/leads.stage'))
                    ->relationship('pipelineStage', 'name'),
                SelectFilter::make('assigned_user_id')
                    ->label(__('filament/leads.assigned_to'))
                    ->relationship('assignedUser', 'name'),
                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->label(__('filament/leads.tag')),
                TernaryFilter::make('is_starred')
                    ->label(__('filament/leads.starred'))
                    ->trueLabel(__('filament/leads.starred_only'))
                    ->falseLabel(__('filament/leads.not_starred'))
                    ->queries(
                        true:  fn($q) => $q->where('is_starred', true),
                        false: fn($q) => $q->where('is_starred', false),
                        blank: fn($q) => $q,
                    ),
                Filter::make('is_duplicate')
                    ->label(__('filament/leads.duplicates_only'))
                    ->query(fn($query) => $query->where('is_duplicate', true))
                    ->toggle(),
                SelectFilter::make('waiting_on')
                    ->label(__('filament/leads.waiting_on'))
                    ->options([
                        'us'   => __('filament/leads.waiting_us'),
                        'them' => __('filament/leads.waiting_them'),
                        'new'  => __('filament/leads.waiting_new'),
                    ])
                    ->query(function ($query, $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) {
                            return $query;
                        }

                        $inbound  = ['email_received', 'message_received', 'call_inbound'];
                        $outbound = ['email_sent', 'message_sent', 'call_outbound', 'note_added'];

                        return match ($value) {
                            'new' => $query
                                ->whereDoesntHave('activities', fn ($q) => $q->whereIn('type', array_merge($inbound, $outbound))),
                            'them' => $query
                                ->whereHas('activities', fn ($q) => $q->whereIn('type', $outbound))
                                ->whereDoesntHave('activities', fn ($q) => $q->whereIn('type', $inbound)),
                            // For 'us' we need activity comparison; approximate
                            // with "has inbound and (no outbound OR inbound is newer)"
                            'us' => $query->whereHas('activities', fn ($q) => $q->whereIn('type', $inbound)),
                            default => $query,
                        };
                    }),
                Filter::make('created_from')
                    ->form([\Filament\Forms\Components\DatePicker::make('date_from')->label(__('filament/leads.created_from'))])
                    ->query(fn($query, $data) => $query->when($data['date_from'], fn($q, $v) => $q->whereDate('created_at', '>=', $v))),
                Filter::make('created_until')
                    ->form([\Filament\Forms\Components\DatePicker::make('date_until')->label(__('filament/leads.created_until'))])
                    ->query(fn($query, $data) => $query->when($data['date_until'], fn($q, $v) => $q->whereDate('created_at', '<=', $v))),
                Filter::make('score_min')
                    ->form([TextInput::make('score_min')->numeric()->label(__('filament/leads.min_score'))])
                    ->query(fn($query, $data) => $query->when($data['score_min'], fn($q, $v) => $q->where('lead_score', '>=', $v))),
                Filter::make('deal_value_min')
                    ->form([TextInput::make('deal_value_min')->numeric()->label(__('filament/leads.min_deal_value'))->prefix(fn () => Currency::defaultSymbol())])
                    ->query(fn($query, $data) => $query->when($data['deal_value_min'] ?? null, fn($q, $v) => $q->where('deal_value', '>=', $v))),
                Filter::make('deal_value_max')
                    ->form([TextInput::make('deal_value_max')->numeric()->label(__('filament/leads.max_deal_value'))->prefix(fn () => Currency::defaultSymbol())])
                    ->query(fn($query, $data) => $query->when($data['deal_value_max'] ?? null, fn($q, $v) => $q->where('deal_value', '<=', $v))),
            ])
            ->actions([
                Action::make('toggle_star')
                    ->icon(fn($record) => $record->is_starred ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn($record) => $record->is_starred ? 'warning' : 'gray')
                    ->tooltip(fn($record) => $record->is_starred ? __('filament/leads.tooltip_unstar') : __('filament/leads.tooltip_star_this_lead'))
                    ->action(fn($record) => $record->updateQuietly(['is_starred' => ! $record->is_starred]))
                    ->iconButton()
                    ->label(''),
                Action::make('view_detail')
                    ->label(__('filament/leads.view_detail_action_label'))
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.leads.view', $record))
                    ->color('gray')
                    ->tooltip(__('filament/leads.tooltip_view_lead')),
                EditAction::make()->tooltip(__('filament/leads.tooltip_edit')),
                DeleteAction::make()->tooltip(__('filament/leads.tooltip_delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('assign')
                        ->label(__('filament/leads.bulk_assign_agent'))
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Select::make('assigned_user_id')
                                ->label(__('filament/leads.bulk_assign_to'))
                                ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['assigned_user_id' => $data['assigned_user_id']]);
                            Notification::make()->success()->title(__('filament/leads.bulk_leads_assigned'))->send();
                        }),

                    BulkAction::make('change_status')
                        ->label(__('filament/leads.bulk_change_status'))
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label(__('filament/leads.status'))
                                ->options(\App\Enums\LeadStatus::options())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['status' => $data['status']]);
                            Notification::make()->success()->title(__('filament/leads.bulk_status_updated'))->send();
                        }),

                    BulkAction::make('add_tag')
                        ->label(__('filament/leads.bulk_add_tag'))
                        ->icon('heroicon-o-tag')
                        ->form([
                            Select::make('tag_id')
                                ->label(__('filament/leads.tag'))
                                ->options(fn() => Tag::where('tenant_id', $tenantId())->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) use ($tenantId) {
                            $tag = \App\Models\Tag::find($data['tag_id']);
                            $dispatcher = app(\App\Services\Automations\AutomationDispatcher::class);
                            $records->each(function ($lead) use ($data, $tag, $dispatcher) {
                                $lead->tags()->syncWithoutDetaching([$data['tag_id']]);
                                if ($tag) {
                                    $dispatcher->dispatch('tag_added', $lead, ['tag' => $tag->name]);
                                }
                            });
                            Notification::make()->success()->title(__('filament/leads.bulk_tag_added'))->send();
                        }),

                    BulkAction::make('remove_tag')
                        ->label(__('filament/leads.bulk_remove_tag'))
                        ->icon('heroicon-o-tag')
                        ->color('warning')
                        ->form([
                            Select::make('tag_id')
                                ->label(__('filament/leads.tag'))
                                ->options(fn() => Tag::where('tenant_id', $tenantId())->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(fn($lead) => $lead->tags()->detach($data['tag_id']));
                            Notification::make()->success()->title(__('filament/leads.bulk_tag_removed'))->send();
                        }),

                    BulkAction::make('move_stage')
                        ->label(__('filament/leads.bulk_move_to_stage'))
                        ->icon('heroicon-o-arrows-right-left')
                        ->form([
                            Select::make('pipeline_id')
                                ->label(__('filament/leads.pipeline'))
                                ->options(fn() => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                                ->reactive()
                                ->required(),
                            Select::make('pipeline_stage_id')
                                ->label(__('filament/leads.stage'))
                                ->options(fn($get) => $get('pipeline_id')
                                    ? PipelineStage::where('pipeline_id', $get('pipeline_id'))->pluck('name', 'id')
                                    : []
                                )
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'pipeline_id'       => $data['pipeline_id'],
                                'pipeline_stage_id' => $data['pipeline_stage_id'],
                            ]);
                            Notification::make()->success()->title(__('filament/leads.bulk_leads_moved'))->send();
                        }),

                    BulkAction::make('export')
                        ->label(__('filament/leads.bulk_export_csv'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) use ($tenantId) {
                            ExportLeads::dispatch($tenantId(), auth()->id(), ['ids' => $records->pluck('id')->toArray()]);
                            Notification::make()->success()->title(__('filament/leads.bulk_export_queued'))->send();
                        }),

                    BulkAction::make('run_automation')
                        ->label(__('filament/leads.bulk_run_automation'))
                        ->icon('heroicon-o-bolt')
                        ->form([
                            Select::make('automation_id')
                                ->label(__('filament/leads.bulk_select_automation'))
                                ->options(fn() => Automation::where('tenant_id', $tenantId())
                                    ->where('enabled', true)
                                    ->where('trigger_type', 'manual')
                                    ->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $automationId = $data['automation_id'];
                            foreach ($records as $lead) {
                                RunAutomation::dispatch($automationId, $lead->id, 0, null, $lead->tenant_id);
                            }
                            Notification::make()->success()->title(__('filament/leads.bulk_automation_queued', ['count' => $records->count()]))->send();
                        }),

                    BulkAction::make('enroll_sequence')
                        ->label(__('filament/leads.bulk_enroll_in_sequence'))
                        ->icon('heroicon-o-envelope-open')
                        ->form([
                            Select::make('sequence_id')
                                ->label(__('filament/leads.bulk_sequence'))
                                ->options(fn() => \App\Models\EmailSequence::where('tenant_id', $tenantId())
                                    ->where('status', 'active')
                                    ->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Collection $records, array $data) use ($tenantId) {
                            $sequenceId = (int) $data['sequence_id'];
                            $added      = 0;
                            $skipped    = 0;
                            foreach ($records as $lead) {
                                // Tenant-scoped existence check — never fall
                                // back to cross-tenant enrollment detection.
                                $already = \App\Models\EmailSequenceEnrollment::query()
                                    ->where('tenant_id', $tenantId())
                                    ->where('sequence_id', $sequenceId)
                                    ->where('lead_id', $lead->id)
                                    ->exists();
                                if ($already) {
                                    $skipped++;
                                    continue;
                                }
                                \App\Models\EmailSequenceEnrollment::create([
                                    'tenant_id'    => $tenantId(),
                                    'sequence_id'  => $sequenceId,
                                    'lead_id'      => $lead->id,
                                    'current_step' => 0,
                                    'status'       => 'active',
                                    'enrolled_at'  => now(),
                                    'next_send_at' => now(),
                                ]);
                                $added++;
                            }
                            Notification::make()
                                ->success()
                                ->title(__('filament/leads.bulk_enrolled_skipped', ['added' => $added, 'skipped' => $skipped]))
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->poll('30s')
            // Empty-state scaffolding — first-time tenants used to see
            // a blank table with no guidance.  These props surface clear
            // CTAs pointing at the three primary lead-ingestion paths.
            ->emptyStateHeading(__('filament/leads.empty_heading'))
            ->emptyStateDescription(__('filament/leads.empty_description'))
            ->emptyStateIcon('heroicon-o-user-plus')
            ->emptyStateActions([
                \Filament\Actions\Action::make('create_lead_empty')
                    ->label(__('filament/leads.empty_add_lead'))
                    ->icon('heroicon-o-plus')
                    ->url(fn () => static::getUrl('create'))
                    ->button(),
                \Filament\Actions\Action::make('import_leads_empty')
                    ->label(__('filament/leads.empty_import_csv'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->url(fn () => \App\Support\AdminUrl::for('imports'))
                    ->outlined(),
                \Filament\Actions\Action::make('create_form_empty')
                    ->label(__('filament/leads.empty_build_form'))
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('gray')
                    ->url(fn () => \App\Support\AdminUrl::for('forms/create'))
                    ->outlined(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();

        return parent::getEloquentQuery()
            // pipelineStage.pipeline is the deep eager-load — the lead
            // view blade reads $lead->pipelineStage->pipeline->name and
            // similar tooltip fields, which would otherwise N+1 once
            // per row.  Adding it to the with() list pre-loads the
            // pipeline relation alongside its parent stage.
            ->with(['tags', 'assignedUser', 'pipelineStage.pipeline', 'companyEntity'])
            // Materialize the two timestamps that drive the
            // `waiting_on` badge column in a single subquery JOIN
            // instead of the per-row activities lookup the accessor
            // would otherwise issue.  Cuts a 50-row table page from
            // 100+ queries to one.
            ->withWaitingOnState()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EmailsRelationManager::class,
            RelationManagers\MessagesRelationManager::class,
            RelationManagers\TasksRelationManager::class,
            RelationManagers\DealItemsRelationManager::class,
            RelationManagers\SequenceEnrollmentsRelationManager::class,
            RelationManagers\PageViewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit'   => Pages\EditLead::route('/{record}/edit'),
            'view'   => Pages\ViewLead::route('/{record}/view'),
        ];
    }
}
