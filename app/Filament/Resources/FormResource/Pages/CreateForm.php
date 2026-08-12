<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\FormResource;
use App\Models\FormField;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    // After "Create" (NOT "Create & another"): return to the forms list
    // instead of landing on the Edit page. Lets the operator confirm
    // the new form exists in their catalogue before drilling in.
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }

    protected function afterCreate(): void
    {
        $form = $this->record;
        $hasGdpr = $form->fields()->where('type', 'gdpr')->exists();
        if (! $hasGdpr) {
            FormField::create([
                'form_id'      => $form->id,
                'type'         => 'gdpr',
                // Translator-first with literal English fallback. Same
                // key as the EditForm fallback so a single lang entry
                // controls both create and edit paths.
                'label'        => (function (): string {
                    $key   = 'filament/forms.gdpr_default_field_label';
                    $trans = __($key);
                    return is_string($trans) && $trans !== $key
                        ? $trans
                        : 'I agree to the processing of my personal data in accordance with the Privacy Policy.';
                })(),
                'required'     => true,
                'locked'       => true,
                'sort_order'   => 9999,
                'step_number'  => $form->fields()->max('step_number') ?? 1,
            ]);
        }
    }
}
