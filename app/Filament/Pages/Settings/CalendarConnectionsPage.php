<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Models\CalendarConnection;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * /admin/settings/calendar — per-user calendar connection manager.
 *
 * Lists the current user's calendar connections (one Google + one
 * Outlook max).  Each row shows status, last-synced-at, last-sync-error
 * and a Disconnect button.  A "Connect" CTA appears for providers
 * the user hasn't linked yet.
 *
 * Outlook is shown but disabled — the connect URL surfaces a friendly
 * "ships in a follow-up" message until OutlookCalendarSyncService
 * lands.  The structure here is intentionally provider-agnostic so
 * Outlook drops in without UI changes.
 */
class CalendarConnectionsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 50;
    protected static ?string $slug = 'settings/calendar';
    protected string $view = 'filament.pages.settings.calendar-connections';

    public static function getNavigationLabel(): string
    {
        return __('filament/calendar_connections.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/calendar_connections.title');
    }

    public function getHeading(): string
    {
        return __('filament/calendar_connections.heading');
    }

    public function getSubheading(): ?string
    {
        return __('filament/calendar_connections.subheading');
    }

    public function getViewData(): array
    {
        $userId = auth()->id();

        $connections = $userId
            ? CalendarConnection::query()
                ->where('user_id', $userId)
                ->get()
                ->keyBy('provider')
            : collect();

        return [
            'connections' => $connections,
            'providers'   => [
                CalendarConnection::PROVIDER_GOOGLE => [
                    'label'       => __('filament/calendar_connections.provider_google'),
                    'icon'        => '📅',
                    'color'       => '#4285F4',
                    'available'   => true,
                    'connect_url' => '/admin/calendar/connect/' . CalendarConnection::PROVIDER_GOOGLE,
                ],
                CalendarConnection::PROVIDER_OUTLOOK => [
                    'label'       => __('filament/calendar_connections.provider_outlook'),
                    'icon'        => '📆',
                    'color'       => '#0078D4',
                    'available'   => true, // OutlookCalendarSyncService now ships in this batch
                    'connect_url' => '/admin/calendar/connect/' . CalendarConnection::PROVIDER_OUTLOOK,
                ],
            ],
            'statuses'    => CalendarConnection::statusLabels(),
        ];
    }
}
