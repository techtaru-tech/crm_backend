<?php

namespace App\Filament\Pages;

use App\Jobs\SendLeadMessage;
use App\Models\Lead;
use App\Models\LeadMessage;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class Conversations extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('filament/conversations.nav_label');
    }

    public ?int $selectedLeadId = null;
    public string $newMessage = '';
    public string $selectedChannel = 'whatsapp';

    public function getView(): string
    {
        return 'filament.pages.conversations';
    }

    public function mount(): void
    {
        $preselect = request()->query('lead');
        if ($preselect) {
            $this->selectLead((int) $preselect);
        }
    }

    public function getTitle(): string
    {
        return __('filament/conversations.title');
    }

    /**
     * Return leads that have any messages, ordered by last message time desc.
     *
     * @return array<int, array<string,mixed>>
     */
    public function getLeadsListProperty(): array
    {
        $tenantId = \App\Support\TenantContext::currentId();
        if (! $tenantId) {
            return [];
        }

        $sub = DB::table('lead_messages')
            ->select('lead_id', DB::raw('MAX(id) as last_id'), DB::raw('MAX(created_at) as last_created'))
            ->where('tenant_id', $tenantId)
            ->groupBy('lead_id');

        $rows = DB::table('lead_messages as m')
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('m.id', '=', 'latest.last_id');
            })
            ->join('leads', 'leads.id', '=', 'm.lead_id')
            ->where('m.tenant_id', $tenantId)
            ->orderByDesc('m.created_at')
            ->limit(100)
            ->select([
                'm.lead_id',
                'm.channel',
                'm.body',
                'm.direction',
                'm.created_at',
                'leads.first_name',
                'leads.last_name',
                'leads.email',
                'leads.phone',
            ])
            ->get();

        // Single batched unread-count query keyed by lead_id — avoids N+1.
        $leadIds = $rows->pluck('lead_id')->all();
        $unreadByLead = DB::table('lead_messages')
            ->selectRaw('lead_id, COUNT(*) AS c')
            ->where('tenant_id', $tenantId)
            ->whereIn('lead_id', $leadIds)
            ->where('direction', 'inbound')
            ->where('status', '!=', 'read')
            ->groupBy('lead_id')
            ->pluck('c', 'lead_id');

        return $rows->map(function ($row) use ($unreadByLead) {
            $name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
            if ($name === '') {
                // Translator-first fallback for unnamed leads so the
                // sidebar label respects the active locale.  Mirrors
                // the `lead_prefix` key used by the right-panel header
                // in resources/views/filament/pages/conversations.blade.php.
                $name = $row->email ?: $row->phone ?: (__('filament/conversations.lead_prefix') . $row->lead_id);
            }

            return [
                'lead_id'    => $row->lead_id,
                'name'       => $name,
                'channel'    => $row->channel,
                'snippet'    => mb_strimwidth((string) ($row->body ?? ''), 0, 60, '…'),
                'direction'  => $row->direction,
                'unread'     => (int) ($unreadByLead[$row->lead_id] ?? 0),
                'time_ago'   => \Carbon\Carbon::parse($row->created_at)->diffForHumans(null, true),
            ];
        })->toArray();
    }

    /**
     * @return array<int, \App\Models\LeadMessage>
     */
    public function getThreadProperty(): array
    {
        if (! $this->selectedLeadId) {
            return [];
        }

        $tenantId = $this->currentTenantId();
        if ($tenantId === null) {
            return [];
        }

        // Re-validate ownership at render time.  Lead + LeadMessage both
        // carry the BelongsToTenant global scope, but add an explicit
        // `where('tenant_id', ...)` so even if the scope is ever
        // disabled (a future `withoutGlobalScope`, an admin-side bug, a
        // post-impersonation stale Livewire state) the thread refuses
        // to leak across tenants.
        $stillOwned = Lead::where('tenant_id', $tenantId)
            ->whereKey($this->selectedLeadId)
            ->exists();
        if (! $stillOwned) {
            $this->selectedLeadId = null;
            return [];
        }

        return LeadMessage::where('tenant_id', $tenantId)
            ->where('lead_id', $this->selectedLeadId)
            ->orderBy('created_at')
            ->get()
            ->all();
    }

    public function getSelectedLeadProperty(): ?Lead
    {
        if (! $this->selectedLeadId) {
            return null;
        }

        $tenantId = $this->currentTenantId();
        if ($tenantId === null) {
            return null;
        }

        return Lead::where('tenant_id', $tenantId)
            ->whereKey($this->selectedLeadId)
            ->first();
    }

    public function selectLead(int $leadId): void
    {
        $tenantId = $this->currentTenantId();
        if ($tenantId === null) {
            return;
        }

        // Explicit tenant filter on top of the BelongsToTenant global
        // scope.  Belt and braces — same lead lookup the rest of the
        // page uses, so an attacker forcing `?lead=<other-tenant-id>`
        // through mount() lands on the same null result the trait
        // already produces.
        $lead = Lead::where('tenant_id', $tenantId)
            ->whereKey($leadId)
            ->first();
        if (! $lead) {
            return;
        }

        $this->selectedLeadId = $lead->id;

        // Pick default channel from most recent message
        $last = LeadMessage::where('tenant_id', $tenantId)
            ->where('lead_id', $lead->id)
            ->latest('id')
            ->first();
        if ($last) {
            $this->selectedChannel = $last->channel;
        } elseif ($lead->phone) {
            $this->selectedChannel = 'whatsapp';
        } elseif (data_get($lead->custom_fields, 'telegram_chat_id')) {
            $this->selectedChannel = 'telegram';
        }

        // Mark inbound unread messages as read
        LeadMessage::where('tenant_id', $tenantId)
            ->where('lead_id', $lead->id)
            ->where('direction', 'inbound')
            ->where('status', '!=', 'read')
            ->update(['status' => 'read']);
    }

    /**
     * Single source of truth for the tenant id used by every query on
     * this page.  Mirrors the resolution logic in getLeadsListProperty()
     * so the pre-select / select / thread-render paths all see the same
     * tenant context.
     */
    private function currentTenantId(): ?int
    {
        $resolved = \App\Support\TenantContext::currentId();
        return $resolved !== null ? (int) $resolved : null;
    }

    public function sendMessage(): void
    {
        $this->newMessage = trim($this->newMessage);
        if ($this->newMessage === '') {
            Notification::make()->warning()->title(__('filament/conversations.notif_type_message_first'))->send();
            return;
        }
        if (! $this->selectedLeadId) {
            Notification::make()->warning()->title(__('filament/conversations.notif_select_conversation'))->send();
            return;
        }

        // Use the same explicit tenant filter the rest of the page
        // applies (selectLead, getThreadProperty, getSelectedLead).
        // Bare Lead::find relied on the BelongsToTenant global scope
        // alone, which is exactly what the explicit filters elsewhere
        // are belt-and-braces against — keep this sendMessage path
        // consistent so a future withoutGlobalScope can never silently
        // open it across tenants.
        $tenantId = $this->currentTenantId();
        $lead = $tenantId !== null
            ? Lead::where('tenant_id', $tenantId)->whereKey($this->selectedLeadId)->first()
            : null;
        if (! $lead) {
            Notification::make()->danger()->title(__('filament/conversations.notif_lead_not_found'))->send();
            return;
        }

        // Refuse submission when the tenant has the chosen channel disabled.
        // Better a loud UI error than a silent queued→failed job.
        $available = $this->availableChannels;
        if (! isset($available[$this->selectedChannel])) {
            // Translator-first channel label so the toast respects tenant locale.
            // Reuses the existing filament/leads.channel_* keys so brand-correct
            // casing ("WhatsApp" not "Whatsapp") is preserved.  Falls back to a
            // ucfirst() of the raw key when the channel is unknown.
            $chanKey   = 'filament/leads.channel_' . $this->selectedChannel;
            $chanTrans = __($chanKey);
            $chanLabel = is_string($chanTrans) && $chanTrans !== $chanKey
                ? $chanTrans
                : ucfirst($this->selectedChannel);
            Notification::make()
                ->danger()
                ->title(__('filament/conversations.notif_channel_not_enabled', ['channel' => $chanLabel]))
                ->body(__('filament/conversations.notif_channel_not_enabled_body'))
                ->send();
            return;
        }

        // The SendLeadMessage job creates the canonical LeadMessage row once
        // the provider confirms — no optimistic placeholder (it would flash
        // a phantom bubble in the UI before being deleted).  Livewire polling
        // picks up the real row on the next tick.
        SendLeadMessage::dispatch(
            $lead->id,
            $this->selectedChannel,
            $this->newMessage,
            auth()->id()
        );

        $this->newMessage = '';

        Notification::make()->success()->title(__('filament/conversations.notif_message_queued'))->send();
    }

    /**
     * Channels offered in the send dropdown.
     *
     * A channel appears only when BOTH conditions hold:
     *   1. the lead has the right identifier (phone / chat_id), and
     *   2. the workspace has enabled that provider in Messaging Settings.
     *
     * Without the second check the user could click Send and get a
     * silent "queued" notification while the job actually fails — the
     * strict review flagged this as misleading UX.
     */
    public function getAvailableChannelsProperty(): array
    {
        $lead = $this->selectedLead;
        if (! $lead) {
            return [];
        }

        $tenant   = $lead->tenant;
        $settings = (array) ($tenant?->getSetting('messaging') ?? []);

        $channels = [];
        if ($lead->phone) {
            if ($settings['whatsapp_enabled'] ?? false) $channels['whatsapp'] = 'WhatsApp';
            if ($settings['sms_enabled']      ?? false) $channels['sms']      = 'SMS';
            if ($settings['viber_enabled']    ?? false) $channels['viber']    = 'Viber';
        }
        if (data_get($lead->custom_fields, 'telegram_chat_id')
            && ($settings['telegram_enabled'] ?? false)) {
            $channels['telegram'] = 'Telegram';
        }

        return $channels;
    }
}
