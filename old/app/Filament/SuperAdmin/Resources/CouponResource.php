<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\CouponResource\Pages;
use App\Models\Coupon;
use App\Models\Plan;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * SA-side CRUD for the coupon catalog.
 *
 * Use cases the form supports out-of-box:
 *   - First-month-free (percent=100, max_per_tenant=1)
 *   - Black Friday 30% (percent=30, ends_at=Dec 1, max_uses=500)
 *   - $20 off USD plans (fixed=20, currency=USD)
 *   - Influencer code unlimited 25% (percent=25, max_uses=null)
 *   - Beta-tester trial extension (trial_extension=30, max_per_tenant=1)
 */
class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';
    protected static string|UnitEnum|null $navigationGroup = 'Billing';
    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_coupons.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/sa_coupons.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/sa_coupons.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.code_description'))
                ->schema([
                    TextInput::make('code')
                        ->label(__('filament/sa_coupons.code'))
                        ->required()
                        ->maxLength(40)
                        ->unique(ignoreRecord: true)
                        ->dehydrateStateUsing(fn ($state) => strtoupper(trim((string) $state)))
                        ->helperText(__('filament/sa_coupons.code_helper')),

                    Textarea::make('description')
                        ->label(__('filament/sa_coupons.description'))
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->helperText(__('filament/sa_coupons.description_helper')),
                ])->columns(2),

            Section::make(__('sections.discount'))
                ->schema([
                    Select::make('discount_type')
                        ->label(__('filament/sa_coupons.discount_type'))
                        ->options(Coupon::typeLabels())
                        ->default(Coupon::TYPE_PERCENT)
                        ->required()
                        ->live()
                        ->helperText(__('filament/sa_coupons.discount_type_helper')),

                    TextInput::make('discount_value')
                        ->label(__('filament/sa_coupons.discount_value'))
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->suffix(fn (Get $get) => match ($get('discount_type')) {
                            Coupon::TYPE_PERCENT          => '%',
                            Coupon::TYPE_TRIAL_EXTENSION  => __('filament/sa_coupons.discount_value_suffix_days'),
                            default                       => '',
                        })
                        ->helperText(fn (Get $get) => match ($get('discount_type')) {
                            Coupon::TYPE_PERCENT          => __('filament/sa_coupons.discount_value_helper_percent'),
                            Coupon::TYPE_FIXED            => __('filament/sa_coupons.discount_value_helper_fixed'),
                            Coupon::TYPE_TRIAL_EXTENSION  => __('filament/sa_coupons.discount_value_helper_trial'),
                            default                       => '',
                        }),

                    Select::make('currency')
                        ->label(__('filament/sa_coupons.currency'))
                        ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'INR' => 'INR'])
                        ->visible(fn (Get $get) => $get('discount_type') === Coupon::TYPE_FIXED)
                        ->helperText(__('filament/sa_coupons.currency_helper')),
                ])->columns(3),

            Section::make(__('sections.limits_targeting'))
                ->schema([
                    TextInput::make('max_uses')
                        ->label(__('filament/sa_coupons.max_total_uses'))
                        ->numeric()
                        ->minValue(1)
                        ->placeholder(__('filament/sa_coupons.max_total_uses_placeholder'))
                        ->helperText(__('filament/sa_coupons.max_total_uses_helper')),

                    TextInput::make('max_per_tenant')
                        ->label(__('filament/sa_coupons.max_per_tenant'))
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->helperText(__('filament/sa_coupons.max_per_tenant_helper')),

                    Select::make('applies_to_plans')
                        ->label(__('filament/sa_coupons.applies_to_plans'))
                        ->multiple()
                        ->options(fn () => Plan::query()->orderBy('sort_order')->pluck('name', 'key')->all())
                        ->placeholder(__('filament/sa_coupons.applies_to_plans_placeholder'))
                        ->helperText(__('filament/sa_coupons.applies_to_plans_helper'))
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make(__('sections.validity_window'))
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->label(__('filament/sa_coupons.starts_at'))
                        ->seconds(false)
                        ->placeholder(__('filament/sa_coupons.starts_at_placeholder'))
                        ->helperText(__('filament/sa_coupons.starts_at_helper')),

                    DateTimePicker::make('ends_at')
                        ->label(__('filament/sa_coupons.ends_at'))
                        ->seconds(false)
                        ->placeholder(__('filament/sa_coupons.ends_at_placeholder'))
                        ->helperText(__('filament/sa_coupons.ends_at_helper')),

                    Toggle::make('is_active')
                        ->label(__('filament/sa_coupons.is_active'))
                        ->default(true)
                        ->inline(false)
                        ->helperText(__('filament/sa_coupons.is_active_helper')),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label(__('filament/sa_coupons.code'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('discount_type')
                    ->label(__('filament/sa_coupons.column_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Coupon::typeLabels()[$state] ?? $state),

                TextColumn::make('discount_value')
                    ->label(__('filament/sa_coupons.column_value'))
                    ->formatStateUsing(function (Coupon $r): string {
                        return match ($r->discount_type) {
                            Coupon::TYPE_PERCENT          => number_format((float) $r->discount_value, 0) . '%',
                            Coupon::TYPE_FIXED            => number_format((float) $r->discount_value, 2) . ' ' . ($r->currency ?? ''),
                            Coupon::TYPE_TRIAL_EXTENSION  => '+' . (int) $r->discount_value . ' ' . __('filament/sa_coupons.trial_days_suffix'),
                            default                       => (string) $r->discount_value,
                        };
                    }),

                TextColumn::make('uses')
                    ->label(__('filament/sa_coupons.column_uses'))
                    ->state(fn (Coupon $r) => $r->max_uses === null
                        ? "{$r->times_used} / ∞"
                        : "{$r->times_used} / {$r->max_uses}"
                    ),

                TextColumn::make('status')
                    ->label(__('filament/sa_coupons.column_status'))
                    ->badge()
                    ->state(function (Coupon $r): string {
                        if (! $r->is_active) return 'inactive';
                        if ($r->ends_at && $r->ends_at->isPast()) return 'expired';
                        if ($r->starts_at && $r->starts_at->isFuture()) return 'scheduled';
                        if (! $r->hasUsesLeft()) return 'exhausted';
                        return 'active';
                    })
                    ->color(fn (string $state) => match ($state) {
                        'active'    => 'success',
                        'scheduled' => 'info',
                        'expired'   => 'gray',
                        'exhausted' => 'warning',
                        'inactive'  => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active'    => __('filament/sa_coupons.status_active'),
                        'scheduled' => __('filament/sa_coupons.status_scheduled'),
                        'expired'   => __('filament/sa_coupons.status_expired'),
                        'exhausted' => __('filament/sa_coupons.status_exhausted'),
                        'inactive'  => __('filament/sa_coupons.status_inactive'),
                        default     => (string) $state,
                    }),

                TextColumn::make('ends_at')
                    ->label(__('filament/sa_coupons.ends_at'))
                    ->date()
                    ->sortable()
                    ->placeholder(__('filament/sa_coupons.column_ends_at_placeholder'))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('filament/sa_coupons.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->label(__('filament/sa_coupons.filter_label_discount_type'))
                    ->options(Coupon::typeLabels()),
                SelectFilter::make('is_active')->label(__('filament/sa_coupons.filter_active'))->options(['1' => __('filament/sa_coupons.filter_active_yes'), '0' => __('filament/sa_coupons.filter_active_no')]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
