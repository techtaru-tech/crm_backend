<?php

namespace App\Filament\Resources\LeadImportResource\Pages;

use App\Filament\Resources\LeadImportResource;
use App\Jobs\ProcessLeadImport;
use App\Models\LeadImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CreateLeadImport extends Page
{
    protected static string $resource = LeadImportResource::class;

    public function getView(): string
    {
        return 'filament.resources.lead-import.create';
    }

    public ?array $data = [];
    public string $step = 'upload';
    public ?string $uploadedPath = null;
    public ?string $originalFilename = null;
    public array $headers = [];
    public array $previewRows = [];
    public int $totalRows = 0;
    public array $fieldOptions = [];
    public array $columnMapping = [];
    public ?int $importId = null;

    public function mount(): void
    {
        // Localized field options for the column-mapping UI. Stored on the
        // instance so the Livewire template can render translated labels
        // without re-translating on every getMappingSummary() call.
        $this->fieldOptions = [
            'first_name'  => __('filament/lead_imports.field_first_name'),
            'last_name'   => __('filament/lead_imports.field_last_name'),
            'email'       => __('filament/lead_imports.field_email'),
            'phone'       => __('filament/lead_imports.field_phone'),
            'source'      => __('filament/lead_imports.field_source'),
            'status'      => __('filament/lead_imports.field_status'),
            'notes'       => __('filament/lead_imports.field_notes'),
        ];

        // Append this tenant's custom Lead fields so CSV columns can map to
        // them too. Keyed "custom_fields.<key>" so ProcessLeadImport routes
        // the value into the custom_fields JSON column instead of a real
        // attribute. The tenant's own field name is the option label.
        foreach (\App\Models\CustomFieldDefinition::getForTenant(\App\Models\CustomFieldDefinition::ENTITY_LEAD) as $def) {
            $this->fieldOptions['custom_fields.' . $def->key] = $def->name;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('file')
                ->label(__('filament/lead_imports.csv_or_excel'))
                ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                ->disk('local')
                // Tenant-prefix the import path so two tenants can't
                // overwrite each other's uploads on a hash collision
                // and so a forensic / GDPR purge can walk
                // imports/{tid}/ to find every CSV ever uploaded.
                ->directory(fn () => 'imports/' . (auth()->user()?->tenant_id ?? 'unscoped'))
                ->required(),
        ])->statePath('data');
    }

    public function uploadFile(): void
    {
        $this->validate(['data.file' => 'required']);

        // Persist the upload and read its FINAL stored path.  Reading
        // $this->data['file'] raw only ever yields Livewire's *temporary*
        // upload state (an array keyed by an upload id whose value is a
        // TemporaryUploadedFile / temp ref) — it is never moved to the
        // configured 'local' disk and is not a usable Storage path.  That is
        // why a completed upload still failed here with "no file uploaded"
        // (and, before that, a TypeError assigning an array to ?string).
        // $this->form->getState() runs Filament's saveUploadedFiles(), moves
        // the file into storage/app/private/imports/{tenant}/ and returns the
        // permanent path string — the same pattern the Updates page uses.
        $file = $this->form->getState()['file'] ?? null;
        if (is_array($file)) {
            // Defensive: a multi-file FileUpload would hand back an array.
            $file = reset($file);
        }
        if (! is_string($file) || $file === '') {
            Notification::make()
                ->danger()
                ->title(__('filament/lead_imports.notif_read_failed_prefix') . 'no file uploaded')
                ->send();
            return;
        }

        $this->uploadedPath = $file;
        $this->originalFilename = basename($file);

        try {
            // Read from the SAME disk the FileUpload wrote to ('local'), not
            // the default disk — a buyer who points FILESYSTEM_DISK elsewhere
            // would otherwise have the upload land locally but this read miss.
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \Maatwebsite\Excel\HeadingRowImport, Storage::disk('local')->path($file))[0] ?? [];
            $this->headers = array_keys($rows[0] ?? []);
            foreach ($this->headers as $h) {
                // Auto-map when CSV column name matches a known field key (case-insensitive)
                $normalized = strtolower(str_replace([' ', '-'], '_', (string) $h));
                $this->columnMapping[$h] = array_key_exists($normalized, $this->fieldOptions) ? $normalized : null;
            }

            // Load first 10 rows for the preview step (data only, keyed by header)
            $this->totalRows   = count($rows);
            $this->previewRows = array_slice($rows, 0, 10);

            $this->step = 'mapping';
        } catch (\Throwable $e) {
            Notification::make()->danger()->title(__('filament/lead_imports.notif_read_failed_prefix') . $e->getMessage())->send();
        }
    }

    /**
     * Return the list of recognized (mapped) and unmapped CSV columns for
     * the preview UI.
     */
    public function getMappingSummary(): array
    {
        $recognized = [];
        $unmapped   = [];
        foreach ($this->columnMapping as $csvCol => $target) {
            if ($target) {
                $recognized[] = ['csv' => $csvCol, 'field' => $this->fieldOptions[$target] ?? $target];
            } else {
                $unmapped[] = $csvCol;
            }
        }
        return ['recognized' => $recognized, 'unmapped' => $unmapped];
    }

    public function confirmImport(): void
    {
        $tenantId = \App\Support\TenantContext::currentId();

        $import = LeadImport::create([
            'tenant_id'         => $tenantId,
            'user_id'           => auth()->id(),
            'file_path'         => $this->uploadedPath,
            'original_filename' => $this->originalFilename,
            'column_mapping'    => $this->columnMapping,
            'status'            => 'pending',
        ]);

        ProcessLeadImport::dispatch($import->id, $import->tenant_id);

        $this->importId = $import->id;
        $this->step     = 'done';

        Notification::make()->success()->title(__('filament/lead_imports.notif_queued_title'))->send();
    }
}
