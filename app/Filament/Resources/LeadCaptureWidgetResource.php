<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\LeadCaptureWidgetResource\Pages;
use App\Models\LeadCaptureWidget;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadCaptureWidgetResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'forms';
    protected static ?string $model                           = LeadCaptureWidget::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';
    protected static string|\UnitEnum|null $navigationGroup  = 'Tools';
    protected static ?int    $navigationSort                 = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament/lead_capture_widgets.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/lead_capture_widgets.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/lead_capture_widgets.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $schema->components([

            Section::make(__('sections.widget_identity'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->columnSpanFull(),
                    TextInput::make('headline')->required()->default(__('filament/lead_capture_widgets.default_headline')),
                    TextInput::make('subheadline')->nullable()->placeholder(__('filament/lead_capture_widgets.subheadline_placeholder')),
                    TextInput::make('button_text')->required()->default(__('filament/lead_capture_widgets.default_button_text')),
                    Textarea::make('success_message')
                        ->required()
                        ->default(__('filament/lead_capture_widgets.default_success_message'))
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make(__('sections.appearance'))
                ->columns(3)
                ->schema([
                    ColorPicker::make('primary_color')->default('#3b82f6'),
                    ColorPicker::make('text_color')->default('#ffffff'),
                    Select::make('position')
                        ->options([
                            'bottom-right' => __('filament/lead_capture_widgets.position_bottom_right'),
                            'bottom-left'  => __('filament/lead_capture_widgets.position_bottom_left'),
                            'top-right'    => __('filament/lead_capture_widgets.position_top_right'),
                            'top-left'     => __('filament/lead_capture_widgets.position_top_left'),
                        ])
                        ->default('bottom-right'),
                ]),

            Section::make(__('sections.form_fields'))
                ->columns(2)
                ->schema([
                    Toggle::make('show_phone')->label(__('filament/lead_capture_widgets.show_phone'))->default(true),
                    Toggle::make('require_phone')->label(__('filament/lead_capture_widgets.require_phone'))->default(false),
                    Toggle::make('show_company')->label(__('filament/lead_capture_widgets.show_company'))->default(false),
                    Toggle::make('show_message')->label(__('filament/lead_capture_widgets.show_message'))->default(true),
                    Toggle::make('require_message')->label(__('filament/lead_capture_widgets.require_message'))->default(false),
                ]),

            Section::make(__('sections.lead_routing'))
                ->columns(2)
                ->schema([
                    Select::make('pipeline_id')
                        ->label(__('filament/lead_capture_widgets.route_leads_to_pipeline'))
                        ->options(fn() => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                        ->nullable()
                        ->live(),
                    Select::make('pipeline_stage_id')
                        ->label(__('filament/lead_capture_widgets.initial_stage'))
                        ->options(fn($get) => $get('pipeline_id')
                            ? PipelineStage::where('pipeline_id', $get('pipeline_id'))->pluck('name', 'id')
                            : [])
                        ->nullable(),
                ]),

            Section::make(__('sections.status'))
                ->schema([
                    Toggle::make('is_active')->label(__('filament/lead_capture_widgets.widget_is_active'))->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/lead_capture_widgets.name'))->searchable()->sortable(),
                TextColumn::make('position')
                    ->label(__('filament/lead_capture_widgets.position'))
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')->boolean()->label(__('filament/lead_capture_widgets.active')),
                TextColumn::make('leads_captured')
                    ->label(__('filament/lead_capture_widgets.leads_captured'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')->label(__('filament/lead_capture_widgets.created_at'))->date()->sortable()->toggleable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label(__('filament/lead_capture_widgets.action_preview'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    // Opens a mock host page with the widget embedded
                    // so operators can verify colours / copy / position
                    // before deploying the snippet. Works for drafts
                    // (is_active=false) too.
                    ->url(
                        fn (LeadCaptureWidget $record) => url('/widget/' . $record->uuid . '/preview'),
                        shouldOpenInNewTab: true
                    ),
                Action::make('get_snippet')
                    ->label(__('filament/lead_capture_widgets.action_get_snippet'))
                    ->icon('heroicon-o-code-bracket')
                    ->color('success')
                    ->modalHeading(__('filament/lead_capture_widgets.snippet_modal_heading'))
                    ->modalDescription(__('filament/lead_capture_widgets.snippet_modal_description'))
                    // Modal markup + static styles extracted to a Blade
                    // view + dedicated stylesheet so this file no longer
                    // carries inline style="" attributes (translatable-
                    // strings + no-inline-styles rule).  See:
                    //   resources/views/filament/resources/lead-capture-widgets/snippet-modal.blade.php
                    //   public/css/views/filament/resources/lead-capture-widgets/snippet-modal.css
                    ->modalContent(fn (LeadCaptureWidget $record) => new \Illuminate\Support\HtmlString(
                        view('filament.resources.lead-capture-widgets.snippet-modal', [
                            'record' => $record,
                        ])->render()
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/lead_capture_widgets.modal_close')),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeadCaptureWidgets::route('/'),
            'create' => Pages\CreateLeadCaptureWidget::route('/create'),
            'edit'   => Pages\EditLeadCaptureWidget::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }
}
