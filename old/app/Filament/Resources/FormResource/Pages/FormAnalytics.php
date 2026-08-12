<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\FormResource;
use App\Models\Form;
use App\Models\FormSubmission;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class FormAnalytics extends Page
{
    // InteractsWithRecord plugs this page into Filament's standard
    // record-resolution pipeline. Before this change, the custom
    // mount() signature + public Form $record property + a manual
    // findOrFail() combo caused route-binding to sometimes 404 when
    // the tenant scope resolved differently between the URL binding
    // and the mount call. The trait handles it uniformly.
    use InteractsWithRecord;

    protected static string $resource = FormResource::class;

    public function getView(): string
    {
        return 'filament.resources.forms.analytics';
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Defence-in-depth tenant check on top of BelongsToTenant's
        // global scope: the URL param must belong to the authenticated
        // tenant even if Filament's binding somehow escapes the scope.
        $tenantId = \App\Support\TenantContext::currentId();
        abort_unless((int) $this->record->tenant_id === (int) $tenantId, 404);

        $this->record->load('fields');
    }

    protected function getViewData(): array
    {
        $form     = $this->record;
        $tenantId = \App\Support\TenantContext::currentId();

        $totalSubmissions = FormSubmission::where('form_id', $form->id)
            ->where('tenant_id', $tenantId)
            ->where('is_spam', false)
            ->count();

        $submissionsOverTime = FormSubmission::where('form_id', $form->id)
            ->where('tenant_id', $tenantId)
            ->where('is_spam', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $fieldCompletion = [];
        foreach ($form->fields->where('type', '!=', 'divider') as $field) {
            $filled = $field->submissionValues()
                ->whereHas('submission', fn($q) => $q->where('is_spam', false))
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->count();
            $fieldCompletion[$field->label] = $totalSubmissions > 0
                ? round(($filled / $totalSubmissions) * 100)
                : 0;
        }

        $stepDropoff = [];
        if ($form->multi_step) {
            $maxStep = $form->fields->max('step_number') ?? 1;
            for ($step = 1; $step <= $maxStep; $step++) {
                $stepDropoff[$step] = FormSubmission::where('form_id', $form->id)
                    ->where('tenant_id', $tenantId)
                    ->where('completed_step', '>=', $step)
                    ->where('is_spam', false)
                    ->count();
            }
        }

        return [
            'form'                => $form,
            'totalSubmissions'    => $totalSubmissions,
            'submissionsOverTime' => $submissionsOverTime,
            'fieldCompletion'     => $fieldCompletion,
            'stepDropoff'         => $stepDropoff,
        ];
    }
}
