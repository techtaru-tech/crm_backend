<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\LeadEnrichmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    // $tenantId is read by AppServiceProvider's Queue::before hook
    // (reflection on the resolved command) to bind `current_tenant`
    // for the duration of the worker run, so BelongsToTenant queries
    // inside the enrichment service don't fail closed.
    public function __construct(public readonly Lead $lead, public readonly ?int $tenantId = null)
    {
        $this->onQueue('default');
    }

    public function handle(LeadEnrichmentService $service): void
    {
        if ($this->lead->enriched_at) {
            return;
        }

        // i18n: queue workers boot with the app's default locale
        // not the tenant's preferred locale, so wrap the OpenAI enrichment
        // call in a temporary locale switch.  The tenant's default_locale
        // drives the prompt's :locale instruction so OpenAI returns
        // industry/job_title free-text fields in the workspace language.
        $tenant         = $this->lead->tenant;
        $tenantLocale   = (string) ($tenant?->default_locale ?? config('app.locale'));
        $originalLocale = app()->getLocale();

        try {
            app()->setLocale($tenantLocale);
            $success = $service->enrich($this->lead);
        } finally {
            app()->setLocale($originalLocale);
        }

        if (!$success) {
            Log::info('EnrichLead: enrichment returned no data', ['lead_id' => $this->lead->id]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('EnrichLead job failed', [
            'lead_id' => $this->lead->id,
            'error'   => $exception->getMessage(),
        ]);
    }
}
