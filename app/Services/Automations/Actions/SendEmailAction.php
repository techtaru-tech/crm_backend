<?php

namespace App\Services\Automations\Actions;

use App\Models\AutomationRun;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Services\TenantSmtpManager;
use Illuminate\Support\Facades\Mail;

class SendEmailAction
{
    public function execute(Lead $lead, array $config, AutomationRun $run): bool
    {
        $templateId = $config['email_template_id'] ?? null;
        if (! $templateId || ! $lead->email) return false;

        $template = EmailTemplate::find($templateId);
        if (! $template) return false;

        $rendered = $template->render($lead);

        // CRITICAL: automations run in queue workers where there is no
        // request context, so InjectTenantBranding middleware never fires
        // and Laravel uses the *application-default* mailer by default.
        // Apply the tenant's SMTP override explicitly here so the
        // workspace's "override" actually overrides for automations too.
        if ($lead->tenant) {
            app(TenantSmtpManager::class)->applyForTenant($lead->tenant);
        }

        // Wrap body in tenant-branded template (gradient header, logo,
        // footer) so automation emails match the look-and-feel of
        // every other outbound email from this workspace.  Token
        // interpolation on the body happened inside EmailTemplate::render
        // already — this step is purely styling.
        $brandedBody = app(\App\Services\BrandedEmailRenderer::class)
            ->wrap($lead->tenant, $rendered['body_html'], $rendered['subject']);

        // Up to 3 attempts with exponential backoff (1s → 4s → 9s) to
        // tolerate transient SMTP flakiness without drowning the queue.
        $lastError = null;
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                Mail::html($brandedBody, function ($message) use ($lead, $rendered) {
                    $message->to($lead->email, $lead->full_name)
                        ->subject($rendered['subject']);
                });
                return true;
            } catch (\Throwable $e) {
                $lastError = $e;
                if ($attempt < 3) {
                    usleep(($attempt * $attempt) * 1_000_000); // 1s, 4s
                    continue;
                }
            }
        }

        // Persistent failure — log + record on the AutomationRun so
        // tenant admins can see the failure in the Automations > Runs
        // view (was previously invisible: warning log only, no UI hint).
        logger()->warning('SendEmailAction failed after retries', [
            'lead_id'     => $lead->id,
            'template_id' => $templateId,
            'run_id'      => $run->id,
            'error'       => $lastError?->getMessage(),
        ]);

        try {
            $log = (array) ($run->log ?? []);
            $log[] = [
                'at'    => now()->toIso8601String(),
                'level' => 'error',
                'msg'   => __('automation_runs.log.email_send_failed') . \Illuminate\Support\Str::limit(
                    $lastError?->getMessage() ?? __('automation_runs.log.unknown_error'), 300
                ),
                'step'  => 'send_email',
            ];
            $run->forceFill([
                'status' => 'failed',
                'log'    => $log,
            ])->save();
        } catch (\Throwable) {
            // Don't cascade AutomationRun write failures.
        }

        return false;
    }
}
