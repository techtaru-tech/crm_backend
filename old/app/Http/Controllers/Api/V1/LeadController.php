<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\LeadResource;
use App\Http\Resources\Api\TagResource;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $query    = Lead::where('tenant_id', $tenantId)->with(['tags', 'assignedUser', 'pipelineStage']);

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('pipeline_id')) {
            $query->where('pipeline_id', $request->pipeline_id);
        }
        if ($request->filled('stage_id')) {
            $query->where('pipeline_stage_id', $request->stage_id);
        }
        if ($request->filled('assigned_user_id')) {
            $query->where('assigned_user_id', $request->assigned_user_id);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('name', $request->tag));
        }
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn ($q) => $q
                ->where('first_name', 'like', $s)
                ->orWhere('last_name', 'like', $s)
                ->orWhere('email', 'like', $s)
                ->orWhere('phone', 'like', $s));
        }
        if ($request->filled('created_after')) {
            $query->where('created_at', '>=', $request->created_after);
        }
        if ($request->filled('created_before')) {
            $query->where('created_at', '<=', $request->created_before);
        }
        if ($request->boolean('starred')) {
            $query->where('is_starred', true);
        }

        $allowed   = ['created_at', 'updated_at', 'lead_score', 'first_name', 'last_name', 'email'];
        $sort      = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'created_at';
        $dir       = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $perPage   = min((int) $request->input('per_page', 15), 100);

        $query->orderBy($sort, $dir);
        $paginator = $query->paginate($perPage);

        return response()->json(ApiResource::paginatedResponse($paginator, LeadResource::class));
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');

        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'nullable|string|max:100',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:50',
            'source'            => 'nullable|string|max:80',
            'status'            => 'nullable|string|max:50',
            'pipeline_id'       => ['nullable', $this->existsForTenant('pipelines', $tenantId)],
            'pipeline_stage_id' => ['nullable', $this->existsForTenant('pipeline_stages', $tenantId)],
            'assigned_user_id'  => ['nullable', $this->userBelongsToTenant($tenantId)],
            'notes'             => 'nullable|string',
            'custom_fields'     => 'nullable|array',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|max:80',
        ]);

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $lead = DB::transaction(function () use ($validated, $tags, $tenantId) {
            $lead = Lead::create(array_merge($validated, [
                'tenant_id' => $tenantId,
                'source'    => $validated['source'] ?? 'api',
            ]));

            if (! empty($tags)) {
                $tagIds = [];
                foreach ($tags as $tagName) {
                    $tag      = Tag::firstOrCreate(['tenant_id' => $tenantId, 'name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $lead->tags()->sync($tagIds);
            }

            return $lead;
        });

        $lead->load(['tags', 'assignedUser', 'pipelineStage']);

        return response()->json(['data' => new LeadResource($lead)], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $lead     = Lead::where('tenant_id', $tenantId)
            ->with(['tags', 'assignedUser', 'pipelineStage'])
            ->findOrFail($id);

        return response()->json(['data' => new LeadResource($lead)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $lead     = Lead::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'first_name'        => 'sometimes|string|max:100',
            'last_name'         => 'sometimes|nullable|string|max:100',
            'email'             => 'sometimes|nullable|email|max:255',
            'phone'             => 'sometimes|nullable|string|max:50',
            'source'            => 'sometimes|string|max:80',
            'status'            => 'sometimes|string|max:50',
            'pipeline_id'       => ['sometimes', 'nullable', $this->existsForTenant('pipelines', $tenantId)],
            'pipeline_stage_id' => ['sometimes', 'nullable', $this->existsForTenant('pipeline_stages', $tenantId)],
            'assigned_user_id'  => ['sometimes', 'nullable', $this->userBelongsToTenant($tenantId)],
            'is_starred'        => 'sometimes|boolean',
            'notes'             => 'sometimes|nullable|string',
            'custom_fields'     => 'sometimes|nullable|array',
            'tags'              => 'sometimes|nullable|array',
            'tags.*'            => 'string|max:80',
        ]);

        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        DB::transaction(function () use ($lead, $validated, $tags, $tenantId) {
            $lead->update($validated);

            if ($tags !== null) {
                $tagIds = [];
                foreach ($tags as $tagName) {
                    $tag      = Tag::firstOrCreate(['tenant_id' => $tenantId, 'name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $lead->tags()->sync($tagIds);
            }
        });

        $lead->load(['tags', 'assignedUser', 'pipelineStage']);

        return response()->json(['data' => new LeadResource($lead)]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $lead     = Lead::where('tenant_id', $tenantId)->findOrFail($id);
        $lead->delete();

        return response()->json(['message' => 'Lead deleted.']);
    }

    public function attachTag(Request $request, int $leadId): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $lead     = Lead::where('tenant_id', $tenantId)->findOrFail($leadId);
        $data     = $request->validate(['tags' => 'required|array', 'tags.*' => 'string|max:80']);

        $attached = [];
        foreach ($data['tags'] as $tagName) {
            $tag = Tag::firstOrCreate(
                ['tenant_id' => $tenantId, 'name' => trim($tagName)],
            );
            $lead->tags()->syncWithoutDetaching([$tag->id]);
            $attached[] = new TagResource($tag);
        }

        return response()->json(['data' => $attached, 'message' => count($attached) . ' tag(s) attached.']);
    }

    public function detachTag(Request $request, int $leadId, string $tagIdentifier): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $lead     = Lead::where('tenant_id', $tenantId)->findOrFail($leadId);

        if (is_numeric($tagIdentifier)) {
            $tag = Tag::where('tenant_id', $tenantId)->find((int) $tagIdentifier);
        } else {
            $tag = Tag::where('tenant_id', $tenantId)->where('name', $tagIdentifier)->first();
        }

        if (! $tag) {
            return response()->json(['message' => 'Tag not found.'], 404);
        }

        $lead->tags()->detach($tag->id);

        return response()->json(['message' => 'Tag removed.']);
    }

    /**
     * Returns a validation closure that checks the record exists AND
     * belongs to the current tenant — prevents cross-tenant reference injection.
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

    /**
     * Returns a validation closure that checks a user belongs to the current tenant.
     */
    private function userBelongsToTenant(int $tenantId): \Closure
    {
        return function ($attribute, $value, $fail) use ($tenantId) {
            if ($value === null) {
                return;
            }
            $exists = DB::table('tenant_users')
                ->where('user_id', $value)
                ->where('tenant_id', $tenantId)
                ->exists();

            if (! $exists) {
                $fail("The selected {$attribute} does not belong to your workspace.");
            }
        };
    }
}
