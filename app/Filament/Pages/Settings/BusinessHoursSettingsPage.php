<?php

namespace App\Filament\Pages\Settings;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusinessHoursSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Advanced';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 23;

    protected string $view = 'filament.pages.settings.business-hours-settings-page';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/business_hours_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament/business_hours_settings.title');
    }

    public function mount(): void
    {
        $tenant   = auth()->user()?->tenant;
        $existing = (array) ($tenant?->getSetting('business_hours') ?? []);

        $days = $existing['days'] ?? [];
        $normalized = [];
        for ($i = 0; $i <= 6; $i++) {
            $normalized[] = [
                'dow'     => $i,
                'enabled' => (bool) ($days[$i]['enabled'] ?? ($i >= 1 && $i <= 5)),
                'start'   => (string) ($days[$i]['start'] ?? '09:00'),
                'end'     => (string) ($days[$i]['end']   ?? '17:00'),
            ];
        }

        $this->form->fill([
            'timezone' => $existing['timezone'] ?? (config('app.timezone') ?: 'UTC'),
            'days'     => $normalized,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.business_hours'))
                ->description(__('filament/business_hours_settings.section_description'))
                ->schema([
                    Select::make('timezone')
                        ->label(__('filament/business_hours_settings.timezone_label'))
                        ->options(collect(\DateTimeZone::listIdentifiers())->mapWithKeys(fn ($tz) => [$tz => $tz]))
                        ->searchable()
                        ->required(),

                    Repeater::make('days')
                        ->label(__('filament/business_hours_settings.weekly_schedule_label'))
                        ->schema([
                            Select::make('dow')
                                ->label(__('filament/business_hours_settings.day_label'))
                                ->options([
                                    0 => __('filament/business_hours_settings.day_sunday'),
                                    1 => __('filament/business_hours_settings.day_monday'),
                                    2 => __('filament/business_hours_settings.day_tuesday'),
                                    3 => __('filament/business_hours_settings.day_wednesday'),
                                    4 => __('filament/business_hours_settings.day_thursday'),
                                    5 => __('filament/business_hours_settings.day_friday'),
                                    6 => __('filament/business_hours_settings.day_saturday'),
                                ])
                                ->disabled()
                                ->dehydrated(true),
                            Toggle::make('enabled')->label(__('filament/business_hours_settings.open_label')),
                            TimePicker::make('start')->label(__('filament/business_hours_settings.start_label'))->seconds(false)->required(),
                            TimePicker::make('end')->label(__('filament/business_hours_settings.end_label'))->seconds(false)->required(),
                        ])
                        ->columns(4)
                        ->reorderable(false)
                        ->deletable(false)
                        ->addable(false)
                        ->defaultItems(7),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()?->tenant;
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/business_hours_settings.no_tenant'))->send();
            return;
        }

        $state    = $this->form->getState();
        $settings = (array) ($tenant->settings ?? []);

        $daysOut = [];
        foreach ($state['days'] as $row) {
            $dow = (int) ($row['dow'] ?? 0);
            $daysOut[$dow] = [
                'enabled' => (bool) ($row['enabled'] ?? false),
                'start'   => (string) ($row['start'] ?? '09:00'),
                'end'     => (string) ($row['end']   ?? '17:00'),
            ];
        }

        $settings['business_hours'] = [
            'timezone' => (string) ($state['timezone'] ?? 'UTC'),
            'days'     => $daysOut,
        ];

        $tenant->settings = $settings;
        $tenant->save();

        Notification::make()->success()->title(__('filament/business_hours_settings.saved'))->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/business_hours_settings.action_save'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
