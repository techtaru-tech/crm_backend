<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use App\Models\User;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Sales Teams — the sub-groups a workspace splits its reps into.
 *
 * Named "Sales Team" in the UI throughout, because "Team" already means
 * "everyone in this workspace" over in Settings → Users & Access.
 */
class TeamResource extends Resource
{
    use HasRolePermissions;

    protected static string $permissionPrefix = 'teams';
    protected static ?string $model = Team::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string|UnitEnum|null $navigationGroup = 'Users & Access';
    protected static ?int $navigationSort = 26;

    public static function getNavigationLabel(): string
    {
        return __('filament/teams.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/teams.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/teams.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn () => \App\Support\TenantContext::currentId();

        return $schema->components([
            Section::make(__('filament/teams.section_details'))->schema([
                TextInput::make('name')
                    ->label(__('filament/teams.name'))
                    ->required()
                    ->maxLength(120)
                    ->unique(
                        table: 'teams',
                        column: 'name',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule) use ($tenantId) {
                            if ($tid = $tenantId()) {
                                $rule->where('tenant_id', $tid);
                            }
                            return $rule;
                        }
                    ),
                Toggle::make('is_active')
                    ->label(__('filament/teams.is_active'))
                    ->default(true),
                Textarea::make('description')
                    ->label(__('filament/teams.description'))
                    ->rows(2)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('filament/teams.section_members'))
                ->description(__('filament/teams.section_members_help'))
                ->schema([
                    Select::make('members')
                        ->label(__('filament/teams.members'))
                        ->multiple()
                        ->options(fn () => User::where('tenant_id', $tenantId())->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->helperText(__('filament/teams.members_help')),
                    Select::make('managers')
                        ->label(__('filament/teams.managers'))
                        ->multiple()
                        ->options(fn () => User::where('tenant_id', $tenantId())->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->helperText(__('filament/teams.managers_help')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/teams.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Team $record) => $record->description),
                TextColumn::make('managers_list')
                    ->label(__('filament/teams.managers'))
                    ->state(fn (Team $record) => $record->managers->pluck('name')->implode(', ') ?: '—')
                    ->wrap(),
                TextColumn::make('users_count')
                    ->label(__('filament/teams.member_count'))
                    ->counts('users')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('leads_count')
                    ->label(__('filament/teams.lead_count'))
                    ->counts('leads')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament/teams.is_active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('filament/teams.created_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament/teams.is_active')),
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
            ->defaultSort('name')
            ->emptyStateHeading(__('filament/teams.empty_heading'))
            ->emptyStateDescription(__('filament/teams.empty_description'))
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();

        return parent::getEloquentQuery()
            ->with('managers')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit'   => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
