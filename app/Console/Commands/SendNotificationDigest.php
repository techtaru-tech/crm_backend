<?php

namespace App\Console\Commands;

use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNotificationDigest extends Command
{
    protected $signature   = 'notifications:digest';
    protected $description = 'Send hourly email digest of pending notifications to users with hourly email frequency.';

    public function handle(): void
    {
        $hourlyPrefs = NotificationPreference::where('channel', 'email')
            ->where('enabled', true)
            ->where('email_frequency', 'hourly')
            ->with('user')
            ->get()
            ->groupBy('user_id');

        $count = 0;

        foreach ($hourlyPrefs as $userId => $prefs) {
            $user = $prefs->first()?->user;
            if (! $user) continue;

            $digestTypes = $prefs->pluck('notification_type')->toArray();

            $pending = NotificationDigest::where('user_id', $userId)
                ->whereNull('sent_at')
                ->whereIn('notification_type', $digestTypes)
                ->orderBy('created_at')
                ->get();

            if ($pending->isEmpty()) continue;

            try {
                $this->sendDigestEmail($user, $pending);

                NotificationDigest::where('user_id', $userId)
                    ->whereIn('id', $pending->pluck('id'))
                    ->update(['sent_at' => now()]);

                $count++;
            } catch (\Throwable $e) {
                logger()->warning('SendNotificationDigest: failed to send digest', [
                    'user_id' => $userId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $this->info("Digest sent for {$count} users.");
    }

    private function sendDigestEmail(User $user, $records): void
    {
        // Fix: scheduled commands run in CLI context — no
        // SetLocale middleware fires — so __() and Carbon::diffForHumans()
        // would otherwise render in the worker's app.locale (=en) regardless
        // of the recipient's selected language. Switch to the user's
        // preferredLocale() for the duration of the email build, then
        // restore so the next user in the loop starts clean.
        $originalLocale = app()->getLocale();
        $userLocale     = $user->preferredLocale();
        if ($userLocale && in_array($userLocale, array_keys(config('locales.supported', ['en' => []])), true)) {
            app()->setLocale($userLocale);
            try { \Carbon\Carbon::setLocale($userLocale); \Carbon\CarbonImmutable::setLocale($userLocale); }
            catch (\Throwable) { /* unsupported Carbon locale — keep previous */ }
        }

        try {
            $items = $records->map(fn($r) => [
                'type'      => $r->notification_type,
                'message'   => $r->data['lead_name'] ?? $r->data['message'] ?? __('mail.digest_fallback_message'),
                'timestamp' => $r->created_at->diffForHumans(),
            ])->values()->toArray();

            $appName = (string) config('leadhub.branding.app_name', 'LeadHub');

            Mail::html($this->buildDigestHtml($user, $items, $appName), function ($message) use ($user, $appName) {
                $message->to($user->email, $user->name)
                    ->subject(__('mail.digest_subject', [
                        'app'      => $appName,
                        'datetime' => now()->translatedFormat('M j, Y H:i'),
                    ]));
            });
        } finally {
            app()->setLocale($originalLocale);
            try { \Carbon\Carbon::setLocale($originalLocale); \Carbon\CarbonImmutable::setLocale($originalLocale); }
            catch (\Throwable) { /* same fallback */ }
        }
    }

    private function buildDigestHtml(User $user, array $items, string $appName): string
    {
        $rows = collect($items)->map(function ($item) {
            // Translator-first lookup; fall back to ucwords English when the
            // notification_types key is missing for this type. Mirrors the
            // Currency::label() pattern used elsewhere in the codebase.
            $typeKey       = 'notification_types.' . $item['type'];
            $translated    = __($typeKey);
            $typeLabel     = is_string($translated) && $translated !== $typeKey
                ? $translated
                : ucwords(str_replace('_', ' ', $item['type']));

            // XSS fix: $item['message'] is populated from
            // NotificationDigest.data which originates from
            // Lead::$first_name / $last_name / $error / etc. — i.e. data
            // a public-form-filling visitor controls.  Without e() the
            // raw HTML payload renders in every digest recipient's
            // inbox. $typeLabel + $timestamp are developer / Carbon-
            // controlled but escaped for belt-and-suspenders so future
            // edits to notification_types lang keys can't regress this.
            $safeLabel     = e($typeLabel);
            $safeMessage   = e($item['message']);
            $safeTimestamp = e($item['timestamp']);

            return "<tr>
                <td style='padding:8px 12px;border-bottom:1px solid #f3f4f6;color:#374151;font-size:14px;'>{$safeLabel}</td>
                <td style='padding:8px 12px;border-bottom:1px solid #f3f4f6;color:#374151;font-size:14px;'>{$safeMessage}</td>
                <td style='padding:8px 12px;border-bottom:1px solid #f3f4f6;color:#9ca3af;font-size:12px;'>{$safeTimestamp}</td>
            </tr>";
        })->implode('');

        $heading            = e(__('mail.digest_heading', ['app' => $appName]));
        $introLede          = e(__('mail.digest_intro_lede', ['name' => $user->name]));
        $colType            = e(__('mail.digest_col_type'));
        $colDetails         = e(__('mail.digest_col_details'));
        $colWhen            = e(__('mail.digest_col_when'));
        $viewButton         = e(__('mail.digest_view_button', ['app' => $appName]));
        $footerExplainer    = e(__('mail.digest_footer_explainer'));
        $managePreferences  = e(__('mail.digest_manage_preferences_link'));

        return "<!DOCTYPE html><html><body style='font-family:sans-serif;background:#f9fafb;padding:20px;'>
<div style='max-width:600px;margin:0 auto;background:white;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;'>
<div style='background:#4f46e5;padding:24px;'>
    <h1 style='color:white;margin:0;font-size:20px;'>{$heading}</h1>
    <p style='color:#c7d2fe;margin:4px 0 0;font-size:14px;'>{$introLede}</p>
</div>
<div style='padding:24px;'>
<table style='width:100%;border-collapse:collapse;'>
<thead><tr>
    <th style='text-align:left;padding:8px 12px;background:#f9fafb;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.05em;'>{$colType}</th>
    <th style='text-align:left;padding:8px 12px;background:#f9fafb;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.05em;'>{$colDetails}</th>
    <th style='text-align:left;padding:8px 12px;background:#f9fafb;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.05em;'>{$colWhen}</th>
</tr></thead>
<tbody>{$rows}</tbody>
</table>
<div style='margin-top:20px;'>
    <a href='" . \App\Support\AdminUrl::for() . "' style='display:inline-block;padding:10px 20px;background:#4f46e5;color:white;text-decoration:none;border-radius:6px;font-size:14px;font-weight:500;'>{$viewButton}</a>
</div>
<p style='margin-top:16px;font-size:12px;color:#9ca3af;'>{$footerExplainer} <a href='" . \App\Support\AdminUrl::for('notification-preferences') . "' style='color:#4f46e5;'>{$managePreferences}</a></p>
</div>
</div>
</body></html>";
    }
}
