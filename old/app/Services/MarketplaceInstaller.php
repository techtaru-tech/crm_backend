<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationStep;
use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use App\Models\Form;
use App\Models\FormField;
use App\Models\LandingPage;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\SharedTemplate;
use App\Models\SharedTemplateInstall;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\MarketplaceTemplateInstalledNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

/**
 * Clone a SharedTemplate's payload into a target tenant.
 *
 * Each shared template carries a JSON snapshot of an entity's full
 * structure (including child records).  This service replays that
 * snapshot into the buyer's tenant — atomic per install, no partial
 * states.
 *
 * Supported types map to per-type installer methods.  Unknown types
 * surface a clear error so the operator knows the marketplace served
 * an unsupported payload.
 *
 * Side effects:
 *   - Increments shared_templates.downloads counter
 *   - Returns {ok, created, type, ids, error} so the caller can deep-
 *     link the buyer to the new entity
 */
class MarketplaceInstaller
{
    /**
     * Suffix appended to every cloned marketplace entity's name.
     *
     * Kept as the canonical English fallback so countInstallsThisMonthLegacy()
     * has a stable LIKE-needle on partial-migrate environments where the
     * audit table is missing.  UI rendering of newly-cloned entity names
     * MUST route through self::installSuffix() so buyer-facing labels
     * follow the active locale.
     *
     * @deprecated as of round-3 fix #D2 — no longer authoritative for
     *             the monthly install limit. The shared_template_installs
     *             audit table is the source of truth; the suffix is kept
     *             only as a legacy fallback for partial-migrate envs.
     */
    public const INSTALL_NAME_SUFFIX = '(from marketplace)';

    /**
     * Translator-resolved suffix appended to cloned entity names.  Falls
     * back to the canonical const value so partial-migrate environments
     * that boot before lang files are loaded continue to write the legacy
     * English suffix (which countInstallsThisMonthLegacy() can then match).
     */
    public static function installSuffix(): string
    {
        $key        = 'services/marketplace.install_suffix';
        $translated = __($key);

        if (is_string($translated) && $translated !== $key) {
            return $translated;
        }

        return self::INSTALL_NAME_SUFFIX;
    }

    /**
     * @return array{ok:bool, created:int, type:string, ids:array<int>, error:?string}
     */
    public function install(SharedTemplate $template, Tenant $tenant): array
    {
        if (! $template->is_public) {
            return $this->fail($template, __('services/marketplace.template_not_published'));
        }

        $planService = app(PlanService::class);

        if (! $planService->hasFeature($tenant, 'marketplace_install')) {
            return $this->fail(
                $template,
                __('services/marketplace.plan_not_included')
            );
        }

        $monthlyLimit = $planService->getLimit($tenant, 'marketplace_installs_per_month');
        if ($monthlyLimit !== -1) {
            $used = $this->countInstallsThisMonth($tenant);
            if ($monthlyLimit === 0 || $used >= $monthlyLimit) {
                return $this->fail(
                    $template,
                    __('services/marketplace.monthly_limit_reached', [
                        'used'  => $used,
                        'limit' => $monthlyLimit,
                    ])
                );
            }
        }

        try {
            return DB::transaction(function () use ($template, $tenant) {
                $result = match ($template->type) {
                    SharedTemplate::TYPE_AUTOMATION     => $this->installAutomation($template, $tenant),
                    SharedTemplate::TYPE_EMAIL_SEQUENCE => $this->installEmailSequence($template, $tenant),
                    SharedTemplate::TYPE_FORM           => $this->installForm($template, $tenant),
                    SharedTemplate::TYPE_PIPELINE       => $this->installPipeline($template, $tenant),
                    SharedTemplate::TYPE_LANDING_PAGE   => $this->installLandingPage($template, $tenant),
                    default                             => $this->fail($template, __('services/marketplace.unsupported_template_type', ['type' => $template->type])),
                };

                if (($result['ok'] ?? false) === true) {
                    $template->increment('downloads');
                    $this->recordInstall($template, $tenant, $result);
                    $this->notifyOwner($template, $tenant);
                }

                return $result;
            });
        } catch (\Throwable $e) {
            Log::error('MarketplaceInstaller failed', [
                'template_id' => $template->id,
                'tenant_id'   => $tenant->id,
                'error'       => $e->getMessage(),
            ]);
            return $this->fail($template, __('services/marketplace.install_failed', ['error' => $e->getMessage()]));
        }
    }

