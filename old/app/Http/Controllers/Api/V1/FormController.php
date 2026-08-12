<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\FormResource;
use App\Jobs\DispatchOutboundWebhook;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\FormSubmissionValue;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $perPage   = min((int) $request->input('per_page', 15), 100);
        $paginator = Form::where('tenant_id', $tenantId)->paginate($perPage);

        return response()->json(ApiResource::paginatedResponse($paginator, FormResource::class));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $form     = Form::where('tenant_id', $tenantId)->with('fields')->findOrFail($id);

        return response()->json(['data' => new FormResource($form)]);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');

        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'title'             => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'submit_label'      => 'nullable|string|max:100',
            'thank_you_message' => 'nullable|string',
            'redirect_url'      => 'nullable|url',
            'pipeline_id'       => ['nullable', 'integer', $this->existsForTenant('pipelines', $tenantId)],
            'pipeline_stage_id' => ['nullable', 'integer', $this->existsForTenant('pipeline_stages', $tenantId)],
            'active'            => 'boolean',
            'multi_step'        => 'boolean',
        ]);

        $form = Form::create(array_merge($data, ['tenant_id' => $tenantId]));

        return response()->json(['data' => new FormResource($form), 'message' => 'Form created.'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $form     = Form::where('tenant_id', $tenantId)->findOrFail($id);

        $data = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'title'             => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'submit_label'      => 'nullable|string|max:100',
            'thank_you_message' => 'nullable|string',
            'redirect_url'      => 'nullable|url',
            'pipeline_id'       => ['nullable', 'integer', $this->existsForTenant('pipelines', $tenantId)],
            'pipeline_stage_id' => ['nullable', 'integer', $this->existsForTenant('pipeline_stages', $tenantId)],
            'active'            => 'boolean',
            'multi_step'        => 'boolean',
        ]);

        $form->update($data);

        return response()->json(['data' => new FormResource($form->load('fields')), 'message' => 'Form updated.']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $form     = Form::where('tenant_id', $tenantId)->findOrFail($id);
        $form->delete();

        return response()->json(['message' => 'Form deleted.']);
    }

    public function submit(Request $request, int $formId): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $form     = Form::where('tenant_id', $tenantId)->with('fields')->findOrFail($formId);

        if (! $form->active) {
            return response()->json(['message' => 'This form is not accepting submissions.'], 422);
        }

        $data = $request->validate(['fields' => 'required|array']);

        $submission = FormSubmission::create([
            'tenant_id'  => $tenantId,
            'form_id'    => $form->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        foreach ($data['fields'] as $fieldKey => $value) {
            $field = $form->fields->firstWhere('name', $fieldKey) ?? $form->fields->firstWhere('id', $fieldKey);
            if ($field) {
                FormSubmissionValue::create([
                    'form_submission_id' => $submission->id,
                    'form_field_id'      => $field->id,
                    'value'              => is_array($value) ? json_encode($value) : (string) $value,
                ]);
            }
        }

        $fields    = $data['fields'];
        $firstName = $fields['first_name'] ?? $fields['firstName'] ?? null;
        $email     = $fields['email'] ?? null;
        $phone     = $fields['phone'] ?? null;

        if ($firstName || $email) {
            $lead = Lead::create([
                'tenant_id'         => $tenantId,
                'form_id'           => $form->id,
                'pipeline_id'       => $form->pipeline_id,
                'pipeline_stage_id' => $form->pipeline_stage_id,
                'first_name'        => $firstName,
                'email'             => $email,
                'phone'             => $phone,
                'source'            => 'form_api',
            ]);
            $submission->update(['lead_id' => $lead->id]);
        }

        DispatchOutboundWebhook::fireForEvent($tenantId, 'form.submitted', [
            'submission_id' => $submission->id,
            'form_id'       => $form->id,
            'form_name'     => $form->name,
            'lead_id'       => $submission->lead_id,
            'fields'        => $data['fields'],
            'ip_address'    => $request->ip(),
            'submitted_at'  => $submission->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'data'    => ['submission_id' => $submission->id],
            'message' => $form->thank_you_message ?? (string) __('public_form.api_submitted_default'),
        ], 201);
    }

    /**
     * Validation closure that checks a record exists AND belongs to the current
     * tenant, preventing cross-tenant reference injection via the API.
     */
    private function existsForTenant(string $table, int $tenantId): \Closure
    {
        return function ($attribute, $value, $fail) use ($table, $tenantId) {
            if ($value === null) {
                return;
            }

            $exists = DB::table($table)
                ->where('id', $value)
                ->where('tenant_id', $tenantId)
                ->whereNull('deleted_at')
                ->exists();

            if (! $exists) {
                $fail("The selected {$attribute} does not exist or does not belong to your workspace.");
            }
        };
    }
}
