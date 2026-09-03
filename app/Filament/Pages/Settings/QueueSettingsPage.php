<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Concerns\PageRequiresPermission;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Queue;

class QueueSettingsPage extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    protected static string|\UnitEnum|null $navigationGroup = 'Advanced';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static ?int $navigationSort = 26;
    protected string $view = 'filament.pages.settings.queue-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/queue_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament/queue_settings.title');
    }

    public function getQueueInfo(): array
    {
        $connection = config('queue.default', 'sync');
        $horizonUrl = url('/horizon');

        return [
            'connection'  => $connection,
            'driver'      => config("queue.connections.{$connection}.driver", $connection),
            'horizon_url' => $horizonUrl,
            'horizon_installed' => class_exists('\Laravel\Horizon\Horizon'),
        ];
    }
}
