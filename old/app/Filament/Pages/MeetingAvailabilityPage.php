<?php

namespace App\Filament\Pages;

use App\Models\MeetingAvailabilityRule;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MeetingAvailabilityPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string
    {
        return __('filament/meeting_availability.nav_label');
    }

    public array $data = [];

    public function getView(): string
    {
        return 'filament.pages.meeting-availability';
    }

    public function mount(): void
    {
        $rules = MeetingAvailabilityRule::where('user_id', Auth::id())
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->map(fn($r) => [
                'day_of_week' => (int) $r->day_of_week,
                'start_time'  => substr((string) $r->start_time, 0, 5),
                'end_time'    => substr((string) $r->end_time, 0, 5),
            ])
            ->toArray();

        $this->form->fill(['rules' => $rules]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.weekly_availability'))
                    ->description(__('filament/meeting_availability.section_description'))
                    ->schema([
                        Repeater::make('rules')
                            ->label('')
                            ->schema([
                                Select::make('day_of_week')
                                    ->label(__('filament/meeting_availability.day'))
                                    ->options([
                                        0 => __('filament/meeting_availability.day_sunday'),
                                        1 => __('filament/meeting_availability.day_monday'),
                                        2 => __('filament/meeting_availability.day_tuesday'),
                                        3 => __('filament/meeting_availability.day_wednesday'),
                                        4 => __('filament/meeting_availability.day_thursday'),
                                        5 => __('filament/meeting_availability.day_friday'),
                                        6 => __('filament/meeting_availability.day_saturday'),
                                    ])
                                    ->required(),
                                TextInput::make('start_time')
                                    ->label(__('filament/meeting_availability.field_start_time_label'))
                                    ->type('time')
                                    ->required()
                                    ->default('09:00'),
                                TextInput::make('end_time')
                                    ->label(__('filament/meeting_availability.field_end_time_label'))
                                    ->type('time')
                                    ->required()
                                    ->default('17:00'),
                            ])
                            ->columns(3)
                            ->addActionLabel(__('filament/meeting_availability.add_time_slot'))
                            ->defaultItems(0),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preset_weekdays')
                ->label(__('filament/meeting_availability.preset_weekdays_label'))
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->action(function () {
                    $preset = [];
                    foreach ([1, 2, 3, 4, 5] as $day) {
                        $preset[] = [
                            'day_of_week' => $day,
                            'start_time'  => '09:00',
                            'end_time'    => '17:00',
                        ];
                    }
                    $this->form->fill(['rules' => $preset]);
                    Notification::make()->title(__('notifications.preset_loaded'))->success()->send();
                }),
            Action::make('save')
                ->label(__('filament/meeting_availability.save'))
                ->color('primary')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $userId = Auth::id();

        MeetingAvailabilityRule::where('user_id', $userId)->delete();

        foreach ($state['rules'] ?? [] as $rule) {
            if (! isset($rule['day_of_week'], $rule['start_time'], $rule['end_time'])) {
                continue;
            }
            if ($rule['start_time'] >= $rule['end_time']) {
                continue;
            }
            MeetingAvailabilityRule::create([
                'user_id'     => $userId,
                'day_of_week' => (int) $rule['day_of_week'],
                'start_time'  => $rule['start_time'],
                'end_time'    => $rule['end_time'],
            ]);
        }

        Notification::make()->title(__('notifications.availability_saved'))->success()->send();
    }
}
