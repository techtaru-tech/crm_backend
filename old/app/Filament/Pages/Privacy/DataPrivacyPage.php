<?php

declare(strict_types=1);

namespace App\Filament\Pages\Privacy;

use App\Jobs\ExportTenantDataJob;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantErasureService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/**
 * Tenant-side GDPR privacy controls at /admin/privacy.
 *
 * Two flows:
 *
 *   1. Right of access — "Export my data" button dispatches the
 *      ExportTenantData job, which builds a ZIP of every record we
 *      hold and stamps tenants.data_export_*.  The page polls
 *      status; when 'ready', exposes a download link valid for 48h.
 *
 *   2. Right to erasure — "Delete my workspace" confirmation flow.
 *      Sets tenants.deletion_scheduled_at = now+30d.  A daily cron
 *      (ProcessTenantDeletions) sweeps the tenants table and soft-
 *      deletes anyone past their scheduled date.  The 30-day window
 *      is a safety net — the operator can restore via SoftDeletes
 *      if the user changes their mind.  Cancelling deletion clears
 *      both timestamps.
 *
 * Owner-only: staff members can't request export or delete the
 * workspace they don't own.
 */
class DataPrivacyPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|UnitEnum|null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 70;
    protected static ?string $slug = 'privacy';
    protected string $view = 'filament.pages.privacy.data-privacy';

    public static function getNavigationLabel(): string
    {
        return __('filament/data_privacy_page.nav_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/data_privacy_page.title');
    }

    public function getHeading(): string
    {
        return __('filament/data_privacy_page.heading');
    }

    public function getSubheading(): ?string
    {
        return __('filament/data_privacy_page.subheading');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if ($user->is_super_admin ?? false) return true;
        $tenant = $user->tenant ?? null;
        if (! $tenant) return false;
        if ((int) $tenant->owner_id === (int) $user->id) return true;
        return method_exists($user, 'hasRole') && $user->hasRole('admin');
    }

    /*
    |--------------------------------------------------------------------------
    | View data — the blade reads this snapshot
    |--------------------------------------------------------------------------
    */

    public function getViewData(): array
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            return ['tenant' => null];
        }

        $expiresAt = $tenant->data_export_expires_at;
        $exportReady = $tenant->data_export_status === 'ready'
            && $expiresAt
            && $expiresAt->isFuture()
            && ! empty($tenant->data_export_path)
            && Storage::disk('local')->exists($tenant->data_export_path);

        $user = auth()->user();

        return [
            'tenant'              => $tenant,
            'export_status'       => $tenant->data_export_status,
            'export_ready'        => $exportReady,
            'export_size_human'   => $tenant->data_export_size_bytes
                ? $this->humanBytes((int) $tenant->data_export_size_bytes)
                : null,
            'export_expires_at'   => $expiresAt,
            'deletion_pending'    => $tenant->deletion_scheduled_at !== null,
            'deletion_scheduled'  => $tenant->deletion_scheduled_at,
            'deletion_days_left'  => $tenant->deletion_scheduled_at
                ? max(0, (int) Carbon::now()->diffInDays($tenant->deletion_scheduled_at, false))
                : null,
            // Surfaces ownership to the blade so we can hide the
            // destructive "Schedule deletion" button from non-owner
            // admins.  canAccess() lets admin-role users open the page
            // (so they can still trigger the data export — that is a
            // staff-appropriate operation) but they shouldn't see a
            // button that requestDeletion() will refuse to honour
            // anyway.  Super admins are treated as owner for this
            // check so they retain operational override.
            'is_owner'            => $user !== null
                && (
                    (int) ($tenant->owner_id ?? 0) === (int) $user->id
                    || (bool) ($user->is_super_admin ?? false)
                ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Actions — wired from blade buttons
    |--------------------------------------------------------------------------
    */

    public function requestExport(): void
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/data_privacy_page.no_workspace_context'))->send();
            return;
        }

        /** @var \App\Models\User|null $requester */
        $requester = auth()->user();
        if (! $requester instanceof User) {
            Notification::make()->danger()->title(__('filament/data_privacy_page.auth_required'))->send();
            return;
        }

        $tenant->forceFill([
            'data_export_status'       => 'pending',
            'data_export_requested_at' => Carbon::now(),
            'data_export_path'         => null,
            'data_export_expires_at'   => null,
            'data_export_size_bytes'   => null,
        ])->save();

        // GDPR Article 20 — dispatch the canonical token-cached
        // export job.  Previously a second `ExportTenantData` job
        // ran in parallel for the polling-UI status column; that
        // duplication has been folded into ExportTenantDataJob so
        // both surfaces (the in-page poll and the notification
        // download link) come from one canonical artifact.
        ExportTenantDataJob::dispatch($tenant, $requester);

        $this->logAudit($tenant, 'data.export_requested', [
            'gdpr_article' => 20,
            'requester_id' => $requester->id,
        ]);

        Notification::make()
            ->success()
            ->title(__('filament/data_privacy_page.export_requested_title'))
            ->body(__('filament/data_privacy_page.export_requested_body'))
            ->send();
    }

    public function downloadExport()
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/data_privacy_page.no_workspace_context'))->send();
            return null;
        }

        $expiresAt = $tenant->data_export_expires_at;
        if ($tenant->data_export_status !== 'ready'
            || ! $expiresAt
            || $expiresAt->isPast()
            || empty($tenant->data_export_path)
            || ! Storage::disk('local')->exists($tenant->data_export_path)
        ) {
            Notification::make()
                ->danger()
                ->title(__('filament/data_privacy_page.export_unavailable_title'))
                ->body(__('filament/data_privacy_page.export_unavailable_body'))
                ->send();
            return null;
        }

        $this->logAudit($tenant, 'tenant.data_export_downloaded');

        return Storage::disk('local')->download(
            $tenant->data_export_path,
            sprintf('%s-data-export-%s.zip',
                str($tenant->slug ?? 'workspace')->slug(),
                Carbon::now()->format('Y-m-d'),
            ),
        );
    }

    public function requestDeletion(): void
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/data_privacy_page.no_workspace_context'))->send();
            return;
        }

        /** @var \App\Models\User|null $requester */
        $requester = auth()->user();
        if (! $requester instanceof User) {
            Notification::make()->danger()->title(__('filament/data_privacy_page.auth_required'))->send();
            return;
        }

        // Only the workspace owner (or a super admin acting on the
        // tenant's behalf) may schedule deletion — staff with admin
        // role can reach this page for the data export but cannot
        // nuke a workspace they do not own.  Mirrors the equivalent
        // owner check on BillingPortal::requestWorkspaceDeletion()
        // so both entry points produce identical state.
        $isOwner       = (int) $tenant->owner_id === (int) $requester->id;
        $isSuperAdmin  = method_exists($requester, 'isSuperAdmin')
            ? $requester->isSuperAdmin()
            : (bool) ($requester->is_super_admin ?? false);

        if (! $isOwner && ! $isSuperAdmin) {
            Notification::make()
                ->danger()
                ->title(__('filament/data_privacy_page.owner_only_title'))
                ->body(__('filament/data_privacy_page.owner_only_body'))
                ->send();
            return;
        }

        // GDPR Article 17 — delegate to the TenantErasureService.
        // The service handles the 30-day cool-off, suspends the
        // workspace via subscription_status, sends the confirmation
        // email, and writes the audit log row.  Keeping all that
        // logic in one place means the BillingPortal action and
        // any future entry point produce identical state.
        app(TenantErasureService::class)->requestErasure($tenant, $requester);

        Notification::make()
            ->warning()
            ->title(__('filament/data_privacy_page.deletion_scheduled_title'))
            ->body(__('filament/data_privacy_page.deletion_scheduled_body', [
                'days' => TenantErasureService::COOL_OFF_DAYS,
            ]))
            ->persistent()
            ->send();
    }

    public function cancelDeletion(): void
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/data_privacy_page.no_workspace_context'))->send();
            return;
        }

        app(TenantErasureService::class)->cancelErasure($tenant);

        Notification::make()
            ->success()
            ->title(__('filament/data_privacy_page.deletion_cancelled_title'))
            ->body(__('filament/data_privacy_page.deletion_cancelled_body'))
            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function currentTenant(): ?Tenant
    {
        return auth()->user()?->tenant;
    }

    protected function logAudit(Tenant $tenant, string $action, array $extra = [], string $tags = 'gdpr,privacy'): void
    {
        try {
            // Auto-tag with the GDPR article when the action key
            // signals which right is being exercised — keeps the
            // taxonomy consistent across exports / erasure /
            // cancellations without callers having to remember.
            if (! str_contains($tags, 'article-')) {
                $tags .= match (true) {
                    str_starts_with($action, 'data.export_')  => ',article-20',
                    str_contains($action, 'erasure')          => ',article-17',
                    str_contains($action, 'deletion')         => ',article-17',
                    default                                   => '',
                };
            }

            AuditLog::record(
                action: $action,
                auditable: $tenant,
                oldValues: [],
                newValues: array_merge([
                    'tenant_name' => $tenant->name,
                    'by_user_id'  => auth()->id(),
                ], $extra),
                tags: $tags,
            );
        } catch (\Throwable) {
            // Audit failures never block the user-facing action.
        }
    }

    protected function humanBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
