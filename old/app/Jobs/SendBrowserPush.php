<?php

namespace App\Jobs;

use App\Models\PushSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBrowserPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $body,
        public readonly ?string $url = null,
    ) {}

    public function handle(): void
    {
        // Try OneSignal first, fall back to native VAPID
        $oneSignalAppId  = config('leadhub.onesignal.app_id');
        $oneSignalApiKey = config('leadhub.onesignal.rest_api_key');

        if ($oneSignalAppId && $oneSignalApiKey) {
            $this->sendViaOneSignal($oneSignalAppId, $oneSignalApiKey);
            return;
        }

        $this->sendViaVapid();
    }

    private function sendViaOneSignal(string $appId, string $apiKey): void
    {
        try {
            // OneSignal's `headings`/`contents` are language-keyed maps.
            // The strings reaching here are ALREADY localised on the
            // dispatch side — every caller passes `__('notifications.*')`
            // resolved under the recipient's locale.  We therefore put
            // the pre-translated text under OneSignal's required `en`
            // default-language key so it's delivered as the fallback
            // (which OneSignal serves to every device unless the same
            // payload supplies a more-specific language key).  This
            // keeps the push body in the recipient's UI language without
            // having to maintain a Carbon-style locale → BCP-47 mapping
            // here for every Filament locale.
            $payload = [
                'app_id'             => $appId,
                'include_aliases'    => ['external_id' => [(string) $this->userId]],
                'target_channel'     => 'push',
                'headings'           => ['en' => $this->title],
                'contents'           => ['en' => $this->body],
            ];

            if ($this->url) {
                $payload['url'] = $this->url;
            }

            $response = Http::withHeaders([
                'Authorization' => "Key {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post('https://api.onesignal.com/notifications', $payload);

            if (! $response->successful()) {
                Log::warning('OneSignal push failed: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('OneSignal push error: ' . $e->getMessage());
        }
    }

    private function sendViaVapid(): void
    {
        $vapidPublicKey  = config('leadhub.vapid.public_key');
        $vapidPrivateKey = config('leadhub.vapid.private_key');
        $vapidSubject    = config('leadhub.vapid.subject', 'mailto:admin@leadhub.app');

        if (! $vapidPublicKey || ! $vapidPrivateKey) {
            Log::info('SendBrowserPush: No push provider configured (set ONESIGNAL_APP_ID or VAPID keys).');
            return;
        }

        $subscriptions = PushSubscription::where('user_id', $this->userId)->get();
        if ($subscriptions->isEmpty()) return;

        try {
            $auth = [
                'VAPID' => [
                    'subject'    => $vapidSubject,
                    'publicKey'  => $vapidPublicKey,
                    'privateKey' => $vapidPrivateKey,
                ],
            ];

            $webPush = new \Minishlink\WebPush\WebPush($auth);

            $payload = json_encode([
                'title' => $this->title,
                'body'  => $this->body,
                'url'   => $this->url,
                'icon'  => '/favicon.ico',
            ]);

            foreach ($subscriptions as $sub) {
                $subscription = \Minishlink\WebPush\Subscription::create($sub->toWebPushSubscription());
                $webPush->queueNotification($subscription, $payload);
            }

            foreach ($webPush->flush() as $report) {
                if (! $report->isSuccess()) {
                    if ($report->isSubscriptionExpired()) {
                        PushSubscription::where('endpoint', $report->getEndpoint())->delete();
                    }
                    Log::warning('Browser push delivery failed: ' . $report->getReason());
                }
            }
        } catch (\Throwable $e) {
            Log::error('VAPID push error: ' . $e->getMessage());
        }
    }
}