    protected function installAutomation(SharedTemplate $template, Tenant $tenant): array
    {
        $payload = (array) ($template->payload ?? []);
        $automation = Automation::create([
            'tenant_id'    => $tenant->id,
            'name'         => ($payload['name'] ?? $template->name) . ' ' . self::installSuffix(),
            'description'  => $payload['description'] ?? $template->description,
            'trigger_type' => $this->resolveTriggerType($payload),
            'enabled'      => false,
        ]);

        $stepIds = [];
        foreach ((array) ($payload['steps'] ?? []) as $idx => $step) {
            $row = AutomationStep::create([
                'tenant_id'    => $tenant->id,
                'automation_id' => $automation->id,
                'sort_order'   => $idx + 1,
                'type'         => $step['type'] ?? 'send_email',
                'config'       => $step['config'] ?? [],
            ]);
            $stepIds[] = $row->id;
        }

        return $this->ok($template, count($stepIds) + 1, [$automation->id]);
    }

    /**
     * Resolve a valid automations.trigger_type from a shared-template
     * payload.  Snapshots historically stored the trigger under the legacy
     * 'trigger' key in dot-form (e.g. "lead.created"); the column and the
     * AutomationDispatcher use the underscore keys in Automation::TRIGGERS.
     * Normalise dots->underscores and fall back to 'manual' (always valid +
     * non-firing) so an install can never write a NULL/invalid trigger_type.
     */
    private function resolveTriggerType(array $payload): string
    {
        $raw = (string) ($payload['trigger_type'] ?? $payload['trigger'] ?? 'manual');
        $key = str_replace('.', '_', trim($raw));

        return array_key_exists($key, Automation::TRIGGERS) ? $key : 'manual';
    }

    protected function installEmailSequence(SharedTemplate $template, Tenant $tenant): array
    {
        $payload = (array) ($template->payload ?? []);
        $sequence = EmailSequence::create([
            'tenant_id'     => $tenant->id,
            'name'          => ($payload['name'] ?? $template->name) . ' ' . self::installSuffix(),
            'description'   => $payload['description'] ?? $template->description,
            'status'        => 'draft',
            'stop_on_reply' => (bool) ($payload['stop_on_reply'] ?? true),
            'stop_on_won'   => (bool) ($payload['stop_on_won']   ?? true),
        ]);

        foreach ((array) ($payload['steps'] ?? []) as $idx => $step) {
            EmailSequenceStep::create([
                'sequence_id' => $sequence->id,
                'tenant_id'   => $tenant->id,
                'sort_order'  => $idx + 1,
                'delay_days'  => (int) ($step['delay_days'] ?? 0),
                'subject'     => $step['subject']   ?? '',
                'body_html'   => $step['body_html'] ?? '',
            ]);
        }

        return $this->ok($template, 1, [$sequence->id]);
    }

