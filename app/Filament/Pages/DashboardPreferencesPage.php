<?php

namespace App\Filament\Pages;

use App\Models\UserDashboardPreference;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class DashboardPreferencesPage extends Page
{
    protected static ?string $slug = 'dashboard/preferences';
    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public array $enabledWidgets = [];

    public function getView(): string
    {
        return 'filament.pages.dashboard-preferences';
    }

    public function getTitle(): string
    {
        return __('filament/dashboard_preferences.title');
    }

    public function mount(): void
    {
        $pref = UserDashboardPreference::getForUser(Auth::user());
        $this->enabledWidgets = array_values($pref->enabled_widgets ?? []);
    }

    /**
     * Widget options for the CheckboxList.
     *
     * @return array<string,string>
     */
    public function getWidgetOptionsProperty(): array
    {
        return UserDashboardPreference::widgetLabels();
    }

    public function save(): void
    {
        $valid = array_keys(UserDashboardPreference::widgetLabels());
        $enabled = array_values(array_intersect($valid, $this->enabledWidgets ?? []));

        UserDashboardPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            ['enabled_widgets' => $enabled],
        );

        Notification::make()
            ->title(__('filament/dashboard_preferences.notif_saved'))
            ->success()
            ->send();
    }

    public function resetDefaults(): void
    {
        $this->enabledWidgets = UserDashboardPreference::defaultsForUser(Auth::user());
        $this->save();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('filament/dashboard_preferences.back_to_dashboard'))
                ->icon('heroicon-o-arrow-left')
                ->url('/admin')
                ->color('gray'),
        ];
    }
}
