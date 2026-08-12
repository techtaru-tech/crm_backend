<?php

namespace App\Filament\Pages;

use App\Models\NotificationPreference;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class NotificationPreferencesPage extends Page
{
    public function getView(): string
    {
        return 'filament.pages.notification-preferences';
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string
    {
        return __('filament/notification_preferences.nav_label');
    }

    public array $preferences = [];

    public function mount(): void
    {
        $userId = Auth::id();
        $saved  = NotificationPreference::where('user_id', $userId)->get()
            ->groupBy(fn($p) => $p->notification_type . '.' . $p->channel);

        foreach (NotificationPreference::TYPES as $type => $label) {
            foreach (NotificationPreference::CHANNELS as $channel) {
                $pref = $saved->get("{$type}.{$channel}")?->first();
                $this->preferences[$type][$channel] = [
                    'enabled'         => $pref ? (bool) $pref->enabled : true,
                    'email_frequency' => $pref ? $pref->email_frequency : 'immediate',
                ];
            }
        }
    }

    public function save(): void
    {
        $userId = Auth::id();

        foreach ($this->preferences as $type => $channels) {
            foreach ($channels as $channel => $values) {
                NotificationPreference::updateOrCreate(
                    ['user_id' => $userId, 'notification_type' => $type, 'channel' => $channel],
                    [
                        'enabled'         => $values['enabled'] ?? true,
                        'email_frequency' => $values['email_frequency'] ?? 'immediate',
                    ]
                );
            }
        }

        Notification::make()
            ->title(__('filament/notification_preferences.notif_saved'))
            ->success()
            ->send();
    }
}
