<?php

namespace App\Livewire;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationCenter extends Component
{
    public bool $open = false;
    public int $unreadCount = 0;
    public array $notifications = [];
    public int $page = 1;
    public bool $hasMore = false;

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $this->unreadCount = Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
        if ($this->open) {
            $this->page = 1;
            $this->loadNotifications();
        }
    }

    public function loadNotifications(): void
    {
        $perPage = 15;
        $query   = Auth::user()
            ?->notifications()
            ->orderByDesc('created_at');

        $total   = $query->count();
        $items   = $query->forPage($this->page, $perPage)->get();

        $this->notifications = $items->map(fn($n) => [
            'id'        => $n->id,
            'type'      => $n->data['type'] ?? 'unknown',
            'message'   => $this->buildMessage($n->data),
            'lead_id'   => $n->data['lead_id'] ?? null,
            'read'      => (bool) $n->read_at,
            'timestamp' => $n->created_at->diffForHumans(),
        ])->toArray();

        $this->hasMore = ($this->page * $perPage) < $total;
    }

    public function loadMore(): void
    {
        $this->page++;
        $perPage = 15;
        $query   = Auth::user()?->notifications()->orderByDesc('created_at');
        $items   = $query->forPage($this->page, $perPage)->get();

        $newItems = $items->map(fn($n) => [
            'id'        => $n->id,
            'type'      => $n->data['type'] ?? 'unknown',
            'message'   => $this->buildMessage($n->data),
            'lead_id'   => $n->data['lead_id'] ?? null,
            'read'      => (bool) $n->read_at,
            'timestamp' => $n->created_at->diffForHumans(),
        ])->toArray();

        $this->notifications = array_merge($this->notifications, $newItems);
        $total = Auth::user()?->notifications()->count();
        $this->hasMore = ($this->page * $perPage) < $total;
    }

    public function markRead(string $id): void
    {
        $n = Auth::user()?->notifications()->find($id);
        $n?->markAsRead();
        $this->refreshCount();
        $this->loadNotifications();
    }

    public function markAllRead(): void
    {
        Auth::user()?->unreadNotifications()->update(['read_at' => now()]);
        $this->refreshCount();
        $this->loadNotifications();
    }

    public function dismiss(string $id): void
    {
        Auth::user()?->notifications()->find($id)?->delete();
        $this->refreshCount();
        $this->loadNotifications();
    }

    private function buildMessage(array $data): string
    {
        // These notifications are rendered live (not stored as text), so we
        // translate via __() at render time and the user's current locale
        // automatically applies. Keys live in lang/<locale>/notification_center.php.
        return match ($data['type'] ?? '') {
            'lead_received'           => __('notification_center.lead_received', [
                'lead_name' => $data['lead_name'] ?? '',
            ]),
            'lead_assigned'           => __('notification_center.lead_assigned', [
                'lead_name' => $data['lead_name'] ?? '',
            ]),
            'lead_stage_changed'      => __('notification_center.lead_stage_changed', [
                'to_stage'  => $data['to_stage'] ?? '',
                'lead_name' => $data['lead_name'] ?? '',
            ]),
            'automation_ran'          => __('notification_center.automation_ran', [
                'automation_name' => $data['automation_name'] ?? '',
                'lead_name'       => $data['lead_name'] ?? '',
            ]),
            'integration_sync_failed' => __('notification_center.integration_sync_failed', [
                'integration_name' => $data['integration_name'] ?? '',
                'lead_name'        => $data['lead_name'] ?? '',
            ]),
            'export_ready'            => __('notification_center.export_ready'),
            'team_mentioned'          => __('notification_center.team_mentioned', [
                'mentioned_by' => $data['mentioned_by'] ?? __('notification_center.default'),
            ]),
            default                   => __('notification_center.default'),
        };
    }

    public function render()
    {
        return view('livewire.notification-center');
    }
}
