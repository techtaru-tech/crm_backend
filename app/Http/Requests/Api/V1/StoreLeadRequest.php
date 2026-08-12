<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\BaseFormRequest;

/**
 * Validation rules for `POST /api/v1/leads`.
 *
 * Demonstrates the canonical FormRequest pattern: rules live here,
 * the controller method just type-hints the request and reads from
 * `$request->validated()`.  When more controllers (PublicFormController,
 * LeadResource form, LeadIngestionService) migrate to share these
 * rules, this class becomes the single source of truth — moving the
 * same fields between Filament forms, public-facing intake, and the
 * REST API stops drifting.
 */
final class StoreLeadRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'        => ['nullable', 'string', 'max:120'],
            'last_name'         => ['nullable', 'string', 'max:120'],
            'email'             => ['nullable', 'email:rfc,dns', 'max:200'],
            'phone'             => ['nullable', 'string', 'max:60'],
            'company'           => ['nullable', 'string', 'max:200'],
            'source'            => ['nullable', 'string', 'max:80'],
            'status'            => ['nullable', 'string', 'max:40'],
            'pipeline_id'       => ['nullable', 'integer', 'min:1'],
            'pipeline_stage_id' => ['nullable', 'integer', 'min:1'],
            'assigned_user_id'  => ['nullable', 'integer', 'min:1'],
            'notes'             => ['nullable', 'string', 'max:10000'],
            'tags'              => ['nullable', 'array'],
            'tags.*'            => ['string', 'max:60'],
        ];
    }

    /**
     * Normalise the email to lowercase before validation runs.  This
     * is the bug LeadIngestionService had patched in isolation —
     * lifting it here means every consumer of the rules gets the same
     * normalisation without re-implementing it.
     */
    protected function prepareForValidation(): void
    {
        $email = $this->input('email');
        if (is_string($email) && $email !== '') {
            $this->merge(['email' => strtolower(trim($email))]);
        }
    }
}
