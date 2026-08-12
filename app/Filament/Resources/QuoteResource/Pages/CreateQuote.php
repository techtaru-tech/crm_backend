<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    /**
     * Fill initial form state from query params (e.g. ?lead_id=123 — set
     * by the "Create Quote" header action on the lead view).
     */
    protected function fillForm(): void
    {
        parent::fillForm();

        if ($leadId = request()->query('lead_id')) {
            $this->form->fill([
                'lead_id' => (int) $leadId,
                'title'   => __('filament/quotes.new_quote_default_title'),
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id']  = \App\Support\TenantContext::currentId();
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
