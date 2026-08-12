<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\CustomFieldDefinitionResource\Pages;
use App\Models\CustomFieldDefinition;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class CustomFieldDefinitionResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = CustomFieldDefinition::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-adjustments-horizontal';
    protected static string|UnitEnum|null    $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament/custom_fields.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/custom_fields.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/custom_fields.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.field_definition'))
                ->description(__('filament/custom_fields.definition_description'))
                ->schema([
                    Select::make('entity_type')
                        ->label(__('filament/custom_fields.applies_to'))
                        ->options([
                            CustomFieldDefinition::ENTITY_LEAD    => __('filament/custom_fields.option_entity_lead'),
                            CustomFieldDefinition::ENTITY_COMPANY => __('filament/custom_fields.option_entity_company'),
                        ])
                        ->default(CustomFieldDefinition::ENTITY_LEAD)
                        ->required(),

                    TextInput::make('name')
                        ->label(__('filament/custom_fields.field_label'))
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, callable $set, callable $get, string $operation) {
                            if ($operation !== 'create' || ! $state) {
                                return;
                            }
                            // Only auto-fill key if it's empty
                            if (! $get('key')) {
                                $set('key', Str::slug($state, '_'));
                            }
                        }),

                    TextInput::make('key')
                        ->label(__('filament/custom_fields.key_identifier'))
                        ->helperText(__('filament/custom_fields.key_help'))
                        ->required()
                        ->maxLength(50)
                        ->alphaDash()
                        ->disabled(fn ($operation) => $operation === 'edit')
                        ->dehydrated()
                        ->unique(
                            table: 'custom_field_definitions',
                            column: 'key',
                            ignoreRecord: true,
                            modifyRuleUsing: function ($rule, callable $get) {
                                $tenantId = \App\Support\TenantContext::currentId();
                                return $rule
                                    ->where('tenant_id', $tenantId)
                                    ->where('entity_type', $get('entity_type'));
                            }
                        ),

                    Select::make('field_type')
                        ->label(__('filament/custom_fields.field_type'))
                        ->options(CustomFieldDefinition::fieldTypeLabels())
                        ->default('text')
                        ->required()
                        ->live(),

                    TextInput::make('placeholder')
                        ->label(__('filament/custom_fields.placeholder'))
                        ->maxLength(255)
                        ->nullable(),

                    Textarea::make('help_text')
                        ->label(__('filament/custom_fields.helper_text'))
                        ->rows(2)
                        ->columnSpanFull()
                        ->nullable(),
                ])
                ->columns(2),

            Section::make(__('sections.options'))
                ->description(__('filament/custom_fields.options_description'))
                ->schema([
                    TagsInput::make('options')
                        ->label(__('filament/custom_fields.options'))
                        ->helperText(__('filament/custom_fields.options_help'))
                        ->nullable(),
                ])
                ->visible(fn ($get) => in_array($get('field_type'), ['select', 'multi_select'], true)),

            Section::make(__('sections.visibility_behavior'))
                ->schema([
                    Toggle::make('required')
                        ->label(__('filament/custom_fields.required'))
                        ->default(false),
                    Toggle::make('show_in_form')
                        ->label(__('filament/custom_fields.show_in_form'))
                        ->default(true),
                    Toggle::make('show_in_table')
                        ->label(__('filament/custom_fields.show_in_table'))
                        ->default(false),
                    Toggle::make('show_in_filters')
                        ->label(__('filament/custom_fields.show_in_filters'))
                        ->default(false),
                    TextInput::make('sort_order')
                        ->label(__('filament/custom_fields.sort_order'))
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entity_type')
                    ->label(__('filament/custom_fields.col_entity'))
                    ->badge()
                    ->color(fn ($state) => $state === 'lead' ? 'primary' : 'info')
                    ->formatStateUsing(function ($state) {
                        // Translator-first: reuse the option_entity_* keys
                        // from this resource's lang file so the badge
                        // matches the form select and filter for tenants
                        // running a non-English locale. ucfirst() remains
                        // as the missing-translation safety net.
                        $key        = 'filament/custom_fields.option_entity_' . strtolower((string) $state);
                        $translated = __($key);
                        return is_string($translated) && $translated !== $key
                            ? $translated
                            : ucfirst((string) $state);
                    }),
                TextColumn::make('name')
                    ->label(__('filament/custom_fields.col_field'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('key')
                    ->label(__('filament/custom_fields.col_key'))
                    ->color('gray')
                    ->copyable(),
                TextColumn::make('field_type')
                    ->label(__('filament/custom_fields.col_type'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => CustomFieldDefinition::fieldTypeLabels()[$state] ?? $state),
                IconColumn::make('required')
                    ->label(__('filament/custom_fields.col_req'))
                    ->boolean(),
                IconColumn::make('show_in_form')
                    ->label(__('filament/custom_fields.col_form'))
                    ->boolean(),
                IconColumn::make('show_in_table')
                    ->label(__('filament/custom_fields.col_table'))
                    ->boolean(),
                IconColumn::make('show_in_filters')
                    ->label(__('filament/custom_fields.col_filters'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('filament/custom_fields.col_order'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('entity_type')
                    ->label(__('filament/custom_fields.filter_entity'))
                    ->options([
                        'lead'    => __('filament/custom_fields.option_entity_lead'),
                        'company' => __('filament/custom_fields.option_entity_company'),
                    ]),
                SelectFilter::make('field_type')
                    ->label(__('filament/custom_fields.filter_type'))
                    ->options(CustomFieldDefinition::fieldTypeLabels()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomFieldDefinitions::route('/'),
            'create' => Pages\CreateCustomFieldDefinition::route('/create'),
            'edit'   => Pages\EditCustomFieldDefinition::route('/{record}/edit'),
        ];
    }
}
