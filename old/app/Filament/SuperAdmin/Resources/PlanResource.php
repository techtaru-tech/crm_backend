<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PlanResource\Pages;
use App\Models\Plan;
use App\Services\PlanService;
use App\Support\Currency;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * Super-Admin CRUD for the plans catalog. Replaces `config/plans.php`
 * as the source of truth — script owners can now add, reprice, and
 * retire plans without shell access.
 *
 * PlanService is flushed after every write so the cached plan map
 * picks up changes on the next request.
 */
class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string|UnitEnum|null $navigationGroup = 'Billing';
    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_plans.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/sa_plans.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/sa_plans.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make(__('filament/sa_plans.tabs_outer'))->tabs([

                Tab::make(__('filament/sa_plans.tab_basics'))->icon('heroicon-o-sparkles')->schema([
                    Section::make()->schema([
                        TextInput::make('key')
                            ->label(__('filament/sa_plans.plan_key'))
                            ->required()
                            ->maxLength(40)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('filament/sa_plans.plan_key_helper'))
                            ->dehydrateStateUsing(fn ($state) => Str::slug($state, '_'))
                            ->disabledOn('edit'),

                        TextInput::make('name')
                            ->label(__('filament/sa_plans.name'))
                            ->required()
                            ->maxLength(80)
                            ->helperText(__('filament/sa_plans.name_helper')),

                        Textarea::make('description')
                            ->label(__('filament/sa_plans.description'))
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                    Section::make(__('sections.pricing'))->schema([
                        TextInput::make('price')
                            ->label(__('filament/sa_plans.monthly_price'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->helperText(__('filament/sa_plans.monthly_price_helper')),

                        Select::make('currency')
                            ->label(__('filament/sa_plans.currency'))
                            ->required()
                            ->options(static::currencyOptions())
                            ->default('USD')
                            ->searchable(),

                        Select::make('interval')
                            ->label(__('filament/sa_plans.interval'))
                            ->required()
                            ->options([
                                'month' => __('filament/sa_plans.interval_monthly'),
                                'year'  => __('filament/sa_plans.interval_yearly'),
                                'week'  => __('filament/sa_plans.interval_weekly'),
                                'day'   => __('filament/sa_plans.interval_daily'),
                            ])
                            ->default('month')
                            ->helperText(__('filament/sa_plans.interval_helper')),

                        TextInput::make('trial_days')
                            ->label(__('filament/sa_plans.trial_days'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(365)
                            ->default(0)
                            ->suffix(__('filament/sa_plans.trial_days_suffix'))
                            ->helperText(__('filament/sa_plans.trial_days_helper')),

                        TextInput::make('annual_price')
                            ->label(__('filament/sa_plans.annual_price'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->helperText(__('filament/sa_plans.annual_price_helper')),

                        TextInput::make('annual_discount_percent')
                            ->label(__('filament/sa_plans.annual_discount_percent'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99)
                            ->suffix('%')
                            ->helperText(__('filament/sa_plans.annual_discount_percent_helper')),
                    ])->columns(4),

                    Section::make(__('sections.visibility'))->schema([
                        Toggle::make('is_active')
                            ->label(__('filament/sa_plans.active'))
                            ->default(true)
                            ->helperText(__('filament/sa_plans.active_helper')),
                        Toggle::make('is_public')
                            ->label(__('filament/sa_plans.public'))
                            ->default(true)
                            ->helperText(__('filament/sa_plans.public_helper')),
                        Toggle::make('highlight')
                            ->label(__('filament/sa_plans.highlight'))
                            ->default(false)
                            ->helperText(__('filament/sa_plans.highlight_helper')),
                        TextInput::make('sort_order')
                            ->label(__('filament/sa_plans.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('filament/sa_plans.sort_order_helper')),
                    ])->columns(4),
                ]),

                Tab::make(__('filament/sa_plans.tab_limits'))->icon('heroicon-o-scale')->schema([
                    Section::make(__('sections.numeric_limits'))
                        ->description(__('filament/sa_plans.limits_description'))
                        ->schema([
                            KeyValue::make('limits')
                                ->label(__('filament/sa_plans.limits'))
                                ->keyLabel(__('filament/sa_plans.limit_key'))
                                ->valueLabel(__('filament/sa_plans.limit_value'))
                                ->addActionLabel(__('filament/sa_plans.add_limit'))
                                ->default([
                                    'max_seats'          => '5',
                                    'max_leads'          => '2000',
                                    'max_forms'          => '10',
                                    'max_pipelines'      => '5',
                                    'max_automations'    => '5',
                                    'max_integrations'   => '3',
                                    'max_api_keys'       => '2',
                                    'max_custom_domains' => '0',
                                ]),
                        ]),
                ]),

                Tab::make(__('filament/sa_plans.tab_features'))->icon('heroicon-o-check-circle')->schema([
                    Section::make(__('sections.feature_flags'))
                        ->description(__('filament/sa_plans.features_description'))
                        ->schema([
                            KeyValue::make('features')
                                ->label(__('filament/sa_plans.features'))
                                ->keyLabel(__('filament/sa_plans.feature_key'))
                                ->valueLabel(__('filament/sa_plans.feature_enabled'))
                                ->addActionLabel(__('filament/sa_plans.add_feature'))
                                ->default([
                                    'integrations'      => 'true',
                                    'automations'       => 'true',
                                    'api_access'        => 'true',
                                    'custom_domain'     => 'false',
                                    'white_label'       => 'false',
                                    'webhooks_outbound' => 'false',
                                    'reports_advanced'  => 'false',
                                    'priority_support'  => 'false',
                                ]),
                        ]),
                ]),

                Tab::make(__('filament/sa_plans.tab_gateway_ids'))->icon('heroicon-o-credit-card')->schema([
                    Section::make(__('sections.external_price_ids'))
                        ->description(__('filament/sa_plans.gateway_ids_description'))
                        ->schema([
                            TextInput::make('stripe_price_id')->label(__('filament/sa_plans.stripe_price_id'))->maxLength(120),
                            TextInput::make('paddle_price_id')->label(__('filament/sa_plans.paddle_price_id'))->maxLength(120),
                            TextInput::make('razorpay_plan_id')->label(__('filament/sa_plans.razorpay_plan_id'))->maxLength(120),
                            TextInput::make('paystack_plan_code')->label(__('filament/sa_plans.paystack_plan_code'))->maxLength(120),
                        ])->columns(2),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')->label(__('filament/sa_plans.column_number'))->sortable(),
                TextColumn::make('name')->label(__('filament/sa_plans.name'))->searchable()->sortable()
                    ->description(fn (Plan $r) => $r->key),
                TextColumn::make('price')
                    ->label(__('filament/sa_plans.price'))
                    ->money(fn (Plan $r) => $r->currency)
                    ->sortable(),
                TextColumn::make('interval')
                    ->label(__('filament/sa_plans.interval'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'month' => __('filament/sa_plans.interval_month'),
                        'year'  => __('filament/sa_plans.interval_year'),
                        default => (string) $state,
                    })
                    ->sortable(),
                IconColumn::make('is_active')->label(__('filament/sa_plans.column_active'))->boolean(),
                IconColumn::make('is_public')->label(__('filament/sa_plans.column_public'))->boolean(),
                IconColumn::make('highlight')->label(__('filament/sa_plans.column_highlight'))->boolean(),
                TextColumn::make('updated_at')->label(__('filament/sa_plans.updated_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')->label(__('filament/sa_plans.filter_active_label'))->options(['1' => __('filament/sa_plans.filter_active'), '0' => __('filament/sa_plans.filter_inactive')]),
                SelectFilter::make('interval')
                    ->label(__('filament/sa_plans.filter_label_interval'))
                    ->options([
                        'month' => __('filament/sa_plans.interval_monthly'),
                        'year'  => __('filament/sa_plans.interval_yearly'),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->after(fn () => app(PlanService::class)->flushCache()),
                DeleteAction::make()
                    ->after(fn () => app(PlanService::class)->flushCache()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit'   => Pages\EditPlan::route('/{record}/edit'),
        ];
    }

    /**
     * ISO-4217 codes we enable by default. The admin can still enter any
     * 3-letter code through gateway settings, but the UI list stays readable.
     */
    protected static function currencyOptions(): array
    {
        return [
            'USD' => 'USD — US Dollar',
            'EUR' => 'EUR — Euro',
            'GBP' => 'GBP — British Pound',
            'INR' => 'INR — Indian Rupee',
            'NGN' => 'NGN — Nigerian Naira',
            'ZAR' => 'ZAR — South African Rand',
            'KES' => 'KES — Kenyan Shilling',
            'GHS' => 'GHS — Ghanaian Cedi',
            'BRL' => 'BRL — Brazilian Real',
            'MXN' => 'MXN — Mexican Peso',
            'AUD' => 'AUD — Australian Dollar',
            'CAD' => 'CAD — Canadian Dollar',
            'AED' => 'AED — UAE Dirham',
            'SAR' => 'SAR — Saudi Riyal',
            'IDR' => 'IDR — Indonesian Rupiah',
            'PHP' => 'PHP — Philippine Peso',
            'MYR' => 'MYR — Malaysian Ringgit',
            'TRY' => 'TRY — Turkish Lira',
            'EGP' => 'EGP — Egyptian Pound',
            'PKR' => 'PKR — Pakistani Rupee',
        ];
    }
}
