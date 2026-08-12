<?php

namespace App\Console\Commands;

use App\Settings\UpdateSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckForUpdates extends Command
{
    protected $signature   = 'leadhub:check-updates';
    protected $description = 'Check the update server for a newer version of LeadHub';

    public function handle(): int
    {
        $serverUrl = config('leadhub.updates.server_url');
        $current   = config('leadhub.version', '1.0.0');

        if (empty($serverUrl)) {
            $this->warn(__('console.check_updates.no_server_configured'));
            return self::SUCCESS;
        }

        try {
            $response = Http::timeout(15)->get($serverUrl, [
                'product' => 'leadhub',
                'version' => $current,
                'domain'  => parse_url(config('app.url'), PHP_URL_HOST),
            ]);

            if ($response->failed()) {
                $this->error(__('console.check_updates.server_error', ['status' => $response->status()]));
                return self::FAILURE;
            }

            $data = $response->json();

            $latest       = $data['latest_version'] ?? $current;
            $changelogUrl = $data['changelog_url'] ?? null;
            $checkedAt    = now()->toDateTimeString();

            Cache::put('leadhub.update_info', [
                'latest_version' => $latest,
                'changelog_url'  => $changelogUrl,
                'checked_at'     => $checkedAt,
            ], now()->addHours(24));

            try {
                $updateSettings                   = app(UpdateSettings::class);
                $updateSettings->latest_version   = $latest;
                $updateSettings->changelog_url    = $changelogUrl;
                $updateSettings->last_checked_at  = $checkedAt;
                $updateSettings->update_available = version_compare($latest, $current, '>');
                $updateSettings->save();
            } catch (\Throwable) {
            }

            if (version_compare($latest, $current, '>')) {
                $this->info(__('console.check_updates.update_available', ['current' => $current, 'latest' => $latest]));
            } else {
                $this->info(__('console.check_updates.up_to_date', ['current' => $current]));
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error(__('console.check_updates.check_failed', ['message' => $e->getMessage()]));
            return self::FAILURE;
        }
    }
}
