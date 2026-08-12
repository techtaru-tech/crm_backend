<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class LiveLeadFeed extends Widget
{
    protected string $view = 'filament.widgets.live-lead-feed';
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';

    public array $leads = [];

    public function mount(): void
    {
        $tenantId = Auth::user()?->tenant_id;
        $this->leads = Lead::where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function (Lead $l) {
                // Source/status are stored as canonical slug codes
                // ('manual', 'new', etc.) but the live-feed widget
                // renders the resolved string directly in <td x-text>.
                // Resolve to the localized label here so non-English
                // tenants don't see the raw English slug in their
                // dashboard.  Translator-first pattern matches
                // KanbanBoard.php; ucfirst() is a last-resort cosmetic
                // fallback for custom buyer-defined statuses that
                // never made it into the lang files.
                $sourceKey   = (string) ($l->source ?? 'manual');
                $sourceLang  = 'lead_sources.' . $sourceKey;
                $sourceLabel = __($sourceLang);
                if ($sourceLabel === $sourceLang) {
                    $sourceLabel = ucfirst(str_replace('_', ' ', $sourceKey));
                }

                // Lead.status is cast to the App\Enums\LeadStatus backed
                // enum (see Lead::$casts).  A raw (string) cast on an
                // enum object throws "Object of class ... could not be
                // converted to string", so resolve the canonical slug
                // via ->value when the cast actually fired, and fall
                // back to a plain string for legacy rows that somehow
                // skipped the cast.
                $rawStatus = $l->status;
                $statusKey = $rawStatus instanceof \BackedEnum
                    ? (string) $rawStatus->value
                    : (string) ($rawStatus ?? 'new');
                $statusLang  = 'enums/lead_status.' . $statusKey;
                $statusLabel = __($statusLang);
                if ($statusLabel === $statusLang) {
                    $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
                }

                return [
                    'id'         => $l->id,
                    'name'       => trim("{$l->first_name} {$l->last_name}"),
                    'email'      => $l->email,
                    'source'     => (string) $sourceLabel,
                    'status'     => (string) $statusLabel,
                    // status_key is kept separate so the view can keep
                    // applying its statusClass(lead.status_key) colour-coding
                    // off the canonical slug instead of the localized
                    // label (which won't match the JS dictionary).
                    'status_key' => $statusKey,
                    'created_at' => $l->created_at?->diffForHumans(),
                ];
            })
            ->toArray();
    }
}
