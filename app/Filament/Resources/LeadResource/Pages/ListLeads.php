<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use App\Models\SavedFilter;
use App\Models\Tenant;
use App\Services\Migration\CrmCsvImporter;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kanban')
                ->label(__('filament/leads.kanban_board'))
                ->icon('heroicon-o-squares-2x2')
                ->url(fn() => route('filament.admin.pages.kanban-board'))
                ->color('gray'),

            \Filament\Actions\Action::make('save_filters')
                ->label(__('filament/leads.save_filters'))
                ->icon('heroicon-o-bookmark')
                ->color('gray')
                ->form([
                    TextInput::make('name')
                        ->label(__('filament/leads.view_name'))
                        ->placeholder(__('filament/leads.view_name_placeholder'))
                        ->required()
                        ->maxLength(100),
                    \Filament\Forms\Components\Toggle::make('email_alerts')
                        ->label(__('filament/leads.email_alerts'))
                        ->helperText(__('filament/leads.email_alerts_help'))
                        ->default(false),
                    \Filament\Forms\Components\Toggle::make('is_team_shared')
                        ->label(__('filament/leads.share_with_team'))
                        ->helperText(__('filament/leads.share_with_team_help'))
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $filterState = $this->tableFilters ?? [];

                    SavedFilter::create([
                        'user_id'        => auth()->id(),
                        'resource'       => LeadResource::class,
                        'name'           => $data['name'],
                        'filters'        => $filterState,
                        'email_alerts'   => (bool) ($data['email_alerts'] ?? false),
                        'is_team_shared' => (bool) ($data['is_team_shared'] ?? false),
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('filament/leads.filter_view_saved'))
                        ->body(__('filament/leads.filter_view_saved_as', ['name' => $data['name']]))
                        ->send();
                }),

            \Filament\Actions\Action::make('load_filters')
                ->label(__('filament/leads.saved_views'))
                ->icon('heroicon-o-bookmark-square')
                ->color('gray')
                ->form(function () {
                    $views = SavedFilter::visibleTo(auth()->user())
                        ->forResource(LeadResource::class)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();

                    if (empty($views)) {
                        return [
                            \Filament\Forms\Components\Placeholder::make('empty')
                                ->label(__('filament/leads.placeholder_empty_label'))
                                ->content(__('filament/leads.no_saved_views_yet')),
                        ];
                    }

                    return [
                        \Filament\Forms\Components\Select::make('saved_filter_id')
                            ->label(__('filament/leads.select_saved_view'))
                            ->options($views)
                            ->required(),
                    ];
                })
                ->action(function (array $data) {
                    if (empty($data['saved_filter_id'])) {
                        return;
                    }

                    $savedFilter = SavedFilter::visibleTo(auth()->user())
                        ->forResource(LeadResource::class)
                        ->find($data['saved_filter_id']);

                    if (! $savedFilter) {
                        Notification::make()
                            ->danger()
                            ->title(__('filament/leads.saved_view_not_found'))
                            ->send();
                        return;
                    }

                    $this->tableFilters = $savedFilter->filters;
                    $this->updatedTableFilters();

                    Notification::make()
                        ->success()
                        ->title(__('filament/leads.filter_view_loaded', ['name' => $savedFilter->name]))
                        ->send();
                }),

            \Filament\Actions\Action::make('delete_saved_filter')
                ->label(__('filament/leads.delete_view'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->form(function () {
                    $views = SavedFilter::where('user_id', auth()->id())
                        ->forResource(LeadResource::class)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();

                    if (empty($views)) {
                        return [
                            \Filament\Forms\Components\Placeholder::make('empty')
                                ->label(__('filament/leads.placeholder_empty_label'))
                                ->content(__('filament/leads.no_saved_views_to_delete')),
                        ];
                    }

                    return [
                        \Filament\Forms\Components\Select::make('saved_filter_id')
                            ->label(__('filament/leads.select_view_to_delete'))
                            ->options($views)
                            ->required(),
                    ];
                })
                ->action(function (array $data) {
                    if (empty($data['saved_filter_id'])) {
                        return;
                    }

                    $deleted = SavedFilter::where('user_id', auth()->id())
                        ->forResource(LeadResource::class)
                        ->where('id', $data['saved_filter_id'])
                        ->delete();

                    if ($deleted) {
                        Notification::make()
                            ->success()
                            ->title(__('filament/leads.saved_view_deleted'))
                            ->send();
                    }
                })
                ->requiresConfirmation(),

            \Filament\Actions\Action::make('export')
                ->label(__('filament/leads.export_current_filters'))
                ->icon('heroicon-o-arrow-down-tray')
                // Spec §7 — same gate as the bulk export on the resource.
                ->visible(fn () => LeadResource::canExport())
                ->action(function () {
                    $tenantId = \App\Support\TenantContext::currentId();

                    $activeFilters = [];
                    $tableFilters  = $this->tableFilters ?? [];

                    if (! empty($tableFilters['source']['value'])) {
                        $activeFilters['source'] = $tableFilters['source']['value'];
                    }
                    if (! empty($tableFilters['status']['value'])) {
                        $activeFilters['status'] = $tableFilters['status']['value'];
                    }
                    if (! empty($tableFilters['assigned_user_id']['value'])) {
                        $activeFilters['assigned_user_id'] = $tableFilters['assigned_user_id']['value'];
                    }
                    if (! empty($tableFilters['pipeline_id']['value'])) {
                        $activeFilters['pipeline_id'] = $tableFilters['pipeline_id']['value'];
                    }
                    if (! empty($tableFilters['pipeline_stage_id']['value'])) {
                        $activeFilters['pipeline_stage_id'] = $tableFilters['pipeline_stage_id']['value'];
                    }
                    if (! empty($tableFilters['is_starred']['isActive'])) {
                        $activeFilters['is_starred'] = true;
                    }
                    if (! empty($tableFilters['created_from']['date_from'])) {
                        $activeFilters['created_from'] = $tableFilters['created_from']['date_from'];
                    }
                    if (! empty($tableFilters['created_until']['date_until'])) {
                        $activeFilters['created_until'] = $tableFilters['created_until']['date_until'];
                    }
                    if (! empty($tableFilters['score_min']['score_min'])) {
                        $activeFilters['score_min'] = $tableFilters['score_min']['score_min'];
                    }
                    if (! empty($tableFilters['tags']['values'])) {
                        $activeFilters['tags'] = $tableFilters['tags']['values'];
                    }
                    if (! empty($tableFilters['is_duplicate']['isActive'])) {
                        $activeFilters['is_duplicate'] = true;
                    }

                    // headingsOnly: column template only — set to false for a full data export.
                    LeadResource::recordExportAudit($activeFilters, $this->getFilteredTableQuery()->count());

                    return \App\Exports\LeadsExport::downloadResponse(
                        $tenantId,
                        $activeFilters,
                        headingsOnly: true,
                        forUserId: auth()->id(),
                    );
                }),

            \Filament\Actions\Action::make('import_csv')
                ->label(__('filament/leads.import_from_crm'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__('filament/leads.import_modal_heading'))
                ->modalDescription(__('filament/leads.import_modal_description'))
                ->form([
                    Select::make('vendor')
                        ->label(__('filament/leads.source_crm'))
                        ->options(array_merge(
                            ['' => __('filament/leads.auto_detect_option')],
                            CrmCsvImporter::labels(),
                        ))
                        ->default('')
                        ->helperText(__('filament/leads.source_crm_help')),

                    FileUpload::make('csv_file')
                        ->label(__('filament/leads.csv_file'))
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                        ->maxSize(20480) // 20MB
                        ->storeFiles(false)
                        ->helperText(__('filament/leads.csv_file_help')),
                ])
                ->action(function (array $data) {
                    $tenantId = \App\Support\TenantContext::currentId();
                    $tenant   = $tenantId ? Tenant::find($tenantId) : null;

                    if (! $tenant) {
                        Notification::make()->danger()->title(__('filament/leads.no_workspace_context'))->send();
                        return;
                    }

                    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $file */
                    $file = $data['csv_file'] ?? null;
                    if (! $file) {
                        Notification::make()->danger()->title(__('filament/leads.no_file_uploaded'))->send();
                        return;
                    }

                    $vendor = $data['vendor'] ?: null;

                    $result = app(CrmCsvImporter::class)->import(
                        $file->getRealPath(),
                        $tenant,
                        $vendor,
                    );

                    $vendorLabel = CrmCsvImporter::labels()[$result['vendor']] ?? $result['vendor'];
                    $body = __('filament/leads.import_body_imported_count', [
                        'count'  => $result['imported'],
                        'vendor' => $vendorLabel,
                    ]);
                    // Duplicates before skips: re-importing a file the tenant
                    // already loaded is by far the most common reason for a
                    // zero count, and used to be reported as nothing at all.
                    if (($result['duplicates'] ?? 0) > 0) {
                        $body .= ' ' . __('filament/leads.import_body_duplicate_count', [
                            'count' => $result['duplicates'],
                        ]);
                    }
                    if ($result['skipped'] > 0) {
                        $body .= ' ' . __('filament/leads.import_body_skipped_count', [
                            'count' => $result['skipped'],
                        ]);
                    }
                    if (! empty($result['errors'])) {
                        $body .= ' ' . __('filament/leads.import_body_batch_errors', [
                            'count' => count($result['errors']),
                        ]);
                    }

                    // A run that imported nothing is not a success — it
                    // rendered green with "0 leads imported" and looked fine.
                    Notification::make()
                        ->status($result['imported'] > 0 ? 'success' : 'warning')
                        ->title(__('filament/leads.csv_import_complete'))
                        ->body($body)
                        ->persistent()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
