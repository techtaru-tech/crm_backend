<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * SaaS-side onboarding drip — sent on day 1/3/5/7 of a fresh trial.
 *
 * Industry data: a well-built onboarding sequence improves
 * trial→paid conversion 25-40%.  The cadence here matches what
 * Hubspot, Pipedrive, and Highlevel use:
 *
 *   Day 1: Welcome + one specific first-action ask (add a lead)
 *   Day 3: "How are you finding it so far? Common stuck-spots are..."
 *   Day 5: Feature-discovery — automations/forms tour
 *   Day 7: Social proof + soft conversion ask
 *
 * Branded via BrandedMailable (inherits all colors / logo / footer
 * config the operator has set).  ProcessSubscriptionLifecycle
 * decides WHEN to send; this class just renders the right copy.
 *
 * NOT queued explicitly — the lifecycle cron is itself the queue,
 * and we want the same sync-send semantics as TrialEndingSoonMail
 * so shared-hosting installs without a queue worker still work.
 */
class OnboardingDripMail extends BrandedMailable
{
    use Queueable, SerializesModels;

    public string $workspaceName;
    public string $ownerName;
    public int $day;
    public string $appUrl;

    public function __construct(Tenant $tenant, int $day)
    {
        $this->withTenant($tenant);
        $this->workspaceName = $tenant->name;
        $this->ownerName     = $tenant->owner?->name ?? __('emails/onboarding_drip.greeting_fallback_name');
        $this->day           = $day;
        $this->appUrl        = \App\Support\AdminUrl::for();
    }

    public function envelope(): Envelope
    {
        // Subjects are translated so non-English buyers / tenants get
        // localised onboarding.  Falls back to the English string if
        // the active locale doesn't have a translation for the key.
        $key = "messages.onboarding.subjects.day_{$this->day}";
        $translated = __($key, ['workspace' => $this->workspaceName]);

        // __() returns the dotted key string when no translation
        // exists — detect that and use a sensible fallback.
        if ($translated === $key) {
            $translated = __('messages.onboarding.subjects.fallback');
            if ($translated === 'messages.onboarding.subjects.fallback') {
                $translated = __('emails/onboarding_drip.fallback_subject');
            }
        }

        return new Envelope(subject: $translated);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding-drip',
            with: [
                'day'           => $this->day,
                'workspaceName' => $this->workspaceName,
                'ownerName'     => $this->ownerName,
                'appUrl'        => $this->appUrl,
                'copy'          => $this->copyForDay($this->day),
            ],
        );
    }

    /**
     * Per-day copy — kept here so the blade stays a thin renderer.
     *
     * Heading / body / CTA strings are translated via lang/en/mail.php
     * (`drip_day_<n>_*` keys) so non-English buyers/tenants get
     * localised onboarding.  CTA URLs stay code-side because they
     * embed the runtime admin URL.
     */
    protected function copyForDay(int $day): array
    {
        return match ($day) {
            1 => [
                'heading' => __('mail.drip_day_1_heading'),
                'body'    => __('mail.drip_day_1_body'),
                'cta'     => __('mail.drip_day_1_cta'),
                'cta_url' => $this->appUrl . '/leads/create',
            ],
            3 => [
                'heading' => __('mail.drip_day_3_heading'),
                'body'    => __('mail.drip_day_3_body'),
                'cta'     => __('mail.drip_day_3_cta'),
                'cta_url' => $this->appUrl,
            ],
            5 => [
                'heading' => __('mail.drip_day_5_heading'),
                'body'    => __('mail.drip_day_5_body'),
                'cta'     => __('mail.drip_day_5_cta'),
                'cta_url' => $this->appUrl . '/automations',
            ],
            7 => [
                'heading' => __('mail.drip_day_7_heading'),
                'body'    => __('mail.drip_day_7_body'),
                'cta'     => __('mail.drip_day_7_cta'),
                'cta_url' => $this->appUrl . '/billing',
            ],
            default => [
                'heading' => __('mail.drip_default_heading'),
                'body'    => __('mail.drip_default_body'),
                'cta'     => __('mail.drip_default_cta'),
                'cta_url' => $this->appUrl,
            ],
        };
    }
}
