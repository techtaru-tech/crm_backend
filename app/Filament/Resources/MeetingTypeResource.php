<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\MeetingTypeResource\Pages;
use App\Models\MeetingType;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use UnitEnum;

class MeetingTypeResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = MeetingType::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|UnitEnum|null $navigationGroup = 'Tools';
    protected static ?int $navigationSort = 15;

    public static function getNavigationLabel(): string
    {
        return __('filament/meeting_types.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/meeting_types.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/meeting_types.plural_model_label');
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
            Section::make(__('sections.meeting_type'))->schema([
                TextInput::make('name')
                    ->label(__('filament/meeting_types.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $context) {
                        if ($context === 'create') {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->label(__('filament/meeting_types.slug'))
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn($context) => $context === 'edit')
                    ->helperText(__('filament/meeting_types.slug_help')),
                Textarea::make('description')
                    ->label(__('filament/meeting_types.description'))
                    ->rows(3)
                    ->maxLength(1000),
                Select::make('duration_minutes')
                    ->label(__('filament/meeting_types.duration'))
                    ->options([
                        15 => '15 minutes',
                        30 => '30 minutes',
                        45 => '45 minutes',
                        60 => '60 minutes',
                        90 => '90 minutes',
                    ])
                    ->default(30)
                    ->required(),
                TextInput::make('buffer_minutes')
                    ->label(__('filament/meeting_types.buffer_after_meeting'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(120),
                TextInput::make('advance_notice_hours')
                    ->label(__('filament/meeting_types.advance_notice_hours'))
                    ->numeric()
                    ->default(4)
                    ->minValue(0)
                    ->maxValue(168),
                TextInput::make('future_days_max')
                    ->label(__('filament/meeting_types.future_days_max'))
                    ->numeric()
                    ->default(60)
                    ->minValue(1)
                    ->maxValue(365),
                ColorPicker::make('color')->label(__('filament/meeting_types.color'))->default('#4f46e5'),
            ])->columns(2),

            Section::make(__('sections.location'))->schema([
                Select::make('location_type')
                    ->label(__('filament/meeting_types.location_type'))
                    ->options([
                        // `:brand` placeholder keeps the brand name
                        // (Google Meet / Zoom) literal while the
                        // surrounding sentence flips with the locale.
                        'custom'      => __('filament/meeting_types.location_option_custom'),
                        'google_meet' => __('filament/meeting_types.location_option_google_meet', ['brand' => 'Google Meet']),
                        'zoom'        => __('filament/meeting_types.location_option_zoom', ['brand' => 'Zoom']),
                        'phone'       => __('filament/meeting_types.location_option_phone'),
                        'in_person'   => __('filament/meeting_types.location_option_in_person'),
                    ])
                    ->default('custom')
                    ->required()
                    ->live(),
                Textarea::make('location_details')
                    ->label(__('filament/meeting_types.location_details'))
                    ->rows(3)
                    ->helperText(__('filament/meeting_types.location_details_help'))
                    ->visible(fn(callable $get) => in_array($get('location_type'), ['custom', 'in_person', 'phone'])),
            ])->columns(1),

            Section::make(__('sections.lead_routing'))->schema([
                Select::make('pipeline_id')
                    ->label(__('filament/meeting_types.pipeline'))
                    ->options(fn() => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->nullable()
                    ->live(),
                Select::make('pipeline_stage_id')
                    ->label(__('filament/meeting_types.stage'))
                    ->options(function (callable $get) use ($tenantId) {
                        $pid = $get('pipeline_id');
                        if (! $pid) return [];
                        return PipelineStage::where('pipeline_id', $pid)
                            ->where('tenant_id', $tenantId())
                            ->pluck('name', 'id');
                    })
                    ->nullable(),
            ])->columns(2)->collapsed(),

            Section::make(__('sections.status'))->schema([
                Toggle::make('is_active')->label(__('filament/meeting_types.is_active'))->default(true),
            ]),

            Section::make(__('sections.share_this_meeting_type'))
                ->schema([
                    Placeholder::make('booking_link_display')
                        ->label(__('filament/meeting_types.public_booking_link'))
                        ->content(function ($record): HtmlString {
                            if (! $record) {
                                return new HtmlString(
                                    '<em class="text-gray-400">'
                                    . e(__('filament/meeting_types.save_first_for_link'))
                                    . '</em>'
                                );
                            }
                            $url             = e($record->booking_url);
                            $embedLabel      = e(__('filament/meeting_types.embed_snippet_label'));
                            return new HtmlString('
                                <div class="flex items-center gap-3">
                                    <a href="' . $url . '" target="_blank" class="text-primary-600 underline break-all">' . $url . '</a>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">' . $embedLabel . '</p>
                                <pre class="bg-gray-900 text-green-400 rounded-lg p-3 text-xs overflow-x-auto whitespace-pre-wrap break-all font-mono border border-gray-700 mt-1">' . e($record->embed_snippet) . '</pre>
                            ');
                        }),
                ])
                ->hidden(fn($record) => $record === null)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/meeting_types.name'))->searchable()->sortable(),
                TextColumn::make('duration_minutes')
                    ->label(__('filament/meeting_types.col_duration'))
                    ->formatStateUsing(fn($state) => __('filament/meeting_types.minutes_short', ['n' => $state]))
                    ->sortable(),
                TextColumn::make('bookings_count')
                    ->label(__('filament/meeting_types.col_bookings'))
                    ->counts('bookings')
                    ->sortable(),
                ColorColumn::make('color')->label(__('filament/meeting_types.color')),
                IconColumn::make('is_active')->label(__('filament/meeting_types.col_active'))->boolean(),
                TextColumn::make('user.name')->label(__('filament/meeting_types.col_owner'))->sortable(),
                TextColumn::make('booking_url')
                    ->label(__('filament/meeting_types.col_link'))
                    ->copyable()
                    ->copyMessage(__('filament/meeting_types.booking_link_copied'))
                    ->limit(40)
                    ->url(fn($record) => $record->booking_url, shouldOpenInNewTab: true),
            ])
            ->actions([
                Action::make('get_link')
                    ->label(__('filament/meeting_types.share'))
                    ->icon('heroicon-o-link')
                    ->modalHeading(fn($record) => __('filament/meeting_types.share_modal_heading', ['name' => $record->name]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/meeting_types.share_modal_close'))
                    ->modalContent(fn($record) => new HtmlString('
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-700">' . e(__('filament/meeting_types.public_url_label')) . '</p>
                                <a href="' . e($record->booking_url) . '" target="_blank" class="text-primary-600 underline break-all">' . e($record->booking_url) . '</a>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 mt-3">' . e(__('filament/meeting_types.embed_snippet_heading')) . '</p>
                                <pre class="bg-gray-900 text-green-400 rounded-lg p-3 text-xs overflow-x-auto whitespace-pre-wrap break-all font-mono border border-gray-700">' . e($record->embed_snippet) . '</pre>
                            </div>
                        </div>
                    ')),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMeetingTypes::route('/'),
            'create' => Pages\CreateMeetingType::route('/create'),
            'edit'   => Pages\EditMeetingType::route('/{record}/edit'),
        ];
    }
}
