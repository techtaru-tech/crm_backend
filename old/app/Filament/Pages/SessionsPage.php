<?php

namespace App\Filament\Pages;

use App\Models\UserSession;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SessionsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 45;
    protected string $view = 'filament.pages.sessions';

    public static function getNavigationLabel(): string
    {
        return __('filament/sessions.nav_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sessions.title');
    }

    public function getSessions()
    {
        return UserSession::where('user_id', Auth::id())
            ->whereNull('revoked_at')
            ->orderByDesc('last_active_at')
            ->get();
    }

    public function revokeSession(int $sessionId): void
    {
        $session = UserSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $session) {
            return;
        }

        if ($session->session_id === request()->session()->getId()) {
            Notification::make()->title(__('notifications.cannot_revoke_current_session'))->warning()->send();
            return;
        }

        $session->revoke();
        Notification::make()->title(__('notifications.session_revoked'))->success()->send();
    }

    public function revokeAllOtherSessions(): void
    {
        $currentSessionId = request()->session()->getId();
        UserSession::where('user_id', Auth::id())
            ->where('session_id', '!=', $currentSessionId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
        Notification::make()->title(__('notifications.all_other_sessions_revoked'))->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revokeAll')
                ->label(__('filament/sessions.revoke_all_others'))
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->revokeAllOtherSessions()),
        ];
    }
}
