<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduledReportResource\Pages;
use App\Models\ScheduledReport;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScheduledReportResource extends Resource
{
    protected static ?string $model                           = ScheduledReport::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup  = 'Reports';
    protected static ?int    $navigationSort                 = 90;

    public static function getNavigationLabel(): string
    {
        return __('filament/scheduled_reports.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/scheduled_reports.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/scheduled_reports.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make(__('sections.report_settings'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/scheduled_reports.name'))
                        ->required()
                        ->placeholder(__('filament/scheduled_reports.name_placeholder'))
                        ->columnSpanFull(),
                    Select::make('report_type')
                        ->label(__('filament/scheduled_reports.report_type'))
                        ->options(ScheduledReport::reportTypeLabels())
                        ->required(),
                    Select::make('frequency')
                        ->label(__('filament/scheduled_reports.delivery_frequency'))
                        ->options(ScheduledReport::frequencyLabels())
                        ->required(),
                    Select::make('format')
                        ->label(__('filament/scheduled_reports.file_format'))
                        ->options(['csv' => __('filament/scheduled_reports.csv_spreadsheet')])
                        ->default('csv'),
                    Toggle::make('is_active')->label(__('filament/scheduled_reports.active'))->default(true),
                ]),

            Section::make(__('sections.recipients'))
                ->description(__('filament/scheduled_reports.recipients_description'))
                ->schema([
                    Repeater::make('recipient_emails')
                        ->label(__('filament/scheduled_reports.email_addresses'))
                        ->schema([
                            TextInput::make('email')
                                ->label(__('filament/scheduled_reports.email'))
                                ->email()
                                ->required()
                                ->placeholder(__('filament/scheduled_reports.email_placeholder')),
                        ])
                        ->addActionLabel(__('filament/scheduled_reports.add_recipient'))
                        ->minItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/scheduled_reports.name'))->searchable()->sortable(),
                TextColumn::make('report_type')
                    ->label(__('filament/scheduled_reports.col_report_type'))
                    ->formatStateUsing(fn($state) => ScheduledReport::reportTypeLabels()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('frequency')
                    ->label(__('filament/scheduled_reports.col_frequency'))
                    ->formatStateUsing(fn($state) => match($state) {
                        'daily'   => __('filament/scheduled_reports.frequency_daily'),
                        'weekly'  => __('filament/scheduled_reports.frequency_weekly'),
                        'monthly' => __('filament/scheduled_reports.frequency_monthly'),
                        default   => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'daily'   => 'success',
                        'weekly'  => 'info',
                        'monthly' => 'warning',
                        default   => 'gray',
                    }),
                TextColumn::make('recipient_emails')
                    ->label(__('filament/scheduled_reports.recipients'))
                    ->formatStateUsing(fn($state) => is_array($state) ? count($state) . __('filament/scheduled_reports.recipients_count_suffix') : '—'),
                IconColumn::make('is_active')->boolean()->label(__('filament/scheduled_reports.active')),
                TextColumn::make('last_sent_at')->dateTime()->label(__('filament/scheduled_reports.last_sent'))->sortable(),
                TextColumn::make('next_due_at')->dateTime()->label(__('filament/scheduled_reports.next_due'))->sortable(),
            ])
            ->filters([
                SelectFilter::make('frequency')
                    ->label(__('filament/scheduled_reports.filter_label_frequency'))
                    ->options(ScheduledReport::frequencyLabels()),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListScheduledReports::route('/'),
            'create' => Pages\CreateScheduledReport::route('/create'),
            'edit'   => Pages\EditScheduledReport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id']        = \App\Support\TenantContext::currentId();
        $data['recipient_emails'] = array_column($data['recipient_emails'] ?? [], 'email');
        return $data;
    }
}