    protected function installForm(SharedTemplate $template, Tenant $tenant): array
    {
        $payload = (array) ($template->payload ?? []);
        $form = Form::create([
            'tenant_id'         => $tenant->id,
            'name'              => ($payload['name'] ?? $template->name) . ' ' . self::installSuffix(),
            'slug'              => 'imported-' . substr(md5((string) $template->id . microtime()), 0, 8),
            'description'       => $payload['description'] ?? $template->description,
            'thank_you_message' => $payload['thank_you_message'] ?? $payload['success_message'] ?? (string) __('services/marketplace.form_default_success_message'),
            'active'            => false,
        ]);

        foreach ((array) ($payload['fields'] ?? []) as $idx => $field) {
            FormField::create([
                'form_id'    => $form->id,
                'sort_order' => $idx + 1,
                'field_key'  => $field['field_key'] ?? $field['name'] ?? "field_{$idx}",
                'label'      => $field['label']     ?? '',
                'type'       => $field['type']      ?? 'text',
                'required'   => (bool) ($field['required'] ?? false),
                'options'    => $field['options']   ?? null,
            ]);
        }

        return $this->ok($template, 1, [$form->id]);
    }

    protected function installPipeline(SharedTemplate $template, Tenant $tenant): array
    {
        $payload = (array) ($template->payload ?? []);
        $pipeline = Pipeline::create([
            'tenant_id'   => $tenant->id,
            'name'        => ($payload['name'] ?? $template->name) . ' ' . self::installSuffix(),
            'description' => $payload['description'] ?? $template->description,
            'is_default'  => false,
        ]);

        foreach ((array) ($payload['stages'] ?? []) as $idx => $stage) {
            PipelineStage::create([
                'tenant_id'       => $tenant->id,
                'pipeline_id'     => $pipeline->id,
                'sort_order'      => $idx + 1,
                'name'            => $stage['name']            ?? (string) __('services/marketplace.pipeline_stage_default_name', ['index' => $idx]),
                'color'           => $stage['color']           ?? '#94a3b8',
                'win_probability' => $stage['win_probability'] ?? 0,
                'is_won'          => (bool) ($stage['is_won']  ?? false),
                'is_lost'         => (bool) ($stage['is_lost'] ?? false),
            ]);
        }

        return $this->ok($template, 1, [$pipeline->id]);
    }

    protected function installLandingPage(SharedTemplate $template, Tenant $tenant): array
    {
        $payload = (array) ($template->payload ?? []);
        $page = LandingPage::create([
            'tenant_id' => $tenant->id,
            'name'      => ($payload['name'] ?? $template->name) . ' ' . self::installSuffix(),
            'slug'      => 'imported-' . substr(md5((string) $template->id . microtime()), 0, 8),
            'status'    => 'draft',
        ]);

        return $this->ok($template, 1, [$page->id]);
    }

    protected function ok(SharedTemplate $template, int $created, array $ids): array
    {
        return [
            'ok'      => true,
            'created' => $created,
            'type'    => $template->type,
            'ids'     => $ids,
            'error'   => null,
        ];
    }

    protected function fail(SharedTemplate $template, string $error): array
    {
        return [
            'ok'      => false,
            'created' => 0,
            'type'    => $template->type,
            'ids'     => [],
            'error'   => $error,
        ];
    }

    /**
     * Notify the contributor that their template was installed by
     * another tenant.  Skips:
     *   - Self-installs (owner tenant installing their own template)
     *   - Templates with no owner_tenant_id (legacy / SA-seeded)
     *   - Notification failures (best-effort, logged not thrown)
     *
     * Targets the owner tenant's admin users via Spatie role lookup
     * with a "tenant owner" fallback so single-user installs still
     * get notified.
     */
    /**
     * Authoritative count of marketplace installs for this tenant in
     * the current calendar month — backed by the shared_template_installs
     * audit table written by recordInstall().
     *
     * Falls back to the legacy INSTALL_NAME_SUFFIX scan only when the
     * audit table is missing (partial-migrate environments) so the
     * service does not blow up before the migration runs.
     */
    protected function countInstallsThisMonth(Tenant $tenant): int
    {
        $start = Carbon::now()->startOfMonth();

        if (Schema::hasTable('shared_template_installs')) {
            try {
                return (int) SharedTemplateInstall::query()
                    ->withoutGlobalScopes()
                    ->where('tenant_id', $tenant->id)
                    ->where('installed_at', '>=', $start)
                    ->count();
            } catch (\Throwable $e) {
                Log::warning('MarketplaceInstaller: install count query failed', [
                    'tenant_id' => $tenant->id,
                    'error'     => $e->getMessage(),
                ]);
                // Fall through to the legacy fallback so the user
                // never sees a 500 from a counter glitch.
            }
        }

        return $this->countInstallsThisMonthLegacy($tenant, $start);
    }

