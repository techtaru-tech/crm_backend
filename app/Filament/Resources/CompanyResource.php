<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Filament\Resources\Concerns\HasCustomFields;
use App\Models\Company;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CompanyResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = Company::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament/companies.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/companies.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/companies.plural_model_label');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'domain'];
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $schema->components([
            Section::make(__('sections.company_info'))->schema([
                TextInput::make('name')
                    ->label(__('filament/companies.field_name_label'))
                    ->required()
                    ->maxLength(150),
                TextInput::make('domain')
                    ->label(__('filament/companies.domain'))
                    ->placeholder(__('filament/companies.domain_placeholder'))
                    ->maxLength(150),
                Select::make('industry')
                    ->label(__('filament/companies.field_industry_label'))
                    ->options([
                        'technology'     => __('filament/companies.industry_technology'),
                        'finance'        => __('filament/companies.industry_finance'),
                        'healthcare'     => __('filament/companies.industry_healthcare'),
                        'retail'         => __('filament/companies.industry_retail'),
                        'manufacturing'  => __('filament/companies.industry_manufacturing'),
                        'education'      => __('filament/companies.industry_education'),
                        'real_estate'    => __('filament/companies.industry_real_estate'),
                        'construction'   => __('filament/companies.industry_construction'),
                        'hospitality'    => __('filament/companies.industry_hospitality'),
                        'transportation' => __('filament/companies.industry_transportation'),
                        'energy'         => __('filament/companies.industry_energy'),
                        'media'          => __('filament/companies.industry_media'),
                        'nonprofit'      => __('filament/companies.industry_nonprofit'),
                        'government'     => __('filament/companies.industry_government'),
                        'other'          => __('filament/companies.industry_other'),
                    ])
                    ->searchable()
                    ->nullable(),
                Select::make('size')
                    ->label(__('filament/companies.field_size_label'))
                    ->options([
                        'small'      => __('filament/companies.size_small'),
                        'medium'     => __('filament/companies.size_medium'),
                        'large'      => __('filament/companies.size_large'),
                        'enterprise' => __('filament/companies.size_enterprise'),
                    ])
                    ->nullable(),
                TextInput::make('website')
                    ->label(__('filament/companies.field_website_label'))
                    ->url()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-globe-alt'),
                TextInput::make('phone')
                    ->label(__('filament/companies.field_phone_label'))
                    ->tel()
                    ->maxLength(30),
            ])->columns(2),

            Section::make(__('sections.location'))->schema([
                Textarea::make('address')
                    ->label(__('filament/companies.field_address_label'))
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('city')
                    ->label(__('filament/companies.field_city_label'))
                    ->maxLength(100),
                TextInput::make('country')
                    ->label(__('filament/companies.field_country_label'))
                    ->maxLength(100),
            ])->columns(2),

            Section::make(__('sections.assignment'))->schema([
                Select::make('assigned_user_id')
                    ->label(__('filament/companies.account_owner'))
                    ->options(fn() => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ])->columns(2),

            Section::make(__('sections.notes'))->schema([
                Textarea::make('notes')
                    ->label(__('filament/companies.field_notes_label'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]),

            ...(count($customFields = HasCustomFields::getCustomFieldFormComponents('company')) > 0
                ? [Section::make(__('sections.custom_fields'))->schema($customFields)->columns(2)->collapsible()]
                : []
            ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/companies.col_company_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('domain')
                    ->label(__('filament/companies.col_domain'))
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('industry')
                    ->label(__('filament/companies.col_industry'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('size')
                    ->label(__('filament/companies.col_size'))
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('leads_count')
                    ->label(__('filament/companies.contacts'))
                    ->counts('leads')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('open_deals_value')
                    ->label(__('filament/companies.open_pipeline'))
                    ->money('USD')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('filament/companies.owner'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('city')
                    ->label(__('filament/companies.col_city'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->label(__('filament/companies.col_country'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament/companies.col_created_at'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('industry')
                    ->label(__('filament/companies.industry'))
                    ->options([
                        'technology'     => __('filament/companies.industry_technology'),
                        'finance'        => __('filament/companies.industry_finance'),
                        'healthcare'     => __('filament/companies.industry_healthcare'),
                        'retail'         => __('filament/companies.industry_retail'),
                        'manufacturing'  => __('filament/companies.industry_manufacturing'),
                        'education'      => __('filament/companies.industry_education'),
                        'real_estate'    => __('filament/companies.industry_real_estate'),
                        'construction'   => __('filament/companies.industry_construction'),
                        'hospitality'    => __('filament/companies.industry_hospitality'),
                        'transportation' => __('filament/companies.industry_transportation'),
                        'energy'         => __('filament/companies.industry_energy'),
                        'media'          => __('filament/companies.industry_media'),
                        'nonprofit'      => __('filament/companies.industry_nonprofit'),
                        'government'     => __('filament/companies.industry_government'),
                        'other'          => __('filament/companies.industry_other'),
                    ]),
                SelectFilter::make('size')
                    ->label(__('filament/companies.size'))
                    ->options([
                        'small'      => __('filament/companies.size_small_short'),
                        'medium'     => __('filament/companies.size_medium_short'),
                        'large'      => __('filament/companies.size_large_short'),
                        'enterprise' => __('filament/companies.size_enterprise_short'),
                    ]),
                SelectFilter::make('assigned_user_id')
                    ->label(__('filament/companies.owner'))
                    ->relationship('assignedUser', 'name'),
            ])
            ->actions([
                ViewAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()
            ->withCount('leads')
            ->with('assignedUser')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LeadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view'   => Pages\ViewCompany::route('/{record}'),
            'edit'   => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
