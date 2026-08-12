<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\LeadScoringRuleResource\Pages;
use App\Models\LeadScoringRule;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LeadScoringRuleResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = LeadScoringRule::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return __('filament/lead_scoring_rules.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/lead_scoring_rules.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/lead_scoring_rules.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->label(__('filament/lead_scoring_rules.name'))->required()->maxLength(100)->columnSpanFull(),
                Select::make('field')
                    ->label(__('filament/lead_scoring_rules.field'))
                    ->options([
                        'email'       => __('filament/lead_scoring_rules.option_field_email'),
                        'phone'       => __('filament/lead_scoring_rules.option_field_phone'),
                        'source'      => __('filament/lead_scoring_rules.option_field_source'),
                        'status'      => __('filament/lead_scoring_rules.option_field_status'),
                        'first_name'  => __('filament/lead_scoring_rules.option_field_first_name'),
                        'last_name'   => __('filament/lead_scoring_rules.option_field_last_name'),
                    ])
                    ->required(),
                Select::make('operator')
                    ->label(__('filament/lead_scoring_rules.operator'))
                    ->options([
                        'equals'       => __('filament/lead_scoring_rules.option_op_equals'),
                        'not_equals'   => __('filament/lead_scoring_rules.option_op_not_equals'),
                        'contains'     => __('filament/lead_scoring_rules.option_op_contains'),
                        'present'      => __('filament/lead_scoring_rules.option_op_is_present'),
                        'absent'       => __('filament/lead_scoring_rules.option_op_is_absent'),
                        'greater_than' => __('filament/lead_scoring_rules.option_op_greater_than'),
                        'less_than'    => __('filament/lead_scoring_rules.option_op_less_than'),
                    ])
                    ->required(),
                TextInput::make('value')->label(__('filament/lead_scoring_rules.value'))->maxLength(100),
                TextInput::make('points')
                    ->label(__('filament/lead_scoring_rules.points'))
                    ->numeric()
                    ->required()
                    ->default(10)
                    ->helperText(__('filament/lead_scoring_rules.points_helper')),
                Toggle::make('active')->label(__('filament/lead_scoring_rules.active'))->default(true),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/lead_scoring_rules.name'))->searchable()->sortable(),
                TextColumn::make('field')->label(__('filament/lead_scoring_rules.field'))->badge()->color('primary'),
                TextColumn::make('operator')->label(__('filament/lead_scoring_rules.operator'))->badge()->color('info'),
                TextColumn::make('value')->label(__('filament/lead_scoring_rules.value'))->placeholder(__('filament/lead_scoring_rules.placeholder_any')),
                TextColumn::make('points')
                    ->label(__('filament/lead_scoring_rules.points'))
                    ->badge()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),
                IconColumn::make('active')->label(__('filament/lead_scoring_rules.active'))->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeadScoringRules::route('/'),
            'create' => Pages\CreateLeadScoringRule::route('/create'),
            'edit'   => Pages\EditLeadScoringRule::route('/{record}/edit'),
        ];
    }
}
