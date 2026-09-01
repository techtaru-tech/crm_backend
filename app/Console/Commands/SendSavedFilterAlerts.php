<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\SavedFilter;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSavedFilterAlerts extends Command
{
    protected $signature   = 'filters:send-alerts';
    protected $description = 'Send email alerts for saved filters with new matching leads';

    public function handle(): int
    {
        $filters = SavedFilter::where('email_alerts', true)->with('user')->get();

        foreach ($filters as $filter) {
            $user = $filter->user;
            if (! $user) {
                continue;
            }

            $since = $filter->last_alert_at ?: $filter->created_at;

            try {
                $leads = $this->queryLeads($user, $filter, $since);
            } catch (\Throwable $e) {
                Log::warning("Saved filter alert #{$filter->id} query failed: {$e->getMessage()}");
                continue;
            }

            $count = $leads->count();
            if ($count === 0) {
                $filter->update(['last_alert_at' => now()]);
                continue;
            }

            $this->sendAlertEmail($user, $filter, $leads, $count);
            $filter->update(['last_alert_at' => now()]);

            $this->info("Sent alert for filter '{$filter->name}' to {$user->email} ({$count} leads).");
        }

        return self::SUCCESS;
    }

    /**
     * Rebuild a minimal filter query for the Lead resource.
     * We only attempt Lead filters — other resources silently skip.
     */
    private function queryLeads(User $user, SavedFilter $filter, $since)
    {
        if ($filter->resource !== \App\Filament\Resources\LeadResource::class) {
            return collect();
        }

        $query = Lead::where('tenant_id', $user->tenant_id)
            ->where('created_at', '>=', $since);

        $f = is_array($filter->filters) ? $filter->filters : [];

        if (! empty($f['source']['value']))            $query->where('source', $f['source']['value']);
        if (! empty($f['status']['value']))            $query->where('status', $f['status']['value']);
        if (! empty($f['assigned_user_id']['value']))  $query->where('assigned_user_id', $f['assigned_user_id']['value']);
        if (! empty($f['pipeline_id']['value']))       $query->where('pipeline_id', $f['pipeline_id']['value']);
        if (! empty($f['pipeline_stage_id']['value'])) $query->where('pipeline_stage_id', $f['pipeline_stage_id']['value']);
        if (! empty($f['is_starred']['isActive']))     $query->where('is_starred', true);
        if (! empty($f['is_duplicate']['isActive']))   $query->where('is_duplicate', true);
        if (! empty($f['score_min']['score_min']))     $query->where('lead_score', '>=', $f['score_min']['score_min']);

        return $query->orderByDesc('created_at')->limit(100)->get();
    }

    private function sendAlertEmail(User $user, SavedFilter $filter, $leads, int $count): void
    {
        // Fix: scheduled commands run in CLI context — no
        // SetLocale middleware fires — so __() would render in the worker's
        // app.locale (=en) regardless of the recipient's selected language.
        // Switch to the user's preferredLocale() for the duration of the
        // email build, then restore so the next user in the loop is clean.
        $originalLocale = app()->getLocale();
        $userLocale     = $user->preferredLocale();
        if ($userLocale && in_array($userLocale, array_keys(config('locales.supported', ['en' => []])), true)) {
            app()->setLocale($userLocale);
            try { \Carbon\Carbon::setLocale($userLocale); \Carbon\CarbonImmutable::setLocale($userLocale); }
            catch (\Throwable) { /* unsupported Carbon locale — keep previous */ }
        }

        try {
            $intro = __('saved_filter_alerts.body_intro', [
                'name'  => $filter->name,
                'count' => $count,
            ]);

            $rows = $leads->map(fn ($l) => __('saved_filter_alerts.body_lead', [
                'full_name' => $l->full_name,
                'email'     => $l->email,
                // H7: status is a LeadStatus enum cast — the translator
                // str_replace()s the value into the line, which fatals on
                // an object.  label() is already locale-correct here: the
                // caller switched app locale to the recipient's above.
                'status'    => $l->status?->label() ?? '',
            ]))->implode("\n");

            $view = __('saved_filter_alerts.body_view', [
                'url' => rtrim(config('app.url'), '/') . '/admin/leads',
            ]);

            $body = $intro . "\n\n" . $rows . "\n\n" . $view;

            $subject = __('saved_filter_alerts.subject', [
                'count' => $count,
                'name'  => $filter->name,
            ]);

            Mail::raw(
                $body,
                function ($m) use ($user, $subject) {
                    $m->to($user->email)
                      ->subject($subject);
                }
            );
        } catch (\Throwable $e) {
            Log::warning("Saved filter alert email to {$user->email} failed: {$e->getMessage()}");
        } finally {
            app()->setLocale($originalLocale);
            try { \Carbon\Carbon::setLocale($originalLocale); \Carbon\CarbonImmutable::setLocale($originalLocale); }
            catch (\Throwable) { /* same fallback */ }
        }
    }
}
