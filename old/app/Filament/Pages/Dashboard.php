<?php

namespace App\Filament\Pages;

use App\Models\UserDashboardPreference;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    /**
     * Filter the panel's registered widgets through the current user's
     * dashboard preferences. Widgets whose class basename is not in the
     * user's enabled_widgets list are omitted.
     */
    public function getWidgets(): array
    {
        $widgets = parent::getWidgets();

        $user = Auth::user();
        if (! $user) {
            return $widgets;
        }

        $pref    = UserDashboardPreference::getForUser($user);
        $enabled = $pref->enabled_widgets ?? [];

        if (empty($enabled)) {
            return $widgets;
        }

        return array_values(array_filter($widgets, function ($widget) use ($enabled) {
            $name = is_string($widget) ? class_basename($widget) : class_basename($widget::class);
            return in_array($name, $enabled, true);
        }));
    }
}