    /**
     * @deprecated Kept for partial-migrate environments only.
     *
     * Counts cloned rows by INSTALL_NAME_SUFFIX across the cloned-entity
     * tables. Brittle by design — a renamed entity escapes the count and
     * a coincidentally-named entity inflates it. Replace by running the
     * 2026_04_29_000010 migration which creates shared_template_installs.
     */
    protected function countInstallsThisMonthLegacy(Tenant $tenant, Carbon $start): int
    {
        $needle = '%' . self::INSTALL_NAME_SUFFIX . '%';

        $countOn = function (string $modelClass, string $column = 'name') use ($tenant, $start, $needle): int {
            if (! class_exists($modelClass)) {
                return 0;
            }
            try {
                /** @var class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
                return (int) $modelClass::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('created_at', '>=', $start)
                    ->where($column, 'like', $needle)
                    ->count();
            } catch (\Throwable) {
                return 0;
            }
        };

        return $countOn(Automation::class)
            + $countOn(EmailSequence::class)
            + $countOn(Form::class)
            + $countOn(Pipeline::class)
            + $countOn(LandingPage::class);
    }

    /**
     * Persist the audit row that countInstallsThisMonth() reads from.
     * Best-effort: a write failure must not roll back the install
     * itself — the user already has the cloned entity in their tenant
     * and a missing audit row only affects the next cycle's counter.
     */
    protected function recordInstall(SharedTemplate $template, Tenant $tenant, array $result): void
    {
        if (! Schema::hasTable('shared_template_installs')) {
            return;
        }

        try {
            $entityId = (int) ($result['ids'][0] ?? 0);
            SharedTemplateInstall::create([
                'tenant_id'    => $tenant->id,
                'template_id'  => $template->id,
                'entity_type'  => (string) $template->type,
                'entity_id'    => $entityId > 0 ? $entityId : null,
                'installed_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('MarketplaceInstaller: recordInstall write failed', [
                'tenant_id'   => $tenant->id,
                'template_id' => $template->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    protected function notifyOwner(SharedTemplate $template, Tenant $installerTenant): void
    {
        $ownerTenantId = (int) ($template->owner_tenant_id ?? 0);
        if ($ownerTenantId === 0 || $ownerTenantId === (int) $installerTenant->id) {
            return;
        }

        try {
            $admins = User::query()
                ->where('tenant_id', $ownerTenantId)
                ->where(function ($q) use ($ownerTenantId) {
                    $q->whereHas('roles', fn ($r) => $r->where('name', 'admin'));
                })
                ->limit(20)
                ->get();

            // Fallback: notify the tenant's owner_id if no admins are
            // resolvable via the role (orphaned single-user installs).
            if ($admins->isEmpty()) {
                $owner = Tenant::query()->find($ownerTenantId);
                $ownerUserId = (int) ($owner->owner_id ?? 0);
                if ($ownerUserId > 0) {
                    $admins = User::query()->whereKey($ownerUserId)->get();
                }
            }

            if ($admins->isEmpty()) {
                return;
            }

            Notification::send($admins, new MarketplaceTemplateInstalledNotification(
                templateId:           (int) $template->id,
                templateName:         (string) $template->name,
                templateType:         (string) $template->type,
                installerTenantId:    (int) $installerTenant->id,
                installerTenantName:  (string) ($installerTenant->name ?? __('services/marketplace.installer_tenant_name_fallback')),
            ));
        } catch (\Throwable $e) {
            Log::warning('MarketplaceInstaller: notifyOwner failed', [
                'template_id' => $template->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
