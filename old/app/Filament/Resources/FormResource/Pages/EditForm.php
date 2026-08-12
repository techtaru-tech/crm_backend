<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\FormResource;
use App\Models\FormField;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('preview_form')
                ->label(__('filament/forms.live_preview'))
                ->icon('heroicon-o-eye')
                ->url(fn() => $this->record->public_url)
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('analytics')
                ->label(__('filament/forms.analytics'))
                ->icon('heroicon-o-chart-bar')
                ->url(fn() => FormResource::getUrl('analytics', ['record' => $this->record])),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $form = $this->record;
        $gdprField = $form->fields()->where('type', 'gdpr')->first();
        if ($gdprField) {
            $gdprField->update(['sort_order' => 9999, 'locked' => true]);
        } else {
            FormField::create([
                'form_id'     => $form->id,
                'type'        => 'gdpr',
                // Translator-first with literal English fallback so the
                // consent sentence on the rendered form stays meaningful
                // even if the lang entry is missing. The column header
                // "GDPR Consent" is a separate key
                // (field_gdpr_consent_default_label).
                'label'       => (function (): string {
                    $key   = 'filament/forms.gdpr_default_field_label';
                    $trans = __($key);
                    return is_string($trans) && $trans !== $key
                        ? $trans
                        : 'I agree to the processing of my personal data in accordance with the Privacy Policy.';
                })(),
                'required'    => true,
                'locked'      => true,
                'sort_order'  => 9999,
                'step_number' => $form->fields()->where('type', '!=', 'gdpr')->max('step_number') ?? 1,
            ]);
        }
    }
}
