<?php

namespace App\Filament\Resources\CompanyImportResource\Pages;

use App\Filament\Resources\CompanyImportResource;
use App\Jobs\ProcessCompanyImport;
use App\Models\CompanyImport;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class CreateCompanyImport extends Page
{
    protected static string $resource = CompanyImportResource::class;

    public function getView(): string
    {
        return 'filament.resources.company-import.create';
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
        // Importable Company fields (matches CompanyResource's form columns).
        $this->fieldOptions = [
            'name'     => __('filament/company_imports.field_name'),
            'domain'   => __('filament/company_imports.field_domain'),
            'industry' => __('filament/company_imports.field_industry'),
            'size'     => __('filament/company_imports.field_size'),
            'website'  => __('filament/company_imports.field_website'),
            'phone'    => __('filament/company_imports.field_phone'),
            'address'  => __('filament/company_imports.field_address'),
            'city'     => __('filament/company_imports.field_city'),
            'country'  => __('filament/company_imports.field_country'),
            'notes'    => __('filament/company_imports.field_notes'),
        ];

        // Append this tenant's custom Company fields so CSV columns can map to
        // them too. Keyed "custom_fields.<key>" so ProcessCompanyImport routes
        // the value into the custom_fields JSON column instead of a real
        // attribute. The tenant's own field name is the option label.
        foreach (\App\Models\CustomFieldDefinition::getForTenant(\App\Models\CustomFieldDefinition::ENTITY_COMPANY) as $def) {
            $this->fieldOptions['custom_fields.' . $def->key] = $def->name;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('file')
                ->label(__('filament/company_imports.csv_or_excel'))
                ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                ->disk('local')
                ->directory(fn () => 'imports/' . (auth()->user()?->tenant_id ?? 'unscoped'))
                ->required(),
        ])->statePath('data');
    }

    public function uploadFile(): void
    {
        $this->validate(['data.file' => 'required']);

        // $this->form->getState() runs saveUploadedFiles(), moving the upload
        // to the 'local' disk and returning the permanent path string (reading
        // $this->data['file'] raw only yields Livewire's temporary state).
        $file = $this->form->getState()['file'] ?? null;
        if (is_array($file)) {
            $file = reset($file);
        }
        if (! is_string($file) || $file === '') {
            Notification::make()
                ->danger()
                ->title(__('filament/company_imports.notif_read_failed_prefix') . 'no file uploaded')
                ->send();
            return;
        }

        $this->uploadedPath = $file;
        $this->originalFilename = basename($file);

        try {
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \Maatwebsite\Excel\HeadingRowImport, Storage::disk('local')->path($file))[0] ?? [];
            $this->headers = array_keys($rows[0] ?? []);
            foreach ($this->headers as $h) {
                // Auto-map when the CSV column name matches a known field key.
                $normalized = strtolower(str_replace([' ', '-'], '_', (string) $h));
                $this->columnMapping[$h] = array_key_exists($normalized, $this->fieldOptions) ? $normalized : null;
            }

            $this->totalRows   = count($rows);
            $this->previewRows = array_slice($rows, 0, 10);

            $this->step = 'mapping';
        } catch (\Throwable $e) {
            Notification::make()->danger()->title(__('filament/company_imports.notif_read_failed_prefix') . $e->getMessage())->send();
        }
    }

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

        $import = CompanyImport::create([
            'tenant_id'         => $tenantId,
            'user_id'           => auth()->id(),
            'file_path'         => $this->uploadedPath,
            'original_filename' => $this->originalFilename,
            'column_mapping'    => $this->columnMapping,
            'status'            => 'pending',
        ]);

        ProcessCompanyImport::dispatch($import->id, $import->tenant_id);

        $this->importId = $import->id;
        $this->step     = 'done';

        Notification::make()->success()->title(__('filament/company_imports.notif_queued_title'))->send();
    }
}
