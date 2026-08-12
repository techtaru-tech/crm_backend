<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\PipelineResource\Pages;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PipelineResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'pipeline';
    protected static ?string $model = Pipeline::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament/pipelines.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/pipelines.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/pipelines.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.pipeline_details'))->schema([
                TextInput::make('name')->label(__('filament/pipelines.name'))->required()->maxLength(100),
                Textarea::make('description')->label(__('filament/pipelines.description'))->rows(2)->columnSpanFull(),
                Toggle::make('is_default')->label(__('filament/pipelines.default_pipeline')),
            ])->columns(2),

            Section::make(__('sections.stages'))->schema([
                Repeater::make('stages')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')->label(__('filament/pipelines.stage_name'))->required()->maxLength(100),
                        ColorPicker::make('color')->label(__('filament/pipelines.stage_color'))->default('#6366f1'),
                        Toggle::make('is_won')->label(__('filament/pipelines.win_stage')),
                        Toggle::make('is_lost')->label(__('filament/pipelines.loss_stage')),
                    ])
                    ->columns(4)
                    ->orderColumn('sort_order')
                    ->addActionLabel(__('filament/pipelines.add_stage')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/pipelines.name'))->searchable()->sortable()->weight('medium'),
                TextColumn::make('stages_count')
                    ->label(__('filament/pipelines.stages'))
                    ->counts('stages')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('leads_count')
                    ->label(__('filament/pipelines.leads'))
                    ->counts('leads')
                    ->badge()
                    ->color('success'),
                IconColumn::make('is_default')->boolean()->label(__('filament/pipelines.default')),
                TextColumn::make('created_at')->label(__('filament/pipelines.created_at'))->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_default')
                    ->label(__('filament/pipelines.default_pipeline')),
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
            ->defaultSort('is_default', 'desc')
            // Empty-state scaffolding for first-time tenants.
            ->emptyStateHeading(__('filament/pipelines.empty_heading'))
            ->emptyStateDescription(__('filament/pipelines.empty_description'))
            ->emptyStateIcon('heroicon-o-rectangle-group')
            ->emptyStateActions([
                \Filament\Actions\Action::make('create_pipeline_empty')
                    ->label(__('filament/pipelines.create_first_pipeline'))
                    ->icon('heroicon-o-plus')
                    ->url(fn () => static::getUrl('create'))
                    ->button(),
            ]);
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
            'index'  => Pages\ListPipelines::route('/'),
            'create' => Pages\CreatePipeline::route('/create'),
            'edit'   => Pages\EditPipeline::route('/{record}/edit'),
        ];
    }
}
